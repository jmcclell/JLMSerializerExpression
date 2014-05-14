<?php

namespace JLM\SerializerExpression\Tests\ExpressionLanguage;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage as BaseExpressionLanguage;
use Symfony\Component\ExpressionLanguage\Expression;


/**
 */
class CustomExpressionLanguage extends BaseExpressionLanguage
{
    protected function registerFunctions()
    {
        parent::registerFunctions();

        // Test expression which always returns the opposite of the value we pass to it
        $this->register('hasAccessIfTrue', function ($arg) {
            return sprintf('return !%s', $arg);
        }, function (array $variables, $value)  {
            return !$value;
        });
    }
}
