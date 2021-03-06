<?php

namespace JLM\SerializerExpression\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD"})
 */
final class ExcludeIf
{
    /** @var string @Required */
    public $expression;
}