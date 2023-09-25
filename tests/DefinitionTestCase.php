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

    protected function actual(Definition $definition) : object
    {
        return $definition->new($this->container);
    }

    /**
     * @param array{class-string, string}[] $expect
     */
    protected function assertNotInstantiable(
        Definition $definition,
        array $expect,
    ) : void
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
