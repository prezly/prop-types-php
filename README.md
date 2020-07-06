# PropTypes.php

Complete PHP port of [React PropTypes](https://github.com/facebook/prop-types).

Runtime type checking for complex properties structures.

You can use prop-types to document the intended types of properties passed into your code. PropTypes will check props passed to your functions against those definitions, and throw an error if they don’t match.

<img src="https://github.com/prezly/prop-types-php/workflows/Test/badge.svg" alt="Build status">

## Installation

```
composer require prezly/prop-types
```

## Usage

PropTypes was originally exposed as part of the React core module, and is commonly used with React components. We've tried to bring the familiarity of React PropTypes into PHP. Here is an example of using PropTypes with a PHP function, which also documents the different validators provided.

```php
<?php

use Prezly\PropTypes\Exceptions\PropTypeException;
use Prezly\PropTypes\PropTypes;

function myFunction(array $options): void
{
    PropTypes::check([
        // You can declare that a prop has a specific type.
        // By default, these are all optional.
        'requiredArray' => PropTypes::array(),
        'requiredBool' => PropTypes::bool(),
        'requiredInteger' => PropTypes::int(),
        'requiredFloat' => PropTypes::float(),
        'requiredObject' => PropTypes::object(),
        'requiredString' => PropTypes::string(),
        // You can also declare that a prop is an instance of a class.
        // This uses `instanceof` operator.
        'requiredDateTime' => PropTypes::instanceOf(DateTime::class),
        // You can ensure that your prop is limited to specific values
        // by treating it as an enum.
        'requiredEnum' => PropTypes::oneOf(['News', 'Photos']),
        // An object that could be one of many types
        'requiredUnion' => PropTypes::oneOfType([
            PropTypes::string(),
            PropTypes::int(),
            PropTypes::instanceOf(DateTime::class),
        ]),

        // An array of a certain type
        'requiredArrayOf' => PropTypes::arrayOf(PropTypes::int()),

        // You can chain any of the above with `isOptional()`
        // to make sure an error is not thrown if the prop isn't provided.

        // An object taking on a particular shape
        'requiredArrayWithShape' => PropTypes::shape([
            'requiredProperty' => PropTypes::int(),
            'optionalProperty' => PropTypes::string()->isOptional(),
        ]),

        // An object with errors on extra properties
        'requiredObjectWithStrictShape' => PropTypes::exact([
            'requiredProperty' => PropTypes::int(),
            'optionalProperty' => PropTypes::string()->isOptional(),
        ]),
    
        // You can chain any of the above with `isNullable()`
        // to allow passing `null` as a value.

        'requiredNullableString' => PropTypes::string()->isNullable(),
        'optionalNullableString' => PropTypes::string()->isNullable()->isOptional(),
        
        // A value of any data type (except null)
        'requiredAny' => PropTypes::any(),
        // A value of any data type (including null)
        'requiredNullableAny' => PropTypes::any()->isNullable(),

        // You can also specify a custom validator.
        // It should *return* a PropTypeException instance if the validation fails.
        'customProp' => PropTypes::callback(
            function (array $props, string $prop_name, string $prop_full_name): ?PropTypeException {
                if (! preg_match('/matchme /', $props[$prop_name])) {
                    return new PropTypeException(
                        $prop_name,
                        'Invalid prop `' . $prop_full_name . '` supplied. Validation failed.'
                    );
                }
                return null;
            }
        ),

        // You can also supply a custom validator to `arrayOf` and `objectOf`.
        // It should return an Error object if the validation fails. The validator
        // will be called for each key in the array or object. The first two
        // arguments of the validator are the array or object itself, and the
        // current item's key.
        'customArrayProp' => PropTypes::arrayOf(
            PropTypes::callback(function (array $props, string $prop_name, string $prop_full_name) {
                if (! preg_match('/matchme /', $props[$prop_name])) {
                    return new PropTypeException(
                        $prop_name,
                        'Invalid prop `' . $prop_full_name . '` supplied. Validation failed.'
                    );
                }
                return null;
            })
        ),
    ], $options);
}
```

## Difference from React PropTypes

1. In this package we've split *required* and *nullalbe* checks into different traits:
   - *Required* means a property has to be defined in the props object
   - *Nullable* means a property value can be set to `null`  
   
   React PropTypes has less straightforward logic around required, nulls and undefined.
   
2. As opposed to React PropTypes we don't have a separate checker for null (`PropTypes::null()`).
   Instead any property can become nullable by calling `->isNullable()` on its checker:
   
   ```php
   [
      'title' => PropTypes::string()->isNullable(),
   ]
   ```
   
3. Unlike React PropTypes, all properties are *required* by default (meaning they cannot be omitted).
   Unless `isOptional()` is explicitly called on its type checker. 

   This is done this way to closer match native PHP language semantics.

## Changelog

All notable changes to this project will be documented in the [CHANGELOG](./CHANGELOG) file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

-----------------

Brought to you with :heart: by [Prezly](https://www.prezly.com/?utm_source=github&utm_campaign=prop-types-php).
