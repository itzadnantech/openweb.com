<?php

	
class AviosFileAwardConfirmation extends AviosFileAbstract{



	protected $listOfHeaderFields = array( 
										"record-type", "format-version", "partner-code",
										"feed-type", "batch-number", "reissue-number",
										"file-generation-date",
									);

	protected $listOfFooterFields = array(
										"record-type", "physical-record-count",  "logical-record-count",
									); 

	private $listOfDetailFields = array(
										"record-type", "start-date", "record-type", 
										"message-type", "message-code",
									);


	public $headerParams = array();
	public $footerParams = array();
	public $contentParams = array(); // can be multidimensional array

	public $headerChunk = "";
	public $footerChunk = "";
	public $contentChunkCollection = "";


	public $headerChunkStr = "";
	public $footerChunkStr = "";
	public $contentChunkCollectionStr = "";



	// Array which maps type-names to type-codes
  	// e.g. array("heder" => 0,)
	protected $recordTypeMapper = array(

			"header"  => 0,
			"summary" => 1,
			"detail"  => 2,
			"footer"  => 9,

		);

	// Array which maps type-codes to type-names
	protected $reverseRecordTypeMapper = array(

			0 => "header",
			1 => "summary",
			2 => "detail",
			9 => "footer",

		);

	// Array which determines length of each row type (required for validation)
	// e.g. array("header"  => 49,)
	protected $recordLengthMapper = array(

			"header"  => 49,
			"detail"  => 6,
			"footer"  => 17,

		);

	function __construct($format = "00301", $partnerCode = "EXAGB00001", $feed = "ANAINP", $mode = null){

		$this->writeMode     = $mode;
		$this->formatVersion = $format;
		$this->partnerCode   = $partnerCode;
		$this->feedType 	 = $feed;

	}


	// -------------------------------------------------------------------
	// files & helpers


	// AviosFileAbstract::generateEmptyFileObject, AviosFileAbstract::convertRowsToChunks, AviosFileAbstract::validateContentRowTypes, 

	// AviosFileAbstract::setConstHeaderParameters, AviosFileAbstract::setConstFooterParameters, AviosFileAbstract::generateFullContentChunk


	// Generates an empty object 
	public function generateEmptyFileObject(){

		$this->generateFileObjectFromParams(array(), array(array()), array());

	}



	function validateContentRowTypes($arrayOfRows){

		/*
			// staticly validate each row (summary/detail) 


			// 1. validate sequence (each detail should has summary which was recognized previosly)

			// 2. automatically check required fields

		*/
		$rowValidationArray = array();
		$count = count($arrayOfRows);

		for( $i = 0; $i < $count; $i++){

			// current file type contains only 'detail' fields inside content chunk
			$rowValidation[] = $this->validateRowType($arrayOfRows[$i], "detail");
												  
		}

		$validationResult = $this->searchContentMisValidation($rowValidationArray);
		return $validationResult;

	}




	// -------------------------------------------------------------------
	// header 


	protected function setConstHeaderParameters(&$paramStore){

		$paramStore["record-type"] 			= $this->recordTypeMapper["header"];
		$paramStore["format-version"] 		= $this->formatVersion;
		$paramStore["partner-code"] 		= $this->partnerCode;
		$paramStore["feed-type"] 		    = $this->feedType;
		$paramStore["file-generation-date"] = $this->generateCurrentDate();

	}


	protected function generateHeaderGroups(){



		// -------------- Header chunk / Summary group --------------------------
	
		$summaryGroupArray = array();
		$summaryGroupArray[] =  new AviosFileField(

				array(

					"fGroup" 		 	   => "summary",
					"fFieldName" 	 	   => "record-type",
					"fDescription"   	   => "Used to identify the different type of records being sent in a multi-record type feed file.",

					"fType" 		 	   => "N",			     //  N/AN/A
					"fSize" 		 	   => 1, //1			// count of chars
					"fStartPosition" 	   => 1,
					"fEndPosition"   	   => 1, // 1
					"fRequiredFlag"        => "M",
					"fRequiredFlagComment" => null,
					"fExampleValue" 	   => "0",	
					"fValue"			   => $this->headerParams["record-type"],
					"hZeroPadded"		   => true,
			));

		$summaryGroupArray[]  = new AviosFileField(
			
				array(

					"fGroup" 		 	   => "summary",
					"fFieldName" 	 	   => "format-version",
					"fDescription"   	   => "Determines which version of the feed is being used and is provided to support future changes to the feed formats",

					"fType" 		 	   => "N",			     //  N/AN/A
					"fSize" 		 	   => 5, //1			// count of chars
					"fStartPosition" 	   => 2,
					"fEndPosition"   	   => 6, // 1
					"fRequiredFlag"        => "M",
					"fRequiredFlagComment" => null,
					"fExampleValue" 	   => "00301",	
					"fValue"			   => $this->headerParams["format-version"],
					"hZeroPadded"		   => false,
			));


		// -------------- Header chunk / File Details group ---------------------


		$fileDetailsGroupArray = array();
		$fileDetailsGroupArray[] = new AviosFileField(	
			
				array(

					"fGroup" 		 	   => "file-details",
					"fFieldName" 	 	   => "partner-code",
					// -----------
					"fDescription"   	   => "Used to identify the partner that all transactions
within the record belong to.",

					"fType" 		 	   => "AN",			     //  N/AN/A
					"fSize" 		 	   => 10, //1			// count of chars
					"fStartPosition" 	   => 7,
					"fEndPosition"   	   => 16, // 1
					"fRequiredFlag"        => "M",
					"fRequiredFlagComment" => null,
					"fExampleValue" 	   => "ACME000001",	
					"fValue"			   => $this->headerParams["partner-code"],
					"hZeroPadded"		   => true,
			));

		$fileDetailsGroupArray[] = new AviosFileField(	
			
				array(

					"fGroup" 		 	   => "file-details",
					"fFieldName" 	 	   => "feed-type",
					// -----------
					"fDescription"   	   => "Used to identify the type of feed, e.g. New
registrations, awards etc.",

					"fType" 		 	   => "AN",			     //  N/AN/A
					"fSize" 		 	   => 6, //1			// count of chars
					"fStartPosition" 	   => 17,
					"fEndPosition"   	   => 22, // 1
					"fRequiredFlag"        => "M",
					"fRequiredFlagComment" => null,
					"fExampleValue" 	   => "ANAINP",	
					"fValue"			   => $this->headerParams["feed-type"],
					"hZeroPadded"		   => true,
			));

		$fileDetailsGroupArray[]  = new AviosFileField(	
			
				array(

					"fGroup" 		 	   => "file-details",
					"fFieldName" 	 	   => "batch-number",
					// -----------
					"fDescription"   	   => "Used to identify feeds and to ensure correct
processing order. Batch numbers are validated
to ensure that files arrive in sequence and are
incremented by 1 with each incoming feed.
Batch numbers will roll over at 9999, whereby
the sequence number rolls back to 0001.",

					"fType" 		 	   => "N",			     //  N/AN/A
					"fSize" 		 	   => 4, //1			// count of chars
					"fStartPosition" 	   => 23,
					"fEndPosition"   	   => 26, // 1
					"fRequiredFlag"        => "M",
					"fRequiredFlagComment" => null,
					"fExampleValue" 	   => "0001",	
					"fValue"			   => $this->headerParams["batch-number"],
					"hZeroPadded"		   => true,
			));



		$fileDetailsGroupArray[] = new AviosFileField(	
			
				array(

					"fGroup" 		 	   => "file-details",
					"fFieldName" 	 	   => "reissue-number",
					// -----------
					"fDescription"   	   => "Used to provide the ability to resubmit feed files
which received a confirmation file with errors.
Default is 0. Incremented by 1 on resubmission
of a file.
Records being resubmitted as a result of errors
being received in a handback file should use an
incremented batch number and a reissue
number of 0.
Note: The Confirmation and Handback file
resubmission number sent to partners will
correspond to the resubmission number used in
the input file
Note: The Confirmation and Handback file
resubmission number sent to partners will
correspond to the resubmission number used in
the input file",

					"fType" 		 	   => "N",			     //  N/AN/A
					"fSize" 		 	   => 1, //1			// count of chars
					"fStartPosition" 	   => 27,
					"fEndPosition"   	   => 27, // 1
					"fRequiredFlag"        => "M",
					"fRequiredFlagComment" => null,
					"fExampleValue" 	   => "0",	
					"fValue"			   => $this->headerParams["reissue-number"],
					"hZeroPadded"		   => true,
			));



		$fileDetailsGroupArray[] = new AviosFileField(	
			
				array(

					"fGroup" 		 	   => "file-details",
					"fFieldName" 	 	   => "file-generation-date",
					// -----------
					"fDescription"   	   => "The date in which the partner generated the file.",

					"fType" 		 	   => "Date",// IS0 8601 (YYYY-MM-DDThh:mmTZD - includes Time zone adjustment)",			     //  N/AN/A
					"fSize" 		 	   => 22, //1			// count of chars
					"fStartPosition" 	   => 28,
					"fEndPosition"   	   => 49, // 1
					"fRequiredFlag"        => "M",
					"fRequiredFlagComment" => null,
					"fExampleValue" 	   => "2012-05-30T17:09+01:00",	
					"fValue"			   => $this->headerParams["file-generation-date"],
					"hZeroPadded"		   => false,
			));



		// ---------------------  save groups -----------------------------------

		$headerSummaryGroup = new AviosFileGroup($summaryGroupArray, "summary", "header");
		$headerFileDetailsGroup = new AviosFileGroup($fileDetailsGroupArray, "file-details", "header"); 

		return array($headerSummaryGroup, $headerFileDetailsGroup);

	}



	// -------------------------------------------------------------------------------
	// footer



	protected function setConstFooterParameters(&$paramStore){

		$paramStore["record-type"] = $this->recordTypeMapper["footer"];
	}


	protected function generateFooterGroups(){


		$summaryGroupArray = array();
		$summaryGroupArray[] =  new AviosFileField(array(

					"fGroup" 		 	   => "summary",
					"fFieldName" 	 	   => "record-type",
					// -----------
					"fDescription"   	   => "Used to identify the different type of records being sent in a multi-record type feed file.",

					"fType" 		 	   => "N",			     //  N/AN/A
					"fSize" 		 	   => 1, 			// count of chars
					"fStartPosition" 	   => 1,
					"fEndPosition"   	   => 1, 
					"fRequiredFlag"        => "M",
					"fRequiredFlagComment" => "",
					"fExampleValue" 	   => "9",	
					"fValue"			   => $this->footerParams["record-type"],
					"hZeroPadded"		   => true,
			));		


			$summaryGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "summary",
					"fFieldName" 	 	   => "physical-record-count",
					// -----------
					"fDescription"   	   => "The number of lines contained within the file, excluding the header and footer records.",

					"fType" 		 	   => "N",			     //  N/AN/A
					"fSize" 		 	   => 8, 			// count of chars
					"fStartPosition" 	   => 2,
					"fEndPosition"   	   => 9, 
					"fRequiredFlag"        => "M",
					"fRequiredFlagComment" => "",
					"fExampleValue" 	   => "00002000",	
					"fValue"			   => $this->footerParams["physical-record-count"],
					"hZeroPadded"		   => true,
			));				


			$summaryGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "summary",
					"fFieldName" 	 	   => "logical-record-count",
					// -----------
					"fDescription"   	   => "The number of activity/award requests contained within the file i.e. the number of records with a Record Type of 1.",

					"fType" 		 	   => "N",			     //  N/AN/A
					"fSize" 		 	   => 8, 			// count of chars
					"fStartPosition" 	   => 10,
					"fEndPosition"   	   => 17, 
					"fRequiredFlag"        => "M",
					"fRequiredFlagComment" => "",
					"fExampleValue" 	   => "00001000",	
					"fValue"			   => $this->footerParams["logical-record-count"],
					"hZeroPadded"		   => true,
			));		

		

		$footerSummaryGroup = new AviosFileGroup($summaryGroupArray, "summary", "footer");
		return array($footerSummaryGroup);


	}


	// -------------------------------------------------------------------------------
	// content - detail


	private function setConstDetailParameters(&$paramStore){

		$paramStore["record-type"] = "2";

	}

	public function setDetailParams($params){

		$paramStore = array();
		foreach ($this->listOfDetailFields as $key){

			$val = "";
			if (isset($params[$key]))
				$val = $params[$key];
				
			$paramStore[$key] = $val;	
		}

		$this->setConstDetailParameters($paramStore);
		return $paramStore;
	}


	protected function generateDetailGroups($detailValues){


			$summaryGroupArray = array();
			$summaryGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "summary",
					"fFieldName" 	 	   => "record-type",
					// -----------
					"fDescription"   	   => "Used to identify the different type of records being sent in a multi-record type feed file.",

					"fType" 		 	   => "N",			     //  N/AN/A
					"fSize" 		 	   => 1, //1			// count of chars
					"fStartPosition" 	   => 1,
					"fEndPosition"   	   => 1, // 1
					"fRequiredFlag"        => "M",
					"fRequiredFlagComment" => "",
					"fExampleValue" 	   => "1",	
					"fValue"			   => $detailValues["record-type"],
					"hZeroPadded"		   => true,
			));	

			// -------------------------------------------------

				$summaryGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "summary",
					"fFieldName" 	 	   => "message-type",
					// -----------
					"fDescription"   	   => "The type of message.
I=Informational Message
F=File Level Error Message",

					"fType" 		 	   => "A",// IS0 8601 (YYYY-MM-DDThh:mmTZD)",			     //  N/AN/A
					"fSize" 		 	   =>  1, //1			// count of chars
					"fStartPosition" 	   =>  2,
					"fEndPosition"   	   =>  2, // 1
					"fRequiredFlag"        => "M",
					"fRequiredFlagComment" => "",
					"fExampleValue" 	   => "I
											   F",	
					"fValue"			   => $detailValues["message-type"],
					"hZeroPadded"		   => false,
			));	


		
			$summaryGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "summary",
					"fFieldName" 	 	   => "message-code",
					// -----------
					"fDescription"   	   => "The code specifying the message.",

					"fType" 		 	   => "AN",// IS0 8601 (YYYY-MM-DDThh:mmTZD)",			     //  N/AN/A
					"fSize" 		 	   =>  4, //1			// count of chars
					"fStartPosition" 	   =>  3,
					"fEndPosition"   	   =>  6, // 1
					"fRequiredFlag"        => "M",
					"fRequiredFlagComment" => "",
					"fExampleValue" 	   => "0001",	
					"fValue"			   => $detailValues["message-code"],
					"hZeroPadded"		   => false,
			));	

			$detailSummaryGroup = new AviosFileGroup($summaryGroupArray, "summary", "detail");
			return array($detailSummaryGroup);


	 }	



	protected function generateDetailChunk($detailValues){

		$detailValues = $this->setDetailParams($detailValues);
		$detailGroupArray = $this->generateDetailGroups($detailValues);

		return new AviosFileChunk($detailGroupArray, "content--detail");
	}



	protected function generateDetailChunkCollection($arrayOfDetails){


		$detailsChunkCollectionArray = array();
		foreach ($arrayOfDetails as $detail){
		//	$detailsChunkCollectionArray
			$detailsChunkCollectionArray[] = $this->generateDetailChunk($detail);
		}

		return new AviosFileChunk($detailsChunkCollectionArray, "content");
	}




	// -----------------------------------------------------------------------------------
	// content helpers

	public function generateFullContentChunk(){

		$this->contentChunkCollection = $this->generateDetailChunkCollection($this->contentParams);
		return;
	}


	// TODO : check if we need this
	protected function generateSubContentChunk($str, $type){

		//echo "<hr/>";
		//var_dump($type);

		$resultChunk = null;	
		$resultChunk = $this->generateDetailChunk($str);
		$this->fillRegularChunkFromRow($resultChunk, $str); 	 // parse to object		
		return $resultChunk;

	}

}