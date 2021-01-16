<?php
namespace Gt\DataObject\Test;

use Gt\DataObject\DataObject;
use PHPUnit\Framework\TestCase;

class DataObjectTest extends TestCase {
	public function testGetEmpty() {
		$sut = new DataObject();
		self::assertNull($sut->get("nothing"));
	}
}