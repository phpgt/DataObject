<?php
namespace Gt\DataObject\Test;

use DateTime;
use Gt\DataObject\DataObject;
use PHPUnit\Framework\TestCase;

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
}