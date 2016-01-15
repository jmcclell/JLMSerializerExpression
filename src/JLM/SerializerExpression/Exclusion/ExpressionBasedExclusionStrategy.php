<?php

namespace JLM\SerializerExpression\Exclusion;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface as SerializerEventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use JMS\Serializer\Exclusion\ExclusionStrategyInterface;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata as BasePropertyMetadata;
use JMS\Serializer\Context;

use Metadata\ClassHierarchyMetadata;
use Metadata\MetadataFactory;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\Expression;

/**
 * Exclusion strategy based on Symfony expression langauge component
 *
 * @author Jason McClellan <jason_mcclellan@jasonmcclellan.io>
 */
class ExpressionBasedExclusionStrategy implements ExclusionStrategyInterface, SerializerEventSubscriberInterface
{
    protected $metadataFactory;
    protected $expressionLanguage;

    protected $currentObject;

    public function __construct(MetadataFactory $metadataFactory, ExpressionLanguage $expressionLanguage)
    {
        $this->metadataFactory = $metadataFactory;
        $this->expressionLanguage = $expressionLanguage;
    }

    /**
     * Whether the class should be skipped.
     *
     * This class only supports property level exclusion
     *
     * @param ClassMetadata $metadata The JMS Serializer metadata for the class
     *
     * @return boolean
     */
    public function shouldSkipClass(ClassMetadata $class, Context $context)
    {
        return false;
    }

    /**
     * Whether the property should be skipped.
     *
     * @param BasePropertyMetadata $property
     * @param Context $context
     *
     * @return boolean
     */
    public function shouldSkipProperty(BasePropertyMetadata $property, Context $context)
    {
        if (null !== $property->class) {
            $classMetadata = $this->metadataFactory->getMetadataForClass($property->class);
            $classMetadata = $classMetadata->classMetadata[$property->class];

            if (isset($classMetadata->propertyMetadata[$property->name])) {
                $propertyMetadata = $classMetadata->propertyMetadata[$property->name];
                if (null !== $propertyMetadata->exclusionExpression) {
                    $expression = new Expression($propertyMetadata->exclusionExpression);
                    return (bool)$this->expressionLanguage->evaluate($expression, array(
                        'classMetadata' => $classMetadata,
                        'propertyMetadata' => $propertyMetadata,
                        'object' => $this->currentObject,
                        'context' => $context));
                } elseif (null !== $propertyMetadata->inclusionExpression) {
                    $expression = new Expression($propertyMetadata->inclusionExpression);
                    return (bool)!$this->expressionLanguage->evaluate($expression, array(
                        'classMetadata' => $classMetadata,
                        'propertyMetadata' => $propertyMetadata,
                        'object' => $this->currentObject,
                        'context' => $context));
                }
            }
        }

        return false;
    }

    /**
     * In order to make sure our ExposeIf command can override an ALL ExclusionPolicy, we subscribe to the
     * pre-serialize event and add back any properties that were excluded due to the policy but exposed by
     * our ExposeIf annotation (potentially).
     *
     * JMS Serializer simply doesn't store the property meta data for excluded items, so we'll just create
     * it on the fly here so that our shouldSkipProperty() method has a chance to make a decision on it.
     *
     * @param PreSerializeEvent $event
     */
    public function onPreSerialize(PreSerializeEvent $event)
    {
        $this->currentObject = $object = $event->getObject();

        if(!is_object($object)) {
            return;
        }

        $class = get_class($object);
        $classMetadata = $this->metadataFactory->getMetadataForClass($class);
        $jmsClassMetadata = $event->getContext()->getMetadataFactory()->getMetadataForClass($class);

        if ($classMetadata === null || $jmsClassMetadata === null) {
            return;
        }

        if ($classMetadata instanceof ClassHierarchyMetadata) {
            $classMetadata = $classMetadata->classMetadata[$class];
        }

        if ($jmsClassMetadata instanceof ClassHierarchyMetadata) {
            $jmsClassMetadata = $jmsClassMetadata->classMetadata[$class];
        }

        $theirs = array_keys($jmsClassMetadata->propertyMetadata);
        $ours = array_keys($classMetadata->propertyMetadata);
        $missing = array_diff($ours, $theirs);
        foreach ($missing as $propertyName) {
            $jmsPropertyMetadata = new BasePropertyMetadata($class, $propertyName);
            $jmsClassMetadata->addPropertyMetadata($jmsPropertyMetadata);
        }
    }

    public static function getSubscribedEvents()
    {
       return array(
           array('event' => 'serializer.pre_serialize', 'method' => 'onPreSerialize')
       );
    }
}

