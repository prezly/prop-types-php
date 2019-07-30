<?php
namespace Prezly\PropTypes\Tests;

use PHPUnit\Framework\TestCase;
use Prezly\PropTypes\PropTypes;

class PropTypesTest extends TestCase
{
    /**
     * @test
     * @dataProvider valid_data_examples
     * @param array $specs
     * @param array $values
     */
    public function it_should_silently_pass_valid_data(array $specs, array $values)
    {
        PropTypes::checkPropTypes($specs, $values);
        $this->assertTrue(true, "PropTypes::checkPropTypes didn't throw an exception");
    }

    public function valid_data_examples(): iterable
    {
        yield 'any property: string' => [
            ['name' => PropTypes::any()],
            ['name' => 'Elvis Presley'],
        ];
        yield 'any property: null' => [
            ['name' => PropTypes::any()],
            ['name' => null],
        ];
    }
}
