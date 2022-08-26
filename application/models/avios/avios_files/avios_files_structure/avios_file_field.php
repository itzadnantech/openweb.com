<?php

class AviosFileField {

	// 'f' prefix describes a name of field
	// 'h' prefix describes helpers 

	public $fGroup = null;			//  "lowercase and hyphens '-' instead of spaces
	public $fFieldName = null;
	public $fDescription = null;	
	public $fType = null; 			//  N/AN/A  , A/N
	public $fSize = null;			// count of chars
	public $fStartPosition = null;
	public $fEndPosition = null;
	public $fRequiredFlag = null;
	public $fRequiredFlagComment = null;

	// stores required flag for different cases
	// due not using Registration input files we don't implement this for now
	// in feature it can be updated with rule-parser (function)
	public $fRequiredFlagList = array();

	public $fExampleValue = null;
	public $fValue = "";



	// ------------------------------ // ---------------------------

	public $hTypes = array(
			"N", "AN", "A", "A/N", "ANS", "Date", "ND1", "ND2", "ND2!", "ND4", 

			// ANS (Alpha Numerical  Spaces)
			// Date",// IS0 8601  (22 chars)
			// ND$var -> Numeric + decimal $var chars
			// ND1 -> (Up to 1 decimal places) , eg : 1120 or 99.5
			// ND4 -> (Up to 4 decimal places) , eg :  123456789 or 1234.1234
			// ND2! -> (Exactly 2 decimal places)
			// N+   -> only positive


			//  N   - Numeric Characters Only (0-9)
			//  AN  - Alpha Numeric Characters (A-Z0-9) Only
			//  A   - Alpha Characters (A-Z) Only
			//  A/N - A / N Alpha Only or Numeric Only (For awards collected with Avios anonymous cards)
			
			//  TODO : implement A/N type
			//  TODO : implement different ISO types


		); 

	public $hZeroPadded = true;

	public $hRequiredFlags = array(
			"O", "M", "C",

			// O - Optional
			// M - Mandatory
			// C - Conditional
		);


	// TODO : how do we write extended rules for 
	

	public function __construct($inputParams = array()){

		$constructArray = $this->generateEmptyConstructParams();
		$this->mergeConstructArray($constructArray, $inputParams);


		//$defaultConstructArray = $this->generateEmptyConstructParams();
		$this->initPropertiesFromConstructArray($constructArray);
	}

	public function setValue($val){

		// check it's ingres_field_length(result, index)
		// check type 
		// padded with zeroes or spaces if needs

		$val = strval($val);
		//var_dump($val); 
		$val = trim($val);
		if (!$this->validateValueType($val, $this->fType)){

			$this->fValue = null;
			return;
		}


		// check original value
		// TODO : perhapse, later this part can be imporved by 'inner trim' function
		if (strlen($val) > $this->fSize)
			$val = substr($val, 0, $this->fSize);


		// TODO : validate if numeric


		// if numeric and zeropadded true -> put zeroes,
		// else -> put spaces

		$numericTypesArray = array("N"); //, "ND1", "ND2", "ND2!", "ND4"
		// TODO : perhpase, correct this part depends on read/write 


		$fillChar = " ";
		if ( in_array($this->fType, $numericTypesArray) && ($this->hZeroPadded) ){


			$fillChar = "0";
			$val = $this->fillWithChar($val, $this->fSize, $fillChar);
		} else {

			$fillChar = " ";
			$val = $this->fillWithCharReverse($val, $this->fSize, $fillChar);		
		}

		

		////var_dump($this->fType);
		//var_dump($this->hZeroPadded);
		//echo "<br/>";
		//var_dump($val);
		//echo "<hr/>";
		$this->fValue = $val;

	}

	function getValue() {

	    return $this->fValue;
    }

	// validates val type
	public function validateValueType($val, $valType){

		//if (empty($val))
			//return true;

		if (($valType == "ND1") && preg_match("//", ''));
			return true;

		if (($valType == "A/N"))
			return true;

		if (($valType == "Date"))
			return true;


		if (($valType == "N") && preg_match("/^\d+$/", $val))
			return true;

		if (($valType == "A") && preg_match("/^[A-Za-z]+$/", $val))
			return true;

		//if (($valType == "AN") && preg_match("/^[A-Za-z0-9\s]+$/", $val))
		//	return true;

		if ($valType == "AN")
			return true;

		if (($valType == "ANS") && preg_match("/^[A-Za-z0-9 ]+$/", $val))
			return true;


		return false;

	}



	// helper functions
	// -------------------------------------------
	public function fillWithChar($val, $fieldLength, $fillChar = ""){


		$valLength = strlen($val);
		while ($valLength < $fieldLength){

			$val = $fillChar . $val;	
			$valLength = strlen($val);	
		}

		return $val;
	}

	// left padding
	public function fillWithCharReverse($val, $fieldLength, $fillChar = ""){


		$valLength = strlen($val);
		while ($valLength < $fieldLength){

			$val = $val . $fillChar;	
			$valLength = strlen($val);	
		}

		return $val;
	}





	/* function getRequiredOption($regType){
		if (empty($regType))
			return $this->fRequiredFlag
	}*/


	/*
		Generates init array which contains all properties names

	*/

	function generateEmptyConstructParams(){

		// init values
		return array(
					"fGroup" 		 	   => null,
					"fFieldName" 	 	   => null,
					"fDescription"   	   => null,	
					"fType" 		 	   => null,			//  N/AN/A
					"fSize" 		 	   => null,			// count of chars
					"fStartPosition" 	   => null,
					"fEndPosition"   	   => null,
					"fRequiredFlag"        => null,
					"fRequiredFlagComment" => null,
					"fExampleValue"        => null,
					"fValue"			   => null,
					"hZeroPadded"		   => false,

			);
	}

	/*	
		Merge base construct array with input array
		result contains null value for all keys which were not defined inside input array

	*/
	function mergeConstructArray(&$constructArray, &$inputArray){

		// go through all input keys
		// all unnecessary keys will be ignored at the constructor
		foreach ($inputArray as $key=>$val)
			$constructArray[$key] = $val;

	}

	/*
		assigns inner properties from construct array
	*/
	function initPropertiesFromConstructArray(&$constructArray){


		$defaultConstructArray = $this->generateEmptyConstructParams();
		foreach($defaultConstructArray as $key=>$val){
			$this->$key = $constructArray[$key];
	
		}

		$this->setValue($constructArray['fValue']);

	}


}
