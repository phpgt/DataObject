<?php
namespace Gt\DataObject;

class DataObjectBuilder {
	public function fromObject(
		object $input,
		string $dataObjectClass = DataObject::class
	):DataObject {
		/** @var DataObject $dataObject */
		$dataObject = new $dataObjectClass();

		foreach($input as $key => $value) {
			if(is_object($value)) {
				$value = $this->fromObject($value, $dataObjectClass);
			}
			elseif(is_array($value)) {
				if(empty($value)) {
					$value = [];
				}
				elseif(is_int(key($value))) {
					array_walk($value, function(&$element)use($dataObjectClass) {
						if(is_object($element)) {
							$element = $this->fromObject($element, $dataObjectClass);
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

	/** @param mixed[] $input */
	public function fromAssociativeArray(array $input):DataObject {
		$dataObject = new DataObject();

		foreach($input as $key => $value) {
			if(is_array($value)) {
				if(empty($value)) {
					$value = [];
				}
				elseif(is_int(key($value))) {
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
}
