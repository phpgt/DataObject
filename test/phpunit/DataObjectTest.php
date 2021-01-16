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
}