<?php
declare(strict_types=1);

namespace Capsule\Di;

interface Provider
{
	public function provide(Definitions $def) : void;
}
