<?php
declare(strict_types=1);

namespace Capsule\Di;

use Psr\Container\ContainerExceptionInterface;

class Exception extends \Exception implements ContainerExceptionInterface
{
}
