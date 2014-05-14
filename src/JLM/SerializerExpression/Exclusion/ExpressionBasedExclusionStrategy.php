<?php

namespace JLM\SerializerExpression\Exclusion;

use JMS\Serializer\Exclusion\ExclusionStrategyInterface;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata as BasePropertyMetadata;
use JMS\Serializer\Context;

use Metadata\MetadataFactory;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\Expression;

/**
 * Exclusion strategy based on Symfony expression langauge component 
 *
 * @author Jason McClellan <jason_mcclellan@jasonmcclellan.io>
 */
class ExpressionBasedExclusionStrategy implements ExclusionStrategyInterface
{
    protected $metadataFactory;
    protected $expressionLanguage;

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
     *
     * @return boolean
     */
    public function shouldSkipProperty(BasePropertyMetadata $property, Context $context)
    {
        if (null !== $property->class) {
            $classMetadata = $this->metadataFactory->getMetadataForClass($property->class);
            $classMetadata = $classMetadata->classMetadata[$property->class];
            // Converts the JMS Serializer property metadata into our own property metadata
            $propertyMetadata = $classMetadata->propertyMetadata[$property->name];
      
            if (null !== $propertyMetadata->exclusionExpression) {
                $expression = new Expression($propertyMetadata->exclusionExpression);  
                return (bool)$this->expressionLanguage->evaluate($expression, array(
                        'classMetadata' => $classMetadata,
                        'propertyMetadata' => $propertyMetadata,
                        'context' => $context));  
            }
        }        
        
        return false;
    }
}