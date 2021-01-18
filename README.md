Structured, type-safe, immutable data transfer.
===============================================

Data Transfer Objects are a mechanism to facilitate transfer of data between different layers of an application. This library introduces to `DataObject` class which can be built from an existing associative array or standard object, using the `Builder` class.

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

Load a JSON object into a `DataObject`, then pass to a third party library for processing.

Due to the immutability of the `DataObject` class, there is no risk of the third party library making changes to the contents of the data.

```php
// Create a new Builder and build the DataObject from the remote JSON response.
$builder = new Builder();
$data = $builder->fromObject(
	json_decode(file_get_contents("http://example.com/endpoint.json"))
);

// Pass the data to a third party to process it.
ThirdParty::processData($data);

// Now we can use the data ourselves for whatever purpose:
Database::store(
	id: $data->getInt("id"),
	refname: $data->getString("name"),
);
```