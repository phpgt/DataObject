<?php
namespace Gt\DataObject;

class Builder {
	public function fromObject(object $input):DataObject {
		$dataObject = new DataObject();

		foreach($input as $key => $value) {
			if(is_object($value)) {
				$value = $this->fromObject($value);
			}
			elseif(is_array($value)) {
				array_walk($value, function(&$element) {
					$element = $this->fromObject($element);
				});
			}

			$dataObject = $dataObject->with($key, $value);
		}

		return $dataObject;
	}

	public function fromArray(array $input):DataObject {
		$dataObject = new DataObject();

		foreach($input as $key => $value) {
			if(is_array($value)) {
				if(is_int(key($value))) {
					// Indexed array.
					array_walk($value, function(&$element) {
						$element = $this->fromArray($element);
					});
				}
				else {
					// Associative array.
					$value = $this->fromArray($value);
				}
			}

			$dataObject = $dataObject->with($key, $value);
		}

		return $dataObject;
	}
}