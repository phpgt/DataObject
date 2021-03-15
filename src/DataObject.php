<?php
namespace Gt\DataObject;

use DateTimeImmutable;
use DateTimeInterface;
use Gt\TypeSafeGetter\TypeSafeGetter;
use JsonSerializable;
use TypeError;

class DataObject implements JsonSerializable, TypeSafeGetter {
	/** @var mixed[] */
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
		$value = $this->data[$name] ?? null;
		if($value instanceof static) {
			return $value;
		}

		return null;
	}

	public function getString(string $name):?string {
		return $this->getAsType($name, "string");
	}

	public function getInt(string $name):?int {
		return $this->getAsType($name, "int");
	}

	public function getFloat(string $name):?float {
		return $this->getAsType($name, "float");
	}

	public function getBool(string $name):?bool {
		return $this->getAsType($name, "bool");
	}

	public function getDateTime(string $name):DateTimeInterface {
		return $this->getAsType($name, DateTimeInterface::class);
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
	public function getArray(string $name, string $type = null):array {
		$array = $this->get($name);

		if($type) {
			foreach($array as $i => $value) {
				$this->checkType($value, $type);
			}
		}

		return $array;
	}

	/** @return mixed[] */
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

	private function getAsType(
		string $name,
		string $type
	):mixed {
		$value = $this->get($name);
		if(is_null($value)) {
			return null;
		}

		switch($type) {
		case "int":
			return (int)$value;
		case "float":
			return (float)$value;
		case "string":
			return (string)$value;
		case "bool":
			return (bool)$value;
		}

		if(method_exists($this, "getAs$type")) {
			return call_user_func(
				[$this, "getAs$type"],
				$value
			);
		}

		return null;
	}

	private function getAsDateTimeInterface(mixed $value):DateTimeInterface {
		$dateTime = new DateTimeImmutable();

		if($value instanceof DateTimeInterface) {
			return $value;
		}
		elseif(is_int($value)) {
			$dateTime = $dateTime->setTimestamp($value);
		}
		elseif(is_float($value)) {
			$timestamp = (int)floor($value);
			$microsecond = ($value - $timestamp) * 1_000_000;
			$dateTime = $dateTime->setTimestamp($timestamp);
			$dateTime = $dateTime->setTime(
				(int)$dateTime->format("H"),
				(int)$dateTime->format("i"),
				(int)$dateTime->format("s"),
				(int)round($microsecond)
			);
		}
		else {
			$dateTime = new DateTimeImmutable($value);
		}

		return $dateTime;
	}

	private function checkType(mixed $value, string $type):void {
		switch($type) {
		case "int":
		case "integer":
			$typeMatches = is_int($value);
			break;

		case "bool":
		case "boolean":
			$typeMatches = is_bool($value);
			break;

		case "string":
			$typeMatches = is_string($value);
			break;

		case "float":
			$typeMatches = is_float($value);
			break;

		default:
			$typeMatches = (get_class($value) === $type);
			break;
		}

		if(!$typeMatches) {
			throw new TypeError("Value $value is expected to be of type $type");
		}
	}
}
