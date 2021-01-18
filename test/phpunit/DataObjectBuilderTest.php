<?php
namespace Gt\DataObject\Test;

use Gt\DataObject\AssociativeArrayWithinObjectException;
use Gt\DataObject\DataObjectBuilder;
use Gt\DataObject\DataObject;
use Gt\DataObject\Json\JsonArrayData;
use Gt\DataObject\Json\JsonPrimitiveData;
use Gt\DataObject\ObjectWithinAssociativeArrayException;
use PHPUnit\Framework\TestCase;
use stdClass;

class DataObjectBuilderTest extends TestCase {
	public function testFromObjectSimple() {
		$obj = new StdClass();
		$obj->key1 = "value1";
		$obj->key2 = "value2";

		$sut = new DataObjectBuilder();
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

		$sut = new DataObjectBuilder();
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

		$sut = new DataObjectBuilder();
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

		$sut = new DataObjectBuilder();
		$output = $sut->fromAssociativeArray($array);

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

		$sut = new DataObjectBuilder();
		$output = $sut->fromAssociativeArray($array);

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

		$sut = new DataObjectBuilder();
		$output = $sut->fromAssociativeArray($array);

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

	public function testMixingAssociativeArrayInObjectThrowsError() {
		$object = new StdClass();
		$object->key1 = "value1";
		$object->assoc = [
			"key2" => "value2",
			"key3" => "value3",
		];

		$sut = new DataObjectBuilder();
		self::expectException(AssociativeArrayWithinObjectException::class);
		$sut->fromObject($object);
	}

	public function testMixingObjectInAssociativeArrayThrowsError() {
		$object = new StdClass();
		$object->key2 = "value2";
		$object->key3 = "value3";

		$array = array(
			"key1" => "value1",
			"obj" => $object,
		);
		$sut = new DataObjectBuilder();
		self::expectException(ObjectWithinAssociativeArrayException::class);
		$sut->fromAssociativeArray($array);
	}

	public function testJsonKVP() {
		$jsonString = <<<JSON
		{
			"id": 123,
			"name": "Example"
		}
		JSON;

		$json = json_decode($jsonString);
		$sut = new DataObjectBuilder();
		$dataObject = $sut->fromObject($json);
		self::assertEquals(123, $dataObject->getInt("id"));
		self::assertEquals("Example", $dataObject->getString("name"));
	}

	public function testJsonContainingArray() {
		$jsonString = <<<JSON
		{
			"id": 123,
			"name": "Example",
			"tags": ["test", "data", "json"]
		}
		JSON;

		$json = json_decode($jsonString);
		$sut = new DataObjectBuilder();
		$dataObject = $sut->fromObject($json);
		self::assertEquals(123, $dataObject->getInt("id"));
		self::assertContains("test", $dataObject->getArray("tags"));
		self::assertContains("data", $dataObject->getArray("tags"));
		self::assertContains("json", $dataObject->getArray("tags"));
	}

	public function testJsonContainingArrayWhenDecodedAsArray() {
		$jsonString = <<<JSON
		{
			"id": 123,
			"name": "Example",
			"tags": ["test", "data", "json"]
		}
		JSON;

		$json = json_decode($jsonString, true);
		$sut = new DataObjectBuilder();
		$dataObject = $sut->fromAssociativeArray($json);
		self::assertEquals(123, $dataObject->getInt("id"));
		self::assertContains("test", $dataObject->getArray("tags"));
		self::assertContains("data", $dataObject->getArray("tags"));
		self::assertContains("json", $dataObject->getArray("tags"));
	}

	public function testFromJsonObject() {
		$jsonString = <<<JSON
		{
			"id": 123,
			"name": "Example"
		}
		JSON;

		$json = json_decode($jsonString);
		$sut = new DataObjectBuilder();
		$data = $sut->fromJsonObject($json);

		self::assertEquals(123, $data->getInt("id"));
		self::assertEquals("Example", $data->getString("name"));
	}

	public function testFromJsonObjectArray() {
		$jsonString = <<<JSON
		["one", "two", "three"]
		JSON;

		$json = json_decode($jsonString);
		$sut = new DataObjectBuilder();
		$data = $sut->fromJsonObject($json);

		self::assertCount(3, $data);
		self::assertEquals("one", $data[0]);
		self::assertEquals("two", $data[1]);
		self::assertEquals("three", $data[2]);
	}

	public function testFromJsonObjectArrayNestedObject() {
		$jsonString = <<<JSON
		["one", "two", {
			"id": 123,
			"name": "Example"
		}]
		JSON;

		$json = json_decode($jsonString);
		$sut = new DataObjectBuilder();
		$data = $sut->fromJsonObject($json);

		self::assertCount(3, $data);
		self::assertEquals("one", $data[0]);
		self::assertInstanceOf(DataObject::class, $data[2]);
		self::assertEquals(123, $data[2]->getInt("id"));
		self::assertEquals("Example", $data[2]->getString("name"));
	}

	public function testFromJsonObjectArrayNestedArray() {
		$jsonString = <<<JSON
		["one", "two", {
			"id": 123,
			"name": "Example",
			"tags": ["test", "data", "json"]
		}]
		JSON;

		$json = json_decode($jsonString);
		$sut = new DataObjectBuilder();
		/** @var JsonArrayData $data */
		$data = $sut->fromJsonObject($json);

		self::assertCount(3, $data);
		$tagsArray = $data[2]->getArray("tags");
		self::assertCount(3, $tagsArray);
		self::assertContains("test", $tagsArray);
		self::assertContains("data", $tagsArray);
		self::assertContains("json", $tagsArray);
		self::assertNotContains("name", $tagsArray);
	}

	public function testFromJsonObjectNull() {
		$jsonString = "null";
		$json = json_decode($jsonString);
		$sut = new DataObjectBuilder();
		$data = $sut->fromJsonObject($json);

		self::assertInstanceOf(JsonPrimitiveData::class, $data);
		self::assertNull($data->getValue());
		self::assertEquals("NULL", $data->getType());
	}

	public function testFromJsonObjectBool() {
		$jsonString = "true";
		$json = json_decode($jsonString);
		$sut = new DataObjectBuilder();
		$data = $sut->fromJsonObject($json);

		self::assertInstanceOf(JsonPrimitiveData::class, $data);
		self::assertTrue($data->getValue());
		self::assertEquals("boolean", $data->getType());
	}

	public function testFromJsonObjectInt() {
		$jsonString = "123";
		$json = json_decode($jsonString);
		$sut = new DataObjectBuilder();
		$data = $sut->fromJsonObject($json);

		self::assertInstanceOf(JsonPrimitiveData::class, $data);
		self::assertEquals(123, $data->getValue());
		self::assertEquals("integer", $data->getType());
	}

	public function testFromJsonObjectFloat() {
		$jsonString = "123.456";
		$json = json_decode($jsonString);
		$sut = new DataObjectBuilder();
		$data = $sut->fromJsonObject($json);

		self::assertInstanceOf(JsonPrimitiveData::class, $data);
		self::assertEquals(123.456, $data->getValue());
		self::assertEquals("double", $data->getType());
	}

	public function testFromJsonObjectString() {
		$jsonString = <<<JSON
		"Example!"
		JSON;
		$json = json_decode($jsonString);
		$sut = new DataObjectBuilder();
		$data = $sut->fromJsonObject($json);

		self::assertInstanceOf(JsonPrimitiveData::class, $data);
		self::assertEquals("Example!", $data->getValue());
		self::assertEquals("string", $data->getType());
	}
}