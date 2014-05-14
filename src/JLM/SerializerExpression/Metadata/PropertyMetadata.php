<?php

namespace JLM\SerializerExpression\Metadata;

use JMS\Serializer\TypeParser;
use JMS\Serializer\Exception\RuntimeException;

use Metadata\PropertyMetadata as BasePropertyMetadata;


class PropertyMetadata extends BasePropertyMetadata
{
    public $exclusionExpression;

    public function getExclusionExpression()
    {
        return $this->exclusionExpression;
    }

    public function setExclusionExpression($exclusionExpression)
    {
        $this->exclusionExpression = $exclusionExpression;
    }

    public function serialize()
    {
        return serialize(array(
            $this->exclusionExpression,            
            parent::serialize(),
        ));
    }

    public function unserialize($str)
    {
        list(
            $this->exclusionExpression,            
            $parentStr
        ) = unserialize($str);

        parent::unserialize($parentStr);
    }
}
