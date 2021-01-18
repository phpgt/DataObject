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

	}
}