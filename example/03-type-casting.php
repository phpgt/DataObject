<?php

use Gt\DataObject\DataObject;

require __DIR__ . "/../vendor/autoload.php";

// Set an object with all string values, similar to a web requests:
$obj = new DataObject();
$obj = $obj->with("one", "1");
$obj = $obj->with("two", "two");
$obj = $obj->with("pi", "3.14159");

// Automatically cast to int:

$int1 = $obj->getInt("one");
$int2 = $obj->getInt("two");
$int3 = $obj->getInt("pi");

echo "One: $int1, two: $int2, three: $int3", PHP_EOL;
// Outputs: One: 1, two: 0, three: 3
// Note how the decimal data is lost in the cast to int,
// but how the original data is not lost.
