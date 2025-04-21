<?php

use Gt\DataObject\DataObject;

require __DIR__ . "/../vendor/autoload.php";

$obj = (new DataObject())
	->with("name", "Cody")
	->with("colour", "orange")
	->with("food", [
		"biscuits",
		"mushrooms",
		"corn on the cob",
	]);

echo json_encode($obj);
