# Status Badges (for master)

[![Build Status](https://travis-ci.org/jmcclell/JLMSerializerExpression.png?branch=master)](https://travis-ci.org/jmcclell/JLMSerializerExpression)
[![Coverage Status](https://coveralls.io/repos/jmcclell/JLMSerializerExpression/badge.png?branch=master)](https://coveralls.io/r/jmcclell/JLMSerializerExpression?branch=master)
[![Total Downloads](https://poser.pugx.org/jlm/aws-bundle/downloads.png)](https://packagist.org/packages/jlm/serializer-expression)
[![Latest Stable Version](https://poser.pugx.org/jlm/aws-bundle/v/stable.png)](https://packagist.org/packages/jlm/serializer-expression)

#JLMSerializerExpression

This library adds expression language support to [JMSSerializer](https://github.com/schmittjoh/JMSSerializer) so that individual fields can be hidden based on expressions at runtime via an `@excludeIf` annotation.

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

The library provides an exclusion strategy that must be configured and then added to your serialization context.

The exclusion strategy has two dependencies, an instance of `JMS\Metadata\MetadataFactory` from Johannes Schmitt's [Metadata](http://github.com/schmittjoh/Metadata) library and an instance of 'Symfony\Component\ExpressionLanguage\ExpressionLanguage` which must be extended to provide the functionality you need.

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

#### Metadata Cache

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
$serializer = JMS\Serializer\SerializerBuilder::create()->build();
$serializedContent = $serializer->serialize($data, 'json', $serializationContext);
```

