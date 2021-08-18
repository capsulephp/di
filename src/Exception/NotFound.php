<?php
declare(strict_types=1);

namespace Capsule\Di\Exception;

use Psr\Container\NotFoundExceptionInterface;

class NotFound extends Exception implements NotFoundExceptionInterface
{
}
