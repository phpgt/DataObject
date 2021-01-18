<?php
namespace Gt\DataObject\Json;

class JsonPrimitiveData extends JsonData {
	private null|string|bool|int|float $value;

	public function withValue(null|string|bool|int|float $value):static {
		$clone = clone $this;
		$clone->value = $value;
		return $clone;
	}

	public function getValue():null|string|bool|int|float {
		return $this->value;
	}

	public function getType():string {
		return gettype($this->value);
	}
}