<?php

abstract class AviosFileAbstract{

	protected $formatVersion = "";
	protected $partnerCode = "";
	protected $feedType = "";


	public $headerChunk = "";
	public $footerChunk = "";
	public $contentChunkCollection = "";


	protected $fullFileChunkCollection = "";
	protected $writeMode = ""; 		// 	true  ("write") - generates file structure and write it to a file 
									//  false ("read")  - parse existing file and validate it

	public $headerParams = array();
	public $footerParams = array();
	public $contentParams = array(); // can be multidimensional array


	// Array which contains names of all header fields
	protected $listOfHeaderFields = array();

	// Array which contains names of all footer fields
	protected $listOfFooterFields = array();

	// Array which maps type-names to type-codes
  	// e.g. array("heder" => 0,)
	protected $recordTypeMapper = array();

	// Array which maps type-codes to type-names
	protected $reverseRecordTypeMapper = array();

	// Array which determines length of each row type (required for validation)
	// e.g. array("header"  => 49,)
	protected $recordLengthMapper = array();


	abstract protected function __construct($mode);


	// -------------------------------------------------------------------------------
	// files & helpers

	/*
		write flow : 
					generateFileObject() - create object					
					convertChunkToRows() - convert object chunks to string rows
				    writeStrChunksToFile($filePath, $fileStrData) - write all rows to file
	


		read flow : 
					generateEmptyFileObject() - create object
					readStrChunksFromFile($pathWithFileName) 
					convertRowsToChunks() -fill Current Object with chunk vals (incl. validation)

		

	*/


	// -------------------------------------------------------------------------------
	// abstract methods which should be implemented inside child classes	


	/**
	 * 	{write} Generates an empty object and inits all chunks with empty fields  
	 * 
	 */
	abstract public function generateEmptyFileObject();


	/**
	 *  {write} Generates an object and inits all chunks based on the input params
	 *  @param array $headerParams
	 *  @param array $contentParams
	 *  @param array $footerParams
 	 * 
	 */ 
	//abstract public function generateFileObjectFromParams($headerParams, $contentParams, $footerParams);
	public function generateFileObjectFromParams($headerParams, $contentParams, $footerParams){


	
		$this->setHeaderParams($headerParams); 
		$this->generateHeaderChunk();

		// includes 1 empty summary and 1 empty detail
		$this->setContentParams($contentParams);
		$this->generateFullContentChunk();


		$this->setFooterParams($footerParams); 
		$this->generateFooterChunk();

	}

	/**
	 *  {write} Generates an object and inits all chunks based on the input !chunk params
	 *  @param array $headerParams
	 *  @param array $contentParams
	 *  @param array $footerParams
 	 * 
	 */ 
	public function generateFileObjectFromChunks($headerChunk, $contentChunk, $footerChunk){


		$this->headerChunk  = $headerChunk;
		$this->contentChunkCollection = $contentChunk;
		$this->footerChunk  = $footerChunk;
		return;
	}



	/**
	 * 
	 * 	{write} Converts all object chunks to array of rows
	 * 
	 */ 
	//abstract public function convertChunksToRows();
	public function convertChunksToRows(){

		$headerResultStr = $this->fillRowFromRegularChunk($this->headerChunk);
		$contentResultStrArray = $this->fillRowFromNestedChunk($this->contentChunkCollection);
		$footerResultStr = $this->fillRowFromRegularChunk($this->footerChunk);

		// store all available rows
		$fileRows = array();
		$fileRows[] = $headerResultStr;
		$amountOfContentRows = count($contentResultStrArray);
		for($i=0; $i<$amountOfContentRows; $i++){
			$fileRows[] = $contentResultStrArray[$i];
		}
		$fileRows[] = $footerResultStr;


		return $fileRows;
	}

	/**
	 *  {read} Parses and validates array of strings to AviosFileChunk
	 * 
	 *  
	 *  @param  array $chunkCollections
	 * 	@return AviosFileChunk $resultChunkCollection
	 * 
	 */ 
	//abstract public function convertRowsToChunks($rows);

	public function convertRowsToChunks($rows){


		$rowsCount = count($rows);

		// get header str from array + validate it 
		//$headerStr = trim($rows[0]);  // Contains trim
		$headerStr = $rows[0];

		//var_dump($headerStr);
		//die('-end-');

		//$headerValidationResult = $this->validateRowType($headerStr, "header");

		// validate content rows which store between $rows [0] and [len - 1] exist
        //TODO: important file exist blank line in the end, we increase 2 to 3 for get only content!!!!!!!!!!!!!!
		$contentStrs = array_slice($rows, 1, $rowsCount - 3); //ignores header and footer
		//$contentValidationResult = $this->validateContentRowTypes($contentStrs);

		// get footer str and validate it too	
		$footerStr = trim($rows[$rowsCount - 2]); // Contains trim
		$footerStr = $rows[$rowsCount - 2];
		//$footerValidationResult = $this->validateRowType($footerStr, "footer");


		// check validation resuluts for all 3 block (header + content + footer)
		/*$fullValidationArray = array($headerValidationResult,
										$contentValidationResult, 
											$footerValidationResult);


		$fullValidationResult = $this->searchContentMisValidation($fullValidationArray);
*/

		$resultChunkCollection = array();
//		if ($fullValidationResult){

//================================
        $headerChunkResult =
                        $this->fillRegularChunkFromRow($this->headerChunk, $headerStr);
        $resultChunkCollection[] = $headerData = $this->getValuesFromChunk($this->headerChunk, $headerStr);

        $contentChunkResult =
						$this->fillNestedChunkFromRows($this->contentChunkCollection, $contentStrs);
        $resultChunkCollection[]  = $content = $this->getNestedChunkData($contentChunkResult, $contentStrs);

        $footerChunkResult  =
						$this->fillRegularChunkFromRow($this->footerChunk, $footerStr);
        $resultChunkCollection[] = $footerData = $this->getValuesFromChunk($this->footerChunk, $footerStr);

//================================

//		}

		// TODO : returns an empty array
		return $resultChunkCollection;

	}



	/**
	 *   Validates row types of content
	 *  (uses validateRowType() and searchContentMisValidation() ) 
	 * 
	 *  @param array $arrayOfRows
	 *  @return bool $validationResult
	 */ 
	abstract function validateContentRowTypes($arrayOfRows);



	public function mapFileIntoObject($fileName){

		$this->generateEmptyFileObject();
		$fileRows = $this->readStrChunksFromFile($fileName);
		$parseResult = $this->convertRowsToChunks($fileRows);

		$this->generateFileObjectFromChunks($parseResult[0], $parseResult[1], $parseResult[2]);
		return $this;
		// TODO : add to separate function (test) and check it

	}

	public function mapObjectIntoFile($fileName){


		$fileRows = $this->convertChunksToRows();
		$writeResult = $this->writeStrChunksToFile($fileName, $fileRows);
		return $writeResult;


	}

	public function mapObjectIntoJSONFile($fileName){

		//$fileRows = $this->convertChunksToRows();
        $this->headerParams = $this->headerChunk;
        $this->footerParams = $this->footerChunk;
        $this->contentParams = $this->contentChunkCollection;

		$writeResult = $this->writeStrChunksToJSONFile($fileName, $this);
		return $writeResult;


	}


	// -------------------------------------------------------------------
	// header abstract

	/**
	* 	Set constant header parameters as array-property
	* 
	*  @param $paramStore ["param1" => val1, "param" => val2]
	*  @return void
	* 
	*/
	abstract protected function setConstHeaderParameters(&$paramStore);




	/**
	 *  Initializes header fields and unites it in groups
	 *  @return array of AviosFileGroup
	 *  
	 */
	abstract protected function generateHeaderGroups();


	// --------------------------------------------------------------
	// footer abstract

	/**
	* 	Set constant header parameters as array-property
	* 
	*  @param $paramStore ["param1" => val1, "param" => val2]
	*  @return void
	* 
	*/
	abstract protected function setConstFooterParameters(&$paramStore);



	/**
	 *  Initializes footer fields and unites it in groups
	 *  @return array of AviosFileGroup
	 *  
	 */
	abstract protected function generateFooterGroups();


	// ----------------------------------------------------------------
	// content abstract

	/** 
	 * Parses content params and save them to $contentParams
	 * 
	 * 
	 * 
	 */
	abstract protected function generateFullContentChunk();



	/**
	 *  Creates a content chunk depends on type
	 *  this method usually uses generate{RowType}Chunk() and fillRegularChunkFromRow()
	 * 	
	 *  @param string $str
	 *  @param string $type
	 *  
	 * 
	 */ 
	abstract protected function generateSubContentChunk($str, $type);




	// --------------------------------------------------------------------------------



	/**
	 *  {read} Gets a content from file and splits it for rows
	 * 
	 * 	@pathWithFilenMname
	 * 
	 */ 
	public function readStrChunksFromFile($pathWithFileName){

		$arrayOfRows = array();
		if (!file_exists($pathWithFileName))
			return "file not exist";

		$file = fopen($pathWithFileName, "r");
		if (!$file)
			return false;

		$count = 0;
		while(!feof($file)) {
		  $arrayOfRows[] = str_replace("\n", "", fgets($file));
		
		}
		fclose($file);
		return $arrayOfRows;

	}


	/**
	 *  {write} Puts chunk rows into files
	 * 
	 */ 
	public function writeStrChunksToFile($pathWithFileName, $arrayOfRows){


		$file = fopen($pathWithFileName, "w+");
		if (!$file)
			return false;

		$count = 0;
		$str = implode("\n", $arrayOfRows);
		fwrite($file, $str);

		fclose($file);
		return true;

	}

	public function writeStrChunksToJSONFile($pathWithFileName, $obj){


		$file = fopen($pathWithFileName, "w+");
		if (!$file)
			return false;

		$count = 0;
		$jsonData = json_encode($obj);
		fwrite($file, $jsonData);

		fclose($file);
		return true;

	}





	function validateRowType(&$str, $rowKey){

		if ($str[0] != $this->recordTypeMapper[$rowKey])
			return false;

		//if (strlen($str) !=  $this->recordLengthMapper[$rowKey])
		//	return false;

		// TODO : substr to string len

		return true;

	}


	/**
	 *  Gets row's type based on the reverseRecordTypeMapper
	 *  (checks first char of the string)
	 *  
	 * @param string $str - row from physical file
	 * @return string $rowType - type of the 
	 * 
	 */ 
	function getRowType(&$str){

		$rowType = false;
		if (isset($this->reverseRecordTypeMapper[$str[0]]))
			$rowType = $this->reverseRecordTypeMapper[$str[0]];

		return $rowType;

	}


	/**
	 * @param  (array)  $arrayOfValidationResults - contains results of validation for all rows
	 *
	 * @return (bool) $finalValidationResult - returns 'false', if at least 1 validation failed
	 */ 
	function searchContentMisValidation(&$arrayOfValidationResults){

		$finalValidationResult = true;
		$finalValidationResult = array_reduce($arrayOfValidationResults, array($this, "compareValidation"), $finalValidationResult);
		return $finalValidationResult;

	}


	 /**
	 * Calculates logical '&&' through all items to check if it contains 'false' item
 	 * 
	 * @param (bool) $item
	 * @param (int)  $key
	 * @param (bool) $finalValidationResult 
	 * 
	 */ 
	function compareValidation($finalValidationResult, $item){


		$finalValidationResult = ($finalValidationResult && $item);
		return $finalValidationResult;

	}



	/**
	 * 	Generates a proper date IS0 8601 (YYYY-MM-DDThh:mmTZ) (a string of 22 chars)
	 */ 
	public function generateCurrentDate(){

		return date("o-m-d\TH:iP");
	}



	// -------------------------------------------------------------------------------
	// header

	
	/**
	 * 	Parse header params and save them to object property
	 *  @param Array $params   
	 *  @return Array $headerParams
	 */
	public function setHeaderParams($params){

		$paramStore = array();
		foreach ($this->listOfHeaderFields as $key){

			$val = "";
			if (isset($params[$key]))
				$val = $params[$key];
				
			$paramStore[$key] = $val;	
		}

		// should be implemented in an inherited class
		$this->setConstHeaderParameters($paramStore); 
		$this->headerParams = $paramStore;
		return $this->headerParams;
	}


	/**
	 * Generates header chunk and save it as propery ($this->headerChunk)
	 * 
	 */ 
	public function generateHeaderChunk(){


		// should be implemented in an inherited class
		$headerGroupArray = $this->generateHeaderGroups();
		$this->headerChunk = new AviosFileChunk($headerGroupArray, "header");
	}


	// -------------------------------------------------------------------------------
	// footer


	


	/**
	 * 	Parse footer params and save them to object property
	 *  @param Array $params   
	 *  @return Array $footerParams
	 */
	public function setFooterParams($params){

		$paramStore = array();
		foreach ($this->listOfFooterFields as $key){

			// TODO : it can be set separately
			$val = "";
			if (isset($params[$key]))
				$val = $params[$key];
				
			$paramStore[$key] = $val;	
		}

		$this->setConstFooterParameters($paramStore);
		$this->footerParams = $paramStore;
		return $this->footerParams;

	}



	/**
	 * Generates footer chunk and save it as propery ($this->headerChunk)
	 * 
	 */ 
	public function generateFooterChunk(){


		$footerGroupArray = $this->generateFooterGroups();
		$this->footerChunk = new AviosFileChunk($footerGroupArray, "footer");

		// TODO : validate optional params

	}



	// -------------------------------------------------------------------------------
	// content & helpers



	/**
	 * 	Parse content params and save them to object property
	 *  Can be multidimensional. 
	 * 
	 *  @param Array $params   
	 *  @return Array $contentParams
	 */
	public function setContentParams($contentParams){

		// todo : validate params according to the list of fields (detail/summary)
		$this->contentParams = $contentParams;
	}






	/**
	 *  Generates a regular chunk  (which doesn't have nesting) and returns it.
	 * 
	 *  @param   AviosFileChunk  &$chunk - stores chunk's scheme 
	 *  @param   string 		 &$str - stores file row which should be parsed
	 *  @return  AviosFileChunk  $chunk - chunk with filled fields
	 * 
	 */      
	function fillRegularChunkFromRow(&$chunk, &$str){

		// we go through all groups/fields of header	
		// set each field, and collect them into new groups
		// save chunk into class head (property)
		// ---------------------------------------------------
		//if (empty($chunk) || empty($str))
		//	return false;
        $data = array();
		foreach ($chunk->groupStorage as &$group) {

			foreach ($group->fieldStorage as &$field){
			
				$startPosition = $field->fStartPosition - 1;
				$length = $field->fEndPosition - $field->fStartPosition + 1;
				$value = substr($str, $startPosition, $length);
				$field->setValue($value);
				$data[$field->fFieldName] = $field->getValue();

			}

		}

        return $chunk;
	}

	function getValuesFromChunk(&$chunk, &$str = null) {

        $data = array();
        foreach ($chunk->groupStorage as $group) {

            foreach ($group->fieldStorage as $field){

                $data[$field->fFieldName] = $field->getValue();

            }

        }

        return $data;
    }


	/**
	 *  Fills empty chunk collection with fields
	 * 
	 * @param AviosFileChunk &$chunkCollection - chunk which stores other chunks
	 * @param array &$arrayOfStr - array of rstrings with different types
	 * @return AviosFileChunk  $chunkCollectionArray - chunk which stores other chunks with values
	 * 
	 */ 
	function fillNestedChunkFromRows(&$chunkCollection, &$arrayOfStr){


		if (empty($chunkCollection) || empty($arrayOfStr))
			return false;	

	
		$chunkCollectionArray = array();								
		foreach ($arrayOfStr as $str){	
			$rowType = $this->getRowType($str);
			$chunkCollectionArray[] = $this->generateSubContentChunk($str, $rowType);
		}

		return new AviosFileChunk($chunkCollectionArray, "content");
	}

	function getNestedChunkData(&$chunkCollection, &$arrayOfStr) {

        if (empty($chunkCollection) || empty($arrayOfStr))
            return false;

        if (count($chunkCollection) < 2) {
            $chunkCollection = array(
                $chunkCollection
            );
        }

        $data = array();
        foreach ($chunkCollection as &$chank){
            foreach ($chank->groupStorage as &$gchank)
            $data[] = $this->getValuesFromChunk($gchank);
        }
        //echo "<pre>"; var_dump($data); die();
        return $data;
    }

	
	/**
	 *  {write} Processes a regular chunk and saves its values to a properly formatted string
	 * 
	 *  @param AviosFileChunk $chunk - File chunks with stores groups with fileld fields 
	 *  @return string $str - contains fields and their values
	 * 
	 */ // fillRegularChunkFromRow
	public function fillRowFromRegularChunk($chunk = null){

		$str = "";	
		foreach ($chunk->groupStorage as $group) {

			foreach ($group->fieldStorage as $field){	
				$str .= strval($field->fValue);	
			}

		}
		return $str;
	}


	/**
	 *  Processes a nested chunk and saves its values to a properly formatted array of rows
	 * 
	 *  @param AviosFileChunk $chunk  - nested chunk with values
	 *  @return array $str - stores string rows 
	 * 
	 */ 
	public function fillRowFromNestedChunk($chunk){

		$str = array();
		foreach($chunk->groupStorage as $subChunk)	
			$str[] = $this->fillRowFromRegularChunk($subChunk);

		return $str;
	}


	// ----------------------------------------------------------------------------------------
	// tests


		// read from file1 obj1 -> generate obj2 -> write to file1 
	public function readWriteSelfTest($fileName1, $fileName2){

		// read file 1
		// crate obj 
		// write file 2


		$result = $this->mapFileIntoObject($fileName1)->mapObjectIntoFile($fileName2);
		return $result;

	}

	// generate virtual obj -> write to file, -> read from file and generate obj2
	public function generateSelfTest($params, $fileName1, $fileName2){

		// set params
		// generate object
		// write to file
		// call readWriteSelfTest();

		$this->generateFileObjectFromParams($params[0], $params[1], $params[2]);
		$this->mapObjectIntoFile($fileName1);
		//die('ok2');
		$result = $this->readWriteSelfTest($fileName1, $fileName2);
		return $result;


	}

	// {
	// ------ TEST EXAMPLE ----------------
	// $aviosConfirmationFile = new AviosFileAwardConfirmation(); 

	// 1st test
	//$fileName1 = "/some-file-323";
	//$fileName2 = "/some-file-324";
	//$result = $aviosConfirmationFile->readWriteSelfTest(AVIOS_HOME	DIR . $fileName1, AVIOS_HOMEDIR . $fileName2);

	// 2nd test
	
	//$fileName1 = AVIOS_HOMEDIR . "/some-file-3237";
	//$fileName2 = AVIOS_HOMEDIR . "/some-file-3248";
	//$result = $aviosConfirmationFile->generateSelfTest($pramsForTest, $fileName1, $fileName2);

	//var_dump($result);
	// }


	

}	