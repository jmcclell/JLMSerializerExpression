<?php

namespace JLM\SerializerExpression\Tests\Model\Nested;

use JLM\SerializerExpression\Annotation\ExcludeIf;

class Node
{
    public $id;

    /**
     * @ExcludeIf("object.children === []")
     */
    public $children = [];

    public function __construct($id, array $children = [])
    {
        $this->id = $id;
        $this->children = $children;
    }
}
