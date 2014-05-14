<?php

namespace JLM\SerializerExpression\Serializer\Metadata\Driver;

use Doctrine\Common\Annotations\Reader;

use JLM\SerializerExpression\Serializer\Annotation\ExcludeIf;
use JLM\SerializerExpression\Serializer\Metadata\PropertyMetadata;

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
        $classMetadata = new ClassMetadata($name = $class->name);
        $classMetadata->fileResources[] = $class->getFilename();

        $propertiesMetadata = array();
        $propertiesAnnotations = array();

        foreach ($class->getProperties() as $property) {
            if ($property->class !== $name) {
                continue;
            }
            $propertiesMetadata[] = new PropertyMetadata($name, $property->getName());
            $propertiesAnnotations[] = $this->reader->getPropertyAnnotations($property);
        }

        foreach ($propertiesMetadata as $propertyKey => $propertyMetadata) {
            
            $propertyAnnotations = $propertiesAnnotations[$propertyKey];

            foreach ($propertyAnnotations as $annot) {
                if ($annot instanceof ExcludeIf) {
                    $propertyMetadata->exclusionExpression = $annot->expression;
                }
            }

            $classMetadata->addPropertyMetadata($propertyMetadata);
        }

        return $classMetadata;
    }
}