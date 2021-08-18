<?php
declare(strict_types=1);

namespace Capsule\Di\Exception;

use Psr\Container\ContainerExceptionInterface;

abstract class Exception extends \Exception implements ContainerExceptionInterface
{
}
