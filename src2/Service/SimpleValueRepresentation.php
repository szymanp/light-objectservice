<?php
namespace Szyman\ObjectService\Service;

use Light\ObjectAccess\Resource\ResolvedScalar;
use Szyman\ObjectService\Service\ExecutionEnvironment;

interface SimpleValueRepresentation
{
	public function applyTo(ResolvedScalar $target, ExecutionEnvironment $parameters);
}