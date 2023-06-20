Structured, type-safe, immutable data transfer.
===============================================

A Data Transfer Objects (DTO) in a programming language is a design pattern that facilitates transfer of data between different layers of an application. This library introduces the `DataObject` class which can be built from an existing associative array or standard object, using the `DataObjectBuilder` class.

***

<a href="https://giub.com/PhpGt/DataObject/actions" target="_blank">
	<img src="https://badge.status.php.gt/dataobject-build.svg" alt="Build status" />
</a>
<a href="https://scrutinizer-ci.com/g/PhpGt/DataObject" target="_blank">
	<img src="https://badge.status.php.gt/dataobject-quality.svg" alt="Code quality" />
</a>
<a href="https://scrutinizer-ci.com/g/PhpGt/DataObject" target="_blank">
	<img src="https://badge.status.php.gt/dataobject-coverage.svg" alt="Code coverage" />
</a>
<a href="https://packagist.org/packages/PhpGt/DataObject" target="_blank">
	<img src="https://badge.status.php.gt/dataobject-version.svg" alt="Current version" />
</a>
<a href="http://www.php.gt/dataobject" target="_blank">
	<img src="https://badge.status.php.gt/dataobject-docs.svg" alt="PHP.Gt/DataObject documentation" />
</a>

A `DataObject` has the following features: 

+ It is **immutable**, meaning that code can't modify the data it represents
+ It provides **type-safe** getters to the contained data
+ It can be **nested** within other `DataObject`s
+ It can be converted to and from associative arrays and standard objects

Usage example
-------------

Load an object into a `DataObject`, then pass to a third party library for processing.

Due to the immutability of the `DataObject` class, there is no risk of the third party library making changes to the contents of the data.

```php
use Gt\DataObject\DataObjectBuilder;

// Create a new Builder and build the DataObject from an associative array.
// For example, data loaded from another remote data source.
$sourceData = [
	"id" => 105,
	"name" => "Edgar Scolmore",
	"address" => [
		"street" => "32 Trestles Lane",
		"town" => "Lensworth",
		"county" => "Scamperingshire",
		"postcode" => "SC41 8PN"
	],
];
$builder = new DataObjectBuilder();
$data = $builder->fromAssociativeArray($sourceData);

// Pass the data to a third party to process it.
ThirdParty::processData($data);

// Now we can use the data ourselves for whatever purpose:
Database::store(
	id: $data->getInt("id"),
	refname: $data->getString("name"),
);
```

Working with JSON data
----------------------

A JSON data structure is almost identical in scope to the DataObject introduced in this repository, with one key difference: JSON data can represent a primitive data type, not always key-value-pairs. Because of this, [PHP.Gt/Json is maintained separately to provide structured, type-safe, immutable JSON objects][json] as an extension to this DataObject repository.

[json]: https://php.gt/json
