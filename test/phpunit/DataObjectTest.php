<?php
namespace Gt\DataObject\Test;

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
}