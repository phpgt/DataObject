<?php
namespace Gt\DataObject;

use Gt\DataObject\Json\JsonArrayData;
use Gt\DataObject\Json\JsonKvpData;
use Gt\DataObject\Json\JsonPrimitiveData;

class DataObjectBuilder {
	public function fromObject(
		object $input,
		string $dataObjectClass = DataObject::class
	):DataObject {
		/** @var DataObject $dataObject */
		$dataObject = new $dataObjectClass();

		foreach($input as $key => $value) {
			if(is_object($value)) {
				$value = $this->fromObject($value);
			}
			elseif(is_array($value)) {
				if(is_int(key($value))) {
					array_walk($value, function(&$element) {
						if(is_object($element)) {
							$element = $this->fromObject($element);
						}
					});
				}
				else {
					throw new AssociativeArrayWithinObjectException();
				}
			}

			$dataObject = $dataObject->with($key, $value);
		}

		return $dataObject;
	}

	public function fromAssociativeArray(array $input):DataObject {
		$dataObject = new DataObject();

		foreach($input as $key => $value) {
			if(is_array($value)) {
				if(is_int(key($value))) {
					// Indexed array.
					array_walk($value, function(&$element) {
						if(is_array($element)) {
							$element = $this->fromAssociativeArray($element);
						}
					});
				}
				else {
					// Associative array.
					$value = $this->fromAssociativeArray($value);
				}
			}
			elseif(is_object($value)) {
				throw new ObjectWithinAssociativeArrayException();
			}

			$dataObject = $dataObject->with($key, $value);
		}

		return $dataObject;
	}

	public function fromJsonObject(
		object|array|string|int|float|bool|null $json
	):JsonKvpData|JsonArrayData|JsonPrimitiveData|null {
		$jsonData = null;

		if(is_object($json)) {
			/** @var JsonKvpData $jsonData */
			$jsonData = $this->fromObject(
				$json,
				JsonKvpData::class
			);
		}
		elseif(is_array($json)) {
			$jsonData = $this->fromIndexedArray(
				$json
			);
		}
		else {
			$jsonData = (new JsonPrimitiveData())->withValue($json);
		}

		return $jsonData;
	}

	private function fromIndexedArray(array $json):JsonArrayData {
		$jsonData = new JsonArrayData();

		foreach($json as $key => $value) {
			if(is_object($value)) {
				$value = $this->fromObject($value);
			}

			$jsonData = $jsonData->with($key, $value);
		}

		return $jsonData;
	}
}