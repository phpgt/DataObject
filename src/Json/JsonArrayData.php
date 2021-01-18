<?php
namespace Gt\DataObject\Json;

use ArrayAccess;
use Countable;
use Gt\DataObject\DataObject;
use Gt\DataObject\ImmutableObjectException;
use Iterator;

class JsonArrayData extends JsonData implements ArrayAccess, Countable, Iterator {
	private int $iteratorIndex;

	public function __construct() {
		parent::__construct();
		$this->iteratorIndex = 0;
	}

	public function offsetExists($offset):bool {
		return isset($this->data[$offset]);
	}

	public function offsetGet($offset):DataObject|bool|int|float|string {
		return $this->data[$offset];
	}

	public function offsetSet($offset, $value):void {
		throw new ImmutableObjectException();
	}

	public function offsetUnset($offset):void {
		throw new ImmutableObjectException();
	}

	public function current():DataObject|bool|int|float|string {
		return $this->data[$this->iteratorIndex];
	}

	public function next():void {
		$this->iteratorIndex++;
	}

	public function key():int {
		return $this->iteratorIndex;
	}

	public function valid():bool {
		return isset($this->data[$this->iteratorIndex]);
	}

	public function rewind():void {
		$this->iteratorIndex = 0;
	}

	public function count() {
		return count($this->data);
	}
}