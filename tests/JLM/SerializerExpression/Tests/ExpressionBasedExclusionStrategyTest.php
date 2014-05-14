<?php

namespace JLM\SerializerExpression\Tests;

use JLM\SerializerExpression\Tests\Model\User;
use JLM\SerializerExpression\Tests\ExpressionLanguage\CustomExpressionLanguage;

use JLM\SerializerExpression\Metadata\Driver\AnnotationDriver;
use JLM\SerializerExpression\Exclusion\ExpressionBasedExclusionStrategy;

use Doctrine\Common\Annotations\AnnotationReader;

use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializationContext;

use Metadata\MetadataFactory;


class ExpressionBasedExclusionStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function testExclusion()
    {
        $user = new User();
        $expressionLang = new CustomExpressionLanguage();
        $annotationReader = new AnnotationReader();
        $metadataDriver = new AnnotationDriver($annotationReader);
        $metadataFactory = new MetadataFactory($metadataDriver);
        $exclusionStrategy = new ExpressionBasedExclusionStrategy($metadataFactory, $expressionLang);

        $serializer = SerializerBuilder::create()->build();

        $context = SerializationContext::create();
        $context->addExclusionStrategy($exclusionStrategy);
        $data = $serializer->serialize($user, 'json', $context);

        $check = json_decode($data, true);

        $this->assertEquals(3, count($check));
        foreach (array_keys($check) as $key) {
            $this->assertTrue(in_array($key, array('first_name', 'last_name', 'occupation')));
        }
    }
}
?>