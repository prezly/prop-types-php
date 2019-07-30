<?php
namespace Prezly\PropTypes\Tests\Checkers;

use PHPUnit\Framework\TestCase;
use Prezly\PropTypes\Checkers\AnyTypeChecker;
use Prezly\PropTypes\Exceptions\PropTypeException;

class AnyTypeCheckerTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_always_return_null_for_any_input()
    {
        $this->assertNull($this->validate(['name' => 'Elvis'], 'name'));
        $this->assertNull($this->validate(['name' => null], 'name'));
        $this->assertNull($this->validate(['name' => 1], 'name'));
        $this->assertNull($this->validate(['name' => PHP_INT_MAX], 'name'));
        $this->assertNull($this->validate(['name' => INF], 'name'));
        $this->assertNull($this->validate(['name' => $this], 'name'));
    }

    private function validate(array $props, string $prop_name): ?PropTypeException
    {
        return (new AnyTypeChecker())->validate($props, $prop_name, $prop_name);
    }
}
