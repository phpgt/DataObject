<?php
namespace Gt\DataObject;

use DateTimeImmutable;
use DateTimeInterface;
use Gt\TypeSafeGetter\NullableTypeSafeGetter;
use Gt\TypeSafeGetter\TypeSafeGetter;
use JsonSerializable;
use TypeError;

class DataObject implements JsonSerializable, TypeSafeGetter {
	use NullableTypeSafeGetter;

	/** @var array<string, mixed> */
	protected array $data;

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

	public function getObject(string $name):?static {
		return $this->getInstance($name, static::class);
	}

	public function contains(string $name):bool {
		return isset($this->data[$name]);
	}

	public function typeof(string $name):?string {
		if(!array_key_exists($name, $this->data)) {
			return null;
		}

		$value = $this->data[$name];
		switch(gettype($value)) {
		case "integer":
			return "int";
		case "double":
			return "float";
		case "boolean":
			return "bool";
		case "NULL":
			return "null";
		case "object":
			return get_class($value);
		default:
			return gettype($value);
		}
	}

	/** @noinspection PhpMixedReturnTypeCanBeReducedInspection - This allows php.gt/json to extend and return primitives */
	public function jsonSerialize():mixed {
		return $this->asArray();
	}

	/**
	 * Get an array by name, with optionally fixed types - specify a $type
	 * as either an inbuilt primitive (string, int, etc.) or a class name
	 * (DateTime::class, Example::class, etc.)
	 * @return mixed[]
	 */
	public function getArray(string $name, ?string $type = null):?array {
		$array = $this->get($name);
		if(is_object($array) && method_exists($array, "asArray")) {
			$array = $array->asArray();
		}

		if($array && $type) {
			$array = $this->checkArrayType($array, $type);
		}

		return $array;
	}

	/** @return array<string, mixed> */
	public function asArray():array {
		$array = $this->data;

		array_walk_recursive($array, function(&$item):void {
			if($item instanceof static) {
				$item = $item->asArray();
			}
		});

		return $array;
	}

	public function asObject():object {
		$array = $this->data;

		array_walk_recursive($array, function(&$item):void {
			if($item instanceof static) {
				$item = $item->asObject();
			}
		});

		return (object)$array;
	}

	/**
	 * @param array<mixed> $array
	 * @return array<mixed>
	 */
	private function checkArrayType(array $array, string $type): array {
		$this->validateTypeExists($type);

		foreach ($array as $i => $value) {
			$array[$i] = $this->processValue($value, $type, $i);
		}

		return $array;
	}

	private function validateTypeExists(string $type): void {
		if(!class_exists($type)
		&& !interface_exists($type)
		&& !function_exists("is_$type")) {
			throw new TypeError("Invalid type: $type does not exist.");
		}
	}

	private function processValue(
		mixed $value,
		string $type,
		int $index,
	): mixed {
		if (class_exists($type) || interface_exists($type)) {
			$this->assertInstanceOfType($value, $type, $index);
		} elseif (function_exists("is_$type")) {
			return $this->castValue($value, $type);
		}

		return $value;
	}

	private function assertInstanceOfType(
		mixed $value,
		string $type,
		int $index,
	): void {
		if (!is_a($value, $type)) {
			$actualType = is_scalar($value)
				? gettype($value)
				: get_class($value);
			throw new TypeError("Array index $index"
				. " must be of type $type, $actualType given");
		}
	}

	private function castValue(mixed $value, string $type): mixed {
		return match ($type) {
			"int" => (int)$value,
			"bool" => (bool)$value,
			"string" => (string)$value,
			"float", "double" => (float)$value,
			"array" => (array)$value,
			default => null,
		};
	}
}
