<?php

namespace JLM\SerializerExpression\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD"})
 */
final class ExposeIf
{
    /** @var string @Required */
    public $expression;
}