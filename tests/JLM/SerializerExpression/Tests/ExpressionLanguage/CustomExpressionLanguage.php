<?php

namespace JLM\SerializerExpression\Tests\ExpressionLanguage;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage as BaseExpressionLanguage;

class CustomExpressionLanguage extends BaseExpressionLanguage
{
    protected function registerFunctions()
    {
        parent::registerFunctions();

        $this->register('trueValue', function () {
            return sprintf('return true');
        }, function (array $variables)  {
            return true;
        });

        $this->register('falseValue', function () {
            return sprintf('return false');
        }, function (array $variables)  {
            return false;
        });
    }
}
