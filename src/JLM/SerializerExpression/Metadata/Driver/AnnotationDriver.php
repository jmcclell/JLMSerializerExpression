<?php

namespace JLM\SerializerExpression\Metadata\Driver;

use Doctrine\Common\Annotations\Reader;

use JLM\SerializerExpression\Annotation\ExcludeIf;
use JLM\SerializerExpression\Annotation\ExposeIf;
use JLM\SerializerExpression\Metadata\PropertyMetadata;
use JLM\SerializerExpression\Metadata\MethodMetadata;

use JLM\SerializerExpression\Metadata\VirtualPropertyMetadata;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\VirtualProperty;
use Metadata\Driver\DriverInterface;
use Metadata\ClassMetadata;


class AnnotationDriver implements DriverInterface
{
    protected $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function loadMetadataForClass(\ReflectionClass $class)
    {
        $name = $class->name;
        $classMetadata = new ClassMetadata($name);
        $classMetadata->fileResources[] = $class->getFilename();

        $exclusionPolicy = $this->reader->getClassAnnotation($class, ExclusionPolicy::class);

        $policy = ($exclusionPolicy === null) ? ExclusionPolicy::NONE : $exclusionPolicy->policy;

        foreach ($class->getProperties() as $property) {
            if ($property->class !== $name) {
                continue;
            }
            $propertyMetadata = new PropertyMetadata($name, $property->getName());

            if ($policy === ExclusionPolicy::NONE) {
                $exclude = $this->reader->getPropertyAnnotation($property, Exclude::class);
                if ($exclude === null) {
                    $excludeIf = $this->reader->getPropertyAnnotation($property, ExcludeIf::class);
                    if ($excludeIf !== null) {
                        $propertyMetadata->exclusionExpression = $excludeIf->expression;
                        $classMetadata->addPropertyMetadata($propertyMetadata);
                    }
                }
            } else {
                $expose = $this->reader->getPropertyAnnotation($property, Expose::class);
                if ($expose === null) {
                    $exposeIf = $this->reader->getPropertyAnnotation($property, ExposeIf::class);
                    if ($exposeIf !== null) {
                        $propertyMetadata->inclusionExpression = $exposeIf->expression;
                        $classMetadata->addPropertyMetadata($propertyMetadata);
                    }

                }
            }

        }

        foreach ($class->getMethods() as $method) {
            if ($method->class !== $name) {
                continue;
            }

            $virtual = $this->reader->getMethodAnnotation($method, VirtualProperty::class);

            if ($virtual === null) {
                continue;
            }

            $virtualPropertyMetadata = new VirtualPropertyMetadata($name, $method->getName());

            if ($policy === ExclusionPolicy::NONE) {
                $excludeIf = $this->reader->getMethodAnnotation($method, ExcludeIf::class);
                if ($excludeIf !== null) {
                    $virtualPropertyMetadata->exclusionExpression = $excludeIf->expression;
                    $classMetadata->addPropertyMetadata($virtualPropertyMetadata);
                }
            } else {
                $exposeIf = $this->reader->getMethodAnnotation($method, ExposeIf::class);
                if ($exposeIf !== null) {
                    $virtualPropertyMetadata->inclusionExpression = $exposeIf->expression;
                    $classMetadata->addPropertyMetadata($virtualPropertyMetadata);
                }
            }
        }


        return $classMetadata;
    }
}