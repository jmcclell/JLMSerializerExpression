<?php

namespace JLM\SerializerExpression\Annotation;

/**
 * Uses Symfony expressons to determine of the user is authorized
 * to see this object or property. Used by an exclusion strategy.
 * 
 * @Annotation
 * @Target({"PROPERTY"})
 */
final class ExcludeIf
{
    /** @var string @Required */
    public $expression;
}