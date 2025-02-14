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
	public function getArray(string $name, string $type = null):?array {
		$array = $this->get($name);

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
	 * @param array $array
	 * @return array
	 */
	private function checkArrayType(array $array, string $type):array {
		$errorMessage = "";

		foreach($array as $i => $value) {
			$actualType = is_scalar($value) ? gettype($value) : get_class($value);

			if(class_exists($type) || interface_exists($type)) {
				if(!is_a($value, $type)) {
					$errorMessage = "Array index $i must be of type $type, $actualType given";
				}
			}
			elseif(function_exists("is_$type")) {
				$castedValue = match($type) {
					"int" => (int)$value,
					"bool" => (bool)$value,
					"string" => (string)$value,
					"float", "double" => (float)$value,
					"array" => (array)$value,
					default => null,
				};
				$array[$i] = $castedValue;
			}
		}

		if($errorMessage) {
			throw new TypeError($errorMessage);
		}

		return $array;
	}
}
