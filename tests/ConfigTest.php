<?php
declare(strict_types=1);

namespace Capsule\Di;

class ConfigTest extends \PHPUnit\Framework\TestCase
{
    public function test()
    {
        $config = new Config();
        $config
            ->args('arg1', 'arg2')
            ->call('foo', 'foo1', 'foo2')
            ->call('foo', 'foo3', 'foo4')
            ->call('bar', 'bar1', 'bar2')
            ->call('bar', 'bar3', 'bar4');

        $expect = ['arg1', 'arg2'];
        $actual = $config->getArgs();
        $this->assertSame($expect, $actual);

        $expect = [
            ['foo', ['foo1', 'foo2']],
            ['foo', ['foo3', 'foo4']],
            ['bar', ['bar1', 'bar2']],
            ['bar', ['bar3', 'bar4']],
        ];
        $actual = $config->getCalls();
        $this->assertSame($expect, $actual);

        $config->reset();
        $this->assertSame([], $config->getArgs());
        $this->assertSame([], $config->getCalls());
    }
}
