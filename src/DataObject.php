<?php
namespace Gt\DataObject;

use DateTimeInterface;
use Gt\TypeSafeGetter\TypeSafeGetter;
use JsonSerializable;

class DataObject implements JsonSerializable, TypeSafeGetter {
	private array $data;

	public function __construct() {
		$this->data = [];
	}

	public function with(string $key, mixed $data):static {
		$clone = clone $this;
		$clone->data[$key] = $data;
		return $clone;
	}

	public function without(string $key):static {
		$clone = clone $this;
		unset($clone->data[$key]);
		return $clone;
	}

	public function get(string $name):mixed {
		return $this->data[$name] ?? null;
	}

	public function getString(string $name):?string {
		// TODO: Implement getString() method.
	}

	public function getInt(string $name):?int {
		// TODO: Implement getInt() method.
	}

	public function getFloat(string $name):?float {
		// TODO: Implement getFloat() method.
	}

	public function getBool(string $name):?bool {
		// TODO: Implement getBool() method.
	}

	public function getDateTime(string $name):DateTimeInterface {
		// TODO: Implement getDateTime() method.
	}

	public function jsonSerialize():mixed {
		return $this->asArray();
	}

	public function asArray(bool $nested = true):array {

	}

	public function asObject(bool $nested = true):object {
		return (object)$this->asArray($nested);
	}
}