<?php
namespace Capsule\Di;

class DefinitionTestCase extends \PHPUnit\Framework\TestCase
{
    protected Container $container;

    protected Definitions $definitions;

    protected function setUp() : void
    {
        $this->definitions = new Definitions();
        $this->container = new Container($this->definitions);
    }

    protected function actual(Definition $definition)
    {
        return $definition->new($this->container, $this->definitions);
    }

    protected function assertNotInstantiable(Definition $definition, array $expect)
    {
        try {
            $this->actual($definition);
            $this->assertFalse(true, "Should not have been instantiated.");
        } catch (Exception\NotInstantiated $e) {
            while (! empty($expect)) {
                $e = $e->getPrevious();
                [$expectException, $expectExceptionMessage] = array_shift($expect);
                $this->assertInstanceOf($expectException, $e);
                $this->assertSame($expectExceptionMessage, $e->getMessage());
            }
        }
    }
}
