<?php

namespace plugin\dutoland\completeFramework\bean\inerface;

interface ICompleteDefinition {
	public function complete(array $objectToCompletes): void;
}