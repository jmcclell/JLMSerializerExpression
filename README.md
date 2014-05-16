# Status Badges (for master)

[![Build Status](https://travis-ci.org/jmcclell/JLMSerializerExpression.png?branch=master)](https://travis-ci.org/jmcclell/JLMSerializerExpression)
[![Coverage Status](https://coveralls.io/repos/jmcclell/JLMSerializerExpression/badge.png?branch=master)](https://coveralls.io/r/jmcclell/JLMSerializerExpression?branch=master)
[![Total Downloads](https://poser.pugx.org/jlm/aws-bundle/downloads.png)](https://packagist.org/packages/jlm/serializer-expression)
[![Latest Stable Version](https://poser.pugx.org/jlm/aws-bundle/v/stable.png)](https://packagist.org/packages/jlm/serializer-expression)

#JLMSerializerExpression

This library adds expression language support to Johannes Schmitt's [Serializer](https://github.com/schmittjoh/Serializer) library so that individual fields can be hidden based on expressions at runtime via an `@excludeIf` annotation.

# Installation

This library can be included via Composer by adding the following to your ```composer.json``` file:

```json
"require": {
    # ..
    "jlm/serializer-expression": "dev-master"
    # ..
}
```

# Usage

The library provides an exclusion strategy that must be configured and then added to your serialization context. Using this serialization context, when you serialize an object it is inspected for `@excludeIf` annotations which are then processed at runtime.

The exclusion strategy has two dependencies, an instance of `JMS\Metadata\MetadataFactory` from Johannes Schmitt's [Metadata](http://github.com/schmittjoh/Metadata) library and an instance of `Symfony\Component\ExpressionLanguage\ExpressionLanguage` which must be extended to provide the functionality you need.

### Annotating Your Objects

The `@excludeIf` accepts an expression that must be processable by the `ExpressionLanguage` instance you pass to the exclusion strategy. In the example below, we are using a dummy `hasAccessIfTrue` expression function that is created in the *Creating the Expression Language* section below. It isn't very useful, naturally. Creating a useful expression language is application-specific and left up to you. To see an example for Symfony applications, check out the [Symfony bundle of this library](http://github.com/jmcclell/JLMSerializerExpressionBundle).

```php
<?php

use JLM\SerializerExpression\Annotation\ExcludeIf;

class User
{
    /**
     * @ExcludeIf("hasAccessIfTrue(true)")
     */
    public $firstName = 'Jason';
    /**
     * @ExcludeIf("hasAccessIfTrue(true)")
     */
    public $lastName = 'McClellan';
    /**
     * @ExcludeIf("hasAccessIfTrue(false)")
     */
    public $phone = '555-555-5555';
    /**
     * @ExcludeIf("hasAccessIfTrue(false)")
     */
    public $address ='New York, NY';

    public $occupation = 'Software';
}
```

Given the above annotations, I would expect that if this object were serialized with a `SerializationContext` that included the `ExpressionBasedExclusionStrategy` configured with our custom `ExpressionLanguage`, we would only see 3 fields in the serialized output:
- first_name
- last_name
- occupation

### Creating the Metadata Factory

The metadata factory is what lets us store the expressions from our annotations. As such, it requires that we provide it with a way to read our `@excludeIf` annotation. The library comes with a built-in metadata driver with annotation capabilities: `JLM\SerializerExpression\Metadata\Driver\AnnotationDriver` which can be used directly, but it needs to be provided with an annotation reader. We are using `Doctrine\Common\Annotations\AnnotationReader` from the [Doctrine Common Library](http://www.doctrine-project.org/projects/common.html).

```php
use Doctrine\Common\Annotations\AnnotationReader;
use JLM\SerializerExpression\Metadata\Driver\AnnotationDriver;
use JMS\Metadata\MetadataFactory;

$annotationReader = new AnnotationReader();
$annotationDriver = new AnnotationDriver($annotationReader);
$metadataFactory = new MetadataFactory($annotationDriver);
```

To improve performance, it is *strongly* suggested that you enable caching for the metadata factory. File-based cachcing can be achieved as such:

```php
use Metadata\Cache\FileCache;

$metadataCache = new FileFache('/path/to/cache/dir');
$metadataFactory->setCache($metadataCache);
```

### Creating the Expression Language

The expression language is out of scope for this documentation. For that, please see the relevant [Symfony documentation](http://symfony.com/doc/current/components/expression_language/index.html).

However, here is a quick (and useless) example:

```php
use Symfony\Component\ExpressionLanguage\ExpressionLanguage as BaseExpressionLanguage;
use Symfony\Component\ExpressionLanguage\Expression;

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
```

### Full Example

```php
use JMS\Metadata\MetadataFactory;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializationContext;
use Doctrine\Common\Annotations\AnnotationReader;
use JLM\SerializerExpression\Metadata\Driver\AnnotationDriver;


$expressionLang = new CustomExpressionLanguage();

$metadataDriver = new AnnotationDriver($annotationReader);
$metadataFactory = new MetadataFactory($metadataDriver);

$exclusionStrategy = new ExpressionBasedExclusionStrategy($metadataFactory, $expressionLang);

$serializationContext = SerializationContext::create();
$serializationContext->addExclusionStrategy($exclusionStrategy);

$serializer = SerializerBuilder::create()->build();

$serializedContent = $serializer->serialize($data, 'json', $serializationContext);
```

