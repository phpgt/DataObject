<?php
namespace Gt\DataObject\Test;

use DateTime;
use Gt\DataObject\DataObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use TypeError;

class DataObjectTest extends TestCase {
	public function testGetEmpty() {
		$sut = new DataObject();
		self::assertNull($sut->get("nothing"));
	}

	public function testWith() {
		$key = uniqid("key-");
		$value = uniqid("value-");

		$sut = new DataObject();
		$sutOriginal = $sut;

		$sut = $sut->with($key, $value);
		self::assertEquals($value, $sut->get($key));
		self::assertNull($sutOriginal->get($key));
	}

	public function testWithout() {
		$key = uniqid("key-");
		$value = uniqid("value-");

		$sut = new DataObject();

		$sut = $sut->with($key, $value);
		$sutWith = $sut;

		$sut = $sut->without($key);

		self::assertNull($sut->get($key));
		self::assertEquals($value, $sutWith->get($key));
	}

	public function testGetWithString() {
		$sut = new DataObject();
		$sut = $sut->with("example-key", "example-value");
		self::assertEquals("example-value", $sut->get("example-key"));
	}

	public function testGetWithSelf() {
		$inner = new DataObject();
		$inner = $inner->with("nested-key", "nested-value");
		$sut = new DataObject();
		$sut = $sut->with("nested", $inner);
		self::assertEquals("nested-value", $sut->get("nested")->get("nested-key"));
	}

	public function testGetObject() {
		$inner = new DataObject();
		$inner = $inner->with("nested-key", "nested-value");
		$sut = new DataObject();
		$sut = $sut->with("nested", $inner);
		self::assertEquals(
			"nested-value",
			$sut->getObject("nested")->getString("nested-key")
		);
	}

	public function testGetObjectNoMatch() {
		$sut = new DataObject();
		self::assertNull($sut->getObject("nothing"));
	}

	public function testGetStringFromInt() {
		$value = rand(1_000, 9_999);
		$stringValue = (string)$value;

		$sut = new DataObject();
		$sut = $sut->with("example", $value);

		self::assertSame($stringValue, $sut->getString("example"));
	}

	public function testGetStringFromFloat() {
		$value = rand(1_000, 9_999) * 3.14159;
		$stringValue = (string)$value;

		$sut = new DataObject();
		$sut = $sut->with("example", $value);

		self::assertSame($stringValue, $sut->getString("example"));
	}

	public function testGetStringFromBool() {
		$sut = (new DataObject())
			->with("this-is-true", true)
			->with("this-is-false", false);

		self::assertSame("1", $sut->getString("this-is-true"));
		self::assertSame("", $sut->getString("this-is-false"));
	}

	public function testGetStringNull() {
		$sut = new DataObject();
		self::assertNull($sut->getString("nothing"));
	}

	public function testGetIntFromString() {
		$sut = (new DataObject())
			->with("one", "1")
			->with("two", "2")
			->with("pi", "3.14159");

		self::assertSame(1, $sut->getInt("one"));
		self::assertSame(2, $sut->getInt("two"));
		self::assertSame(3, $sut->getInt("pi"));
	}

	public function testGetIntFromFloat() {
		$sut = (new DataObject())->with("pi", 3.14159);
		self::assertSame(3, $sut->getInt("pi"));
	}

	public function testGetIntFromBool() {
		$sut = (new DataObject())
			->with("this-is-true", true)
			->with("this-is-false", false);

		self::assertSame(1, $sut->getInt("this-is-true"));
		self::assertSame(0, $sut->getInt("this-is-false"));
	}

	public function testGetIntNull() {
		$sut = new DataObject();
		self::assertNull($sut->getInt("nothing"));
	}

	public function testGetFloatFromString() {
		$sut = (new DataObject())
			->with("pi", "3.14159");

		self::assertSame(3.14159, $sut->getFloat("pi"));
	}

	public function testGetFloatFromInt() {
		$sut = (new DataObject())
			->with("one", 1);

		self::assertSame(1.00, $sut->getFloat("one"));
	}

	public function testGetFloatFromBool() {
		$sut = (new DataObject())
			->with("this-is-true", true)
			->with("this-is-false", false);

		self::assertSame(1.00, $sut->getFloat("this-is-true"));
		self::assertSame(0.00, $sut->getFloat("this-is-false"));
	}

	public function testGetBoolFromString() {
		$sut = (new DataObject())
			->with("non-empty", "something")
			->with("empty", "");

		self::assertTrue($sut->getBool("non-empty"));
		self::assertFalse($sut->getBool("empty"));
	}

	public function testGetBoolFromInt() {
		$sut = (new DataObject())
			->with("zero", 0)
			->with("one", 1)
			->with("two", 2);

		self::assertFalse($sut->getBool("zero"));
		self::assertTrue($sut->getBool("one"));
		self::assertTrue($sut->getBool("two"));
	}

	public function testGetBoolFromFloat() {
		$sut = (new DataObject())
			->with("zero", 0.00)
			->with("pi", 3.14159);

		self::assertFalse($sut->getBool("zero"));
		self::assertTrue($sut->getBool("pi"));
	}

	public function testGetDateTimeFromInt() {
		$sut = (new DataObject())
			->with("epoch", 0)
			->with("birthday", 576264065);

		$epochDateTime = new DateTime();
		$epochDateTime->setTimestamp(0);
		self::assertEquals($epochDateTime, $sut->getDateTime("epoch"));

		$birthdayDateTime = new DateTime("5th April 1988 17:21:05");
		self::assertEquals($birthdayDateTime, $sut->getDateTime("birthday"));
	}

	public function testGetDateTimeFromFloat() {
		$sut = (new DataObject())
			->with("precise-time", 576264065.000105);

		$dateTime = $sut->getDateTime("precise-time");
		self::assertEquals(105, $dateTime->format("u"));
	}

	public function getDateTimeFromString() {
		$epochDateString = "1st January 1970 00:00:00";
		$birthdayDateString = "5th April 1988 17:21:05";

		$sut = (new DataObject())
			->with("epoch", $epochDateString)
			->with("birthday", $birthdayDateString);

		self::assertEquals(
			$epochDateString,
			$sut->getDateTime("epoch")->format("jS M Y H:i:s")
		);
		self::assertEquals(
			$birthdayDateString,
			$sut->getDateTime("birthday")->format("jS M Y H:i:s")
		);
	}

	public function testAsArray() {
		$doubleNested = (new DataObject())
			->with("leaf-key-1", "leaf-value-1")
			->with("leaf-key-2", "leaf-value-2");

		$nested = (new DataObject())
			->with("score", 77.4)
			->with("code", "AAA")
			->with("nested-data", $doubleNested);

		$sut = (new DataObject())
			->with("name", "example")
			->with("id", 123)
			->with("data", $nested);

		$array = $sut->asArray();
		self::assertArrayHasKey("id", $array);
		self::assertArrayHasKey("name", $array);
		self::assertArrayHasKey("data", $array);

		self::assertIsArray($array["data"]);
		self::assertArrayHasKey("score", $array["data"]);
		self::assertArrayHasKey("code", $array["data"]);

		self::assertIsArray($array["data"]["nested-data"]);
		self::assertArrayHasKey("leaf-key-1", $array["data"]["nested-data"]);
		self::assertArrayHasKey("leaf-key-2", $array["data"]["nested-data"]);
	}

	public function testAsObject() {
		$doubleNested = (new DataObject())
			->with("leafKey1", "leaf-value-1")
			->with("leafKey2", "leaf-value-2");

		$nested = (new DataObject())
			->with("score", 77.4)
			->with("code", "AAA")
			->with("nestedData", $doubleNested);

		$sut = (new DataObject())
			->with("name", "example")
			->with("id", 123)
			->with("data", $nested);

		$object = $sut->asObject();
		self::assertEquals(123, $object->id);
		self::assertEquals("example", $object->name);
		self::assertIsObject($object->data);
		self::assertEquals(77.4, $object->data->score);
		self::assertEquals("AAA", $object->data->code);
		self::assertIsObject($object->data->nestedData);
		self::assertEquals("leaf-value-1", $object->data->nestedData->leafKey1);
		self::assertEquals("leaf-value-2", $object->data->nestedData->leafKey2);
	}

	public function testContains() {
		$sut = (new DataObject())
			->with("name", "example")
			->with("id", 123);

		self::assertTrue($sut->contains("name"));
		self::assertTrue($sut->contains("id"));
		self::assertFalse($sut->contains("address"));
	}

	public function testTypeof() {
		$obj = new StdClass();
		$dateTime = new DateTime();

		$sut = (new DataObject())
			->with("name", "example")
			->with("id", 123)
			->with("size", 2_347.467)
			->with("isSecure", true)
			->with("container", $obj)
			->with("dispatchDate", $dateTime)
			->with("nothing", null);

		self::assertEquals("string", $sut->typeof("name"));
		self::assertEquals("int", $sut->typeof("id"));
		self::assertEquals("float", $sut->typeof("size"));
		self::assertEquals("bool", $sut->typeof("isSecure"));
		self::assertEquals("stdClass", $sut->typeof("container"));
		self::assertEquals("DateTime", $sut->typeof("dispatchDate"));
		self::assertEquals("null", $sut->typeof("nothing"));
		self::assertNull($sut->typeof("address"));
	}

	public function testJsonSerialize() {
		$obj = new StdClass();
		$obj->nestedKey = "nestedValue";
		$dateTime = new DateTime();

		$sut = (new DataObject())
			->with("name", "example")
			->with("id", 123)
			->with("size", 2_347.467)
			->with("isSecure", true)
			->with("container", $obj)
			->with("dispatchDate", $dateTime)
			->with("nothing", null);
		$json = json_encode($sut);

		self::assertStringContainsString('"name":"example"', $json);
		self::assertStringContainsString('"id":123', $json);
		self::assertStringContainsString('"size":2347.467', $json);
		self::assertStringContainsString('"isSecure":true', $json);
		self::assertStringContainsString('"container":{"nestedKey":"nestedValue"}', $json);
		self::assertStringContainsString('"nothing":null', $json);

		$obj = json_decode($json);
		self::assertStringContainsString(
			$dateTime->format("Y-m-d H:i:s.u"),
			$obj->dispatchDate->date
		);
	}

	public function testGetArrayFixedTypes() {
		$timestampArray = [
			49997,
			50000,
			49999,
			50004,
			50001,
		];
		$sut = (new DataObject())
			->with("timestamps", $timestampArray);
		$array = $sut->getArray("timestamps", "int");
		foreach($array as $i => $value) {
			self::assertIsInt($value);
		}

		self::assertEquals(count($timestampArray), $i + 1);
	}

	public function testGetArrayFixedTypesMismatch() {
		$timestampArray = [
			49997,
			50000,
			49999.0000000000000000000000001, // How did this get here?
			50004,
			50001,
		];
		$sut = (new DataObject())
			->with("timestamps", $timestampArray);
		self::expectException(TypeError::class);
		self::expectExceptionMessage("Value 49999 is expected to be of type int");
		$sut->getArray("timestamps", "int");
	}
}
