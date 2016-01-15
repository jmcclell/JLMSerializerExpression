<?php

namespace JLM\SerializerExpression\Tests;

use JLM\SerializerExpression\Tests\Model\AllExclusionPolicy;
use JLM\SerializerExpression\Tests\ExpressionLanguage\CustomExpressionLanguage;

use JLM\SerializerExpression\Metadata\Driver\AnnotationDriver;
use JLM\SerializerExpression\Exclusion\ExpressionBasedExclusionStrategy;

use Doctrine\Common\Annotations\AnnotationReader;

use JLM\SerializerExpression\Tests\Model\Nested\Node;
use JLM\SerializerExpression\Tests\Model\NoneExclusionPolicy;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializationContext;

use Metadata\MetadataFactory;


class ExpressionBasedExclusionStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Serializer
     */
    protected $serializer;
    /**
     * @var SerializationContext
     */
    protected $context;

    public function setUp()
    {

        $expressionLang = new CustomExpressionLanguage();
        $annotationReader = new AnnotationReader();
        $metadataDriver = new AnnotationDriver($annotationReader);
        $metadataFactory = new MetadataFactory($metadataDriver);
        $exclusionStrategy = new ExpressionBasedExclusionStrategy($metadataFactory, $expressionLang);
        $this->serializer = SerializerBuilder::create()
            ->configureListeners(function(EventDispatcher $dispatcher) use ($exclusionStrategy) {
                    $dispatcher->addSubscriber($exclusionStrategy);
                })
            ->build();
        $context = SerializationContext::create();
        $context->addExclusionStrategy($exclusionStrategy);
        $this->context = $context;
    }

    protected function serialize($data)
    {
        return $this->serializer->serialize($data, 'json', $this->context);
    }

    public function testAllExclusionPolicy()
    {
        $model = new AllExclusionPolicy();

        $data = $this->serialize($model);
        $data = json_decode($data, true);

        $dataKeys = array_keys($data);
        sort($dataKeys);
        $expectedKeys = ['a', 'f', 'g', 'h', 'i', 'aa', 'cc', 'dd', 'ee'];
        sort($expectedKeys);

        $this->assertEquals($expectedKeys, $dataKeys);
    }

    public function testNoneExclusionPolicy()
    {
        $model = new NoneExclusionPolicy();
        $data = $this->serialize($model);
        $data = json_decode($data, true);

        $dataKeys = array_keys($data);
        sort($dataKeys);
        $expectedKeys = ['b', 'c', 'd', 'e', 'bb', 'cc', 'dd', 'ee'];
        sort($expectedKeys);

        $this->assertEquals($expectedKeys, $dataKeys);
    }

    public function testNestedObjectHasProperObjectContext()
    {
        $model = new Node("one.one", [
            new Node("two.one", [
                new Node("three.one"),
                new Node("three.two"),
            ]),
            new Node("two.two"),
            new Node("two.three"),
       ]);

        $data = $this->serialize($model);
        $data = json_decode($data, true);

        $expectedData = [
            'id' => 'one.one',
            'children' => [
                [
                    'id' => 'two.one',
                    'children' => [
                        ['id' => 'three.one'],
                        ['id' => 'three.two'],
                    ],
                ],
                ['id' => 'two.two'],
                ['id' => 'two.three'],
            ]
        ];

        $this->assertEquals($expectedData, $data);
    }
}