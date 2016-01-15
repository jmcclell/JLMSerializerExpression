<?php

namespace JLM\SerializerExpression\Metadata;

use Metadata\PropertyMetadata as BasePropertyMetadata;


class PropertyMetadata extends BasePropertyMetadata
{
    public $exclusionExpression;
    public $inclusionExpression;

    public function getInclusionExpression()
    {
        return $this->inclusionExpression;
    }

    public function setInclusionExpression($inclusionExpression)
    {
        $this->inclusionExpression = $inclusionExpression;
    }

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
            $this->inclusionExpression,
            parent::serialize(),
        ));
    }

    public function unserialize($str)
    {
        list(
            $this->exclusionExpression,
            $this->inclusionExpression,
            $parentStr
        ) = unserialize($str);

        parent::unserialize($parentStr);
    }
}
