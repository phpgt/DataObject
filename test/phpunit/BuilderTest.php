<?php
namespace Gt\DataObject\Test;

use Gt\DataObject\Builder;
use PHPUnit\Framework\TestCase;
use stdClass;

class BuilderTest extends TestCase {
	public function testFromObjectSimple() {
		$obj = new StdClass();
		$obj->key1 = "value1";
		$obj->key2 = "value2";

		$sut = new Builder();
		$output = $sut->fromObject($obj);

		self::assertEquals("value1", $output->getString("key1"));
		self::assertEquals("value2", $output->getString("key2"));
	}

	public function testFromObjectNested() {
		$obj = new StdClass();
		$obj->key1 = "value1";
		$obj->key2 = "value2";
		$obj->nested = new StdClass();
		$obj->nested->key3 = "value3";
		$obj->nested->key4 = "value4";

		$sut = new Builder();
		$output = $sut->fromObject($obj);

		self::assertEquals("value1", $output->getString("key1"));
		self::assertEquals("value2", $output->getString("key2"));
		$nestedOutput = $output->get("nested");
		self::assertIsObject($nestedOutput);
		self::assertEquals("value3", $nestedOutput->getString("key3"));
		self::assertEquals("value4", $nestedOutput->getString("key4"));
	}

	public function testFromObjectNestedArray() {
		$obj = new StdClass();
		$obj->key1 = "value1";
		$obj->key2 = "value2";
		$obj->nested = new StdClass();
		$obj->nested->key3 = "value3";
		$obj->nested->key4 = "value4";

		$innerObj1 = new StdClass();
		$innerObj1->key5 = "value5";
		$innerObj2 = new StdClass();
		$innerObj2->key6 = "value6";
		$obj->nested->arr = array(
			$innerObj1,
			$innerObj2,
		);

		$sut = new Builder();
		$output = $sut->fromObject($obj);

		self::assertEquals("value1", $output->getString("key1"));
		self::assertEquals("value2", $output->getString("key2"));
		$nestedOutput = $output->get("nested");
		self::assertIsObject($nestedOutput);
		self::assertEquals("value3", $nestedOutput->getString("key3"));
		self::assertEquals("value4", $nestedOutput->getString("key4"));
		$nestedArray = $nestedOutput->get("arr");
		self::assertIsArray($nestedArray);
		self::assertEquals("value5", $nestedArray[0]->getString("key5"));
		self::assertEquals("value6", $nestedArray[1]->getString("key6"));
	}

	public function testFromArraySimple() {
		$array = array(
			"key1" => "value1",
			"key2" => "value2",
		);

		$sut = new Builder();
		$output = $sut->fromArray($array);

		self::assertEquals("value1", $output->getString("key1"));
		self::assertEquals("value2", $output->getString("key2"));
	}

	public function testFromArrayNested() {
		$array = array(
			"key1" => "value1",
			"key2" => "value2",
			"nested" => [
				"key3" => "value3",
				"key4" => "value4",
			]
		);

		$sut = new Builder();
		$output = $sut->fromArray($array);

		self::assertEquals("value1", $output->getString("key1"));
		self::assertEquals("value2", $output->getString("key2"));
		$nestedOutput = $output->get("nested");
		self::assertIsObject($nestedOutput);
		self::assertEquals("value3", $nestedOutput->getString("key3"));
		self::assertEquals("value4", $nestedOutput->getString("key4"));
	}

	public function testFromArrayNestedArray() {
		$array = array(
			"key1" => "value1",
			"key2" => "value2",
			"nested" => [
				"key3" => "value3",
				"key4" => "value4",
				"arr" => [
					["key5" => "value5"],
					["key6" => "value6"],
				]
			]
		);

		$sut = new Builder();
		$output = $sut->fromArray($array);

		self::assertEquals("value1", $output->getString("key1"));
		self::assertEquals("value2", $output->getString("key2"));
		$nestedOutput = $output->get("nested");
		self::assertIsObject($nestedOutput);
		self::assertEquals("value3", $nestedOutput->getString("key3"));
		self::assertEquals("value4", $nestedOutput->getString("key4"));
		$nestedArray = $nestedOutput->get("arr");
		self::assertIsArray($nestedArray);
		self::assertEquals("value5", $nestedArray[0]->getString("key5"));
		self::assertEquals("value6", $nestedArray[1]->getString("key6"));
	}
}