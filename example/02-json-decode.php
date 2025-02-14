<?php
use Gt\DataObject\DataObjectBuilder;

require __DIR__ . "/../vendor/autoload.php";

$jsonString = <<<JSON
{
	"name": "Cody",
	"colour": "orange",
	"food": [
		"biscuits",
		"mushrooms",
		"corn on the cob"
	]
}
JSON;

$builder = new DataObjectBuilder();
$obj = $builder->fromObject(json_decode($jsonString));

echo "Hello, ",
	$obj->getString("name"),
	"! Your favourite food is ",
	$obj->getArray("food")[0],
	PHP_EOL;
