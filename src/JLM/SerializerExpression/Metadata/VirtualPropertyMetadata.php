<?php
namespace JLM\SerializerExpression\Metadata;

class VirtualPropertyMetadata extends PropertyMetadata
{
    public function __construct($class, $methodName)
    {
        $this->class = $class;
        $this->name = $methodName;
    }

    public function setValue($obj, $value)
    {
        throw new \LogicException('VirtualPropertyMetadata is immutable.');
    }

    public function serialize()
    {
        return serialize(array(
            $this->inclusionExpression,
            $this->exclusionExpression,
            $this->class,
            $this->name
        ));
    }

    public function unserialize($str)
    {
        list(
            $this->inclusionExpression,
            $this->exclusionExpression,
            $this->class,
            $this->name
            ) = unserialize($str);
    }
}