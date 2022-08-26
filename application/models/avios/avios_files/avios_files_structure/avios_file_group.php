<?php
class AviosFileGroup{

	public $fileChunkName = null; // ??
	public $groupName = null;
	public $fieldStorage = array();
	public $amountOfFields = 0;



	function __construct($arrayOfFields, $groupName = "", $fileChunkName = ""){

		$this->fieldStorage = $arrayOfFields;
		$this->amountOfFields = count($this->fieldStorage);

		$this->groupName = $groupName;
		$this->fileChunkName = $fileChunkName;

	}


	public function getFieldByName($fieldName){
		// TODO : handle cases where fields have the same name 

		// foreach / for ( if == el[name] )


	}

	public function getFieldByIndex(){



	}





}