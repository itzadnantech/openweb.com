<?php
class AviosFileChunk{

	public $fileChunkName = null;
	public $groupStorage = array();
	public $amountOfGroups = 0;


	function __construct($arrayOfGroups, $name = ""){

		$this->groupStorage = $arrayOfGroups;
		$this->amountOfFields = count($this->groupStorage);
		$this->fileChunkName = $name;
	}


	public function getGroupByName($groupName){
		// TODO : handle cases where fields have the same name 

		// foreach / for ( if == el[name] )


	}

	public function getGroupByIndex(){



	}





}