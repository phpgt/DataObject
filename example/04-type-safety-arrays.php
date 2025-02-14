<?php

use Gt\DataObject\DataObject;

require __DIR__ . "/../vendor/autoload.php";

$obj = new DataObject();
$obj = $obj->with("arrayOfData", [
	1,
	2,
	3.14159,
]);

echo "The third element in the array is: ",
	$obj->getArray("arrayOfData", "int")[2], // note the type check of "int"
	PHP_EOL;

/* Output:
The third element in the array is: 3
*/
