<?php

namespace JLM\SerializerExpression\Tests\Model;
use JLM\SerializerExpression\Annotation\ExcludeIf;
use JLM\SerializerExpression\Annotation\ExposeIf;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\VirtualProperty;

/**
 * With a NONE exclusion policy, only Exclude() and ExcludeIf() have any value. Expose() and ExposeIf() should be
 * ignored entirely. Between Exclude() and ExcludeIf(), Exclude() has higher priority.
 *
 * @ExclusionPolicy("NONE")
 */
class NoneExclusionPolicy
{
    /**
     * This property should be excluded
     *
     * @ExcludeIf("trueValue()")
     */
    public $a = true;

    /**
     * This property should not be excluded
     *
     * @ExcludeIf("falseValue()")
     */
    public $b = true;

    /**
     * This property should not be excluded (defaults to exposed when ExclusionPolicy = NONE)
     */
    public $c = true;

    /**
     * This property should not be excluded (defaults to exposed and ExposeIf not valid when ExclusionPolicy = NONE)
     *
     * @ExposeIf("trueValue()")
     */
    public $d = true;

    /**
     * This property should not be excluded (defaults to exposed and ExposeIf not valid when ExclusionPolicy = NONE)
     *
     * @ExposeIf("falseValue()")
     */
    public $e = true;

    /**
     * This property should be excluded (Exclude has higher priority)
     * @Exclude()
     * @ExcludeIf("trueValue()")
     */
    public $f = true;

    /**
     * This property should be excluded (Exclude has higher priority)
     * @Exclude()
     * @ExcludeIf("falseValue()")
     */
    public $g = true;

    /**
     * This property should be excluded (ExposeIf not valid when ExclusionPolicy = NONE)
     * @Exclude()
     * @ExposeIf("trueValue()")
     */
    public $h = true;

    /**
     * This property should be excluded (ExposeIf not valid when ExclusionPolicy = NONE)
     * @Exclude()
     * @ExposeIf("falseValue()")
     */
    public $i = true;

    /**
     * This virtual property should be excluded
     * @VirtualProperty()
     * @ExcludeIf("trueValue()")
     */
    public function aa() { return true; }

    /**
     * This virtual property should not be excluded
     * @VirtualProperty()
     * @ExcludeIf("falseValue()")
     */
    public function bb() { return true; }

    /**
     * @VirtualProperty()
     * This virtual property should not be excluded (defaults to exposed when ExclusionPolicy = NONE)
     */
    public function cc() { return true; }

    /**
     * This virtual property should not be excluded (defaults to exposed and ExposeIf not valid when ExclusionPolicy = NONE)
     * @VirtualProperty()
     * @ExposeIf("trueValue()")
     */
    public function dd() { return true; }

    /**
     * This virtual property should not be excluded (defaults to exposed and ExposeIf not valid when ExclusionPolicy = NONE)
     * @VirtualProperty()
     * @ExposeIf("falseValue()")
     */
    public function ee() { return true; }
}