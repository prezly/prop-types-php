<?php

namespace Prezly\PropTypes\Tests\Checkers;

use PHPUnit\Framework\TestCase;
use Prezly\PropTypes\Checkers\PrimitiveTypeChecker;
use Prezly\PropTypes\Checkers\ShapeTypeChecker;
use Prezly\PropTypes\PropTypes;

class ShapeTypeCheckerTest extends TestCase
{
    /**
     * @test
     * @dataProvider valid_examples
     * @param array $shape
     * @param mixed $value
     */
    public function it_should_pass_valid_values(array $shape, $value)
    {
        $error = (new ShapeTypeChecker($shape))->validate(['value' => $value], 'value', 'test.value');
        $this->assertTrue($error === null);
    }

    /**
     * @test
     * @dataProvider invalid_examples
     * @param array $shape
     * @param mixed $value
     */
    public function it_should_return_error_for_invalid_values(
        array $shape,
        $value
    ) {
        $error = (new ShapeTypeChecker($shape))->validate(['value' => $value], 'value', 'test.value');
        $this->assertNotNull($error);
        // $this->assertEquals('value', $error->getPropName());
    }

    public function valid_examples(): iterable
    {
        yield 'empty shape requirements, empty array' => [
            [],
            []
        ];

        yield 'empty shape requirements, any array' => [
            [],
            ['anything' => 'goes', 'here' => '!']
        ];

        yield 'empty shape requirements, any numeric array' => [
            [],
            ['anything', 'goes', 'here', 200]
        ];

        yield 'arbitrary shape requirements' => [
            ['name' => PropTypes::string()],
            ['name' => 'Elvis Presley'],
        ];

        yield 'arbitrary shape requirements, array with extra props' => [
            ['name' => PropTypes::string()],
            ['name' => 'Elvis Presley', 'title' => 'The King'],
        ];

        yield 'tuple array requirements, tuple array' => [
            [PropTypes::string(), PropTypes::string()],
            ['Elvis Presley', 'The King'],
        ];

        yield 'tuple array requirements, tuple array with extra elements' => [
            [PropTypes::string(), PropTypes::string()],
            ['Elvis Presley', 'The King', null],
        ];
    }

    public function invalid_examples(): iterable
    {
        yield 'empty shape requirements, not an array' => [
            [],
            1
        ];

        yield 'arbitrary shape requirements, empty array' => [
            ['name' => PropTypes::string()],
            []
        ];

        yield 'arbitrary shape requirements, array with missing prop' => [
            ['name' => PropTypes::string()],
            ['title' => 'The King'],
        ];

        yield 'tuple requirements, empty array' => [
            [PropTypes::string()],
            [],
        ];

        yield 'tuple requirements, array with not enough elements' => [
            [PropTypes::string(), PropTypes::string()],
            ['Elvis Presley']
        ];
    }
}
