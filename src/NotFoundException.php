<?php
declare(strict_types=1);

namespace Capsule\Di;

use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends Exception implements NotFoundExceptionInterface
{
}
