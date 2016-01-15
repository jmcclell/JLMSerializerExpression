<?php

namespace JLM\SerializerExpression\Tests\Model;
use JLM\SerializerExpression\Annotation\ExcludeIf;
use JLM\SerializerExpression\Annotation\ExposeIf;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\VirtualProperty;

/**
 * With an ALL exclusion policy, only Expose() and ExposeIf() have any value. Exclude() and ExcludeIf() should be
 * ignored entirely. Between Expose() and ExposeIf(), Expose() has higher priority.
 *
 * @ExclusionPolicy("ALL")
 */
class AllExclusionPolicy
{
    /**
     * This property should be not excluded
     *
     * @ExposeIf("trueValue()")
     */
    public $a = true;

    /**
     * This property should be excluded
     *
     * @ExposeIf("falseValue()")
     */
    public $b = true;

    /**
     * This property should be excluded (defaults to excluded when ExclusionPolicy = ALL)
     */
    public $c = true;

    /**
     * This property should be excluded (defaults to excluded and ExcludeIf not valid when ExclusionPolicy = ALL)
     *
     * @ExcludeIf("trueValue()")
     */
    public $d = true;

    /**
     * This property should be excluded (defaults to excluded and ExcludeIf not valid when ExclusionPolicy = ALL)
     *
     * @ExcludeIf("falseValue()")
     */
    public $e = true;

    /**
     * This property should not be excluded (Expose has higher priority)
     * @Expose()
     * @ExposeIf("trueValue()")
     */
    public $f = true;

    /**
     * This property should not be excluded (Expose has higher priority)
     * @Expose()
     * @ExposeIf("falseValue()")
     */
    public $g = true;

    /**
     * This property should not be excluded (ExcludeIf not valid when ExclusionPolicy = ALL)
     * @Expose()
     * @ExcludeIf("trueValue()")
     */
    public $h = true;

    /**
     * This property should not be excluded (ExcludeIf not valid when ExclusionPolicy = ALL)
     * @Expose()
     * @ExcludeIf("falseValue()")
     */
    public $i = true;

    /**
     * This virtual property should not be excluded
     * @VirtualProperty()
     * @ExposeIf("trueValue()")
     */
    public function aa() { return true; }

    /**
     * This virtual property should be excluded
     * @VirtualProperty()
     * @ExposeIf("falseValue()")
     */
    public function bb() { return true; }

    /**
     * @VirtualProperty()
     * This virtual property should not be excluded (virtual properties ignore ExclusionPolicy)
     */
    public function cc() { return true; }

    /**
     * This virtual property should not be excluded (virtual properties ignore ExclusionPolicy)
     * @VirtualProperty()
     * @ExcludeIf("trueValue()")
     */
    public function dd() { return true; }

    /**
     * This virtual property should be excluded (defaults to excluded and ExcludeIf not valid when ExclusionPolicy = ALL)
     * @VirtualProperty()
     * @ExcludeIf("falseValue()")
     */
    public function ee() { return true; }
}