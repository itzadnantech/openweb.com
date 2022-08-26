<?php
class AviosFileAwardInput extends AviosFileAbstract{//extends AviosFileAbstract{


	/*
		Partner Code: EXAGB00001
		Campaign Code: 00001GBEXA
		Standard Billing Code: GBEXASTAND
		Bonus Billing Code: GBEXABONUS

	*/


	protected $listOfHeaderFields = array( 
										"record-type", "format-version", "partner-code",
										"feed-type", "batch-number", "reissue-number",
										"file-generation-date",
									);

	protected $listOfFooterFields = array(

										"record-type", "physical-record-count", "logical-record-count",
										"total-bonus-loyalty-points-avios-awarded",
										"total-loyalty-points-avios-awarded",

	); 


	protected $listOfSummaryFields = array( 

				"record-type", "transaction-type", "credit-debit", 
				"transaction-date", "debit-type", "debit-transaction-reference", 
				"sub-partner-code", "partner-loyalty-programme-member-id",
				"partner-transaction-reference", "campaign-code", "business-identifier-сode",
				"business-name", "loyalty-programme-name", "loyalty-programme-member-id",
				"member-surname", "member-surname-2", "partner-captured-tier-status",
				"partner-captured-join-date", "partner-capture-method", "partner-location-code-1",
				"partner-location-code-2", "partner-location-code-3", 
				"loyalty-programme-currency-stock-code",
				"payment-method", "loyalty-programme-currency", "transaction-currency",
				"total-transaction-value", "total-loyalty-points", "total-bonus-loyalty-points",
				"qualifies-for-bonus", "standard-loyalty-points-billing-code",
				"bonus-loyalty-points-billing-code", "award-description",
		);


	private $listOfDetailFields = array(
			"record-type", "start-date", "end-date", "duration-unit", "duration-value",
			"product-category-code", "sub-product-category-code", "product-class-code",
			"tariff-code", "quantity", "transaction-value",

		);



	// mapper for fields



	public $headerParams = array();
	public $footerParams = array();
	public $contentParams = array(); // can be multidimensional array

	public $headerChunk = "";
	public $footerChunk = "";
	public $contentChunkCollection = "";


	public $headerChunkStr = "";
	public $footerChunkStr = "";
	public $contentChunkCollectionStr = "";

	///private contentChunkCollection (abstract)

	// for 1 to amount of SummaryChunks do
	//	foreach ($this->summaryChunkCollection as $summaryChunk){

			/* [
					0 => [
							summary
							arrayOfDetails => [0,1,2]

						]

					1 => [
							summry 
							arrayOfDetails => []	

						]	
				]

			*/


	protected $recordTypeMapper = array(

			"header"  => 0,
			"summary" => 1,
			"detail"  => 2,
			"footer"  => 9,

		);

	protected $reverseRecordTypeMapper = array(

			0 => "header",
			1 => "summary",
			2 => "detail",
			9 => "footer",

		);


	protected $recordLengthMapper = array(

			"header"  => 49,
			"summary" => 486,
			"detail"  => 123,
			"footer"  => 41,

		);


	function __construct($mode = null, $format = "00301", $partner = "OPNZA00001", $type = "ANAINP"){

		$this->writeMode     = $mode;
		$this->formatVersion = $format;
		$this->partnerCode   = $partner;
		$this->feedType 	 = $type;

	}


	// -------------------------------------------------------------------
	// files & helpers

	public function generateEmptyFileObject(){


		$this->generateFileObjectFromParams(array(),array( array("summary" => array(), "details" => array( array() ) ) ), array());

	}

	// TODO : check!
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


	

	// TODO : check whole function
	// $arrayOfRows can be nested
	function validateContentRowTypes($arrayOfRows){

		/*
			// staticly validate each row (summary/detail) 


			// 1. validate sequence (each detail should has summary which was recognized previosly)

			// 2. automatically check required fields

		*/
		$rowValidationArray = array();
		$count = count($arrayOfRows);

		for( $i = 0; $i < $count; $i++){

			//$arrayOfRows[$i] = trim($arrayOfRows[$i]);

			// check row as "summary" field, then (if false) check row as "detail" field
			$rowValidation = $this->validateRowType($arrayOfRows[$i], "summary");
			$rowValidationArray[] = ($rowValidation) ? $rowValidation
											  : $this->validateRowType($arrayOfRows[$i], "detail");											  
		}


		$rowValidationArray[1] = true; //false
 		$rowValidationArray[] = true;

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

			$summaryGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "summary",
					"fFieldName" 	 	   => "total-bonus-loyalty-points-avios-awarded",
					// -----------
					"fDescription"   	   => "The total amount of bonus loyalty points
(Avios) awarded within the feed. Will be
used by the hub to validate sent records.
Note: This will be the balance from adding
up credit and debit bonus awards, in the
instance this calculates to a negative
amount, the first character should be
provided with a negative sign. For example a
negative 400 avios award should be
provided as -00000000400",

					"fType" 		 	   => "N",			     //  N/AN/A
					"fSize" 		 	   => 12, 			// count of chars
					"fStartPosition" 	   => 18,
					"fEndPosition"   	   => 29, 
					"fRequiredFlag"        => "M",
					"fRequiredFlagComment" => "",
					"fExampleValue" 	   => "000020000000
000000000000
-00000000400",	
					"fValue"			   => $this->footerParams["total-bonus-loyalty-points-avios-awarded"],
					"hZeroPadded"		   => true,
			));				


			$summaryGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "summary",
					"fFieldName" 	 	   => "total-loyalty-points-avios-awarded",
					// -----------
					"fDescription"   	   => "The total amount of bonus loyalty points
(Avios)awarded within the feed (inclusive of
Bonus). Will be used by the hub to validate
sent records.
Note: This will be the balance from adding
up credit and debit total awards, in the
instance this calculates to a negative
amount, the first character should be
provided with a negative sign. For example a
negative 400 avios award should be
provided as -00000000400",

					"fType" 		 	   => "N",			     //  N/AN/A
					"fSize" 		 	   => 12, 			// count of chars
					"fStartPosition" 	   => 30,
					"fEndPosition"   	   => 41, 
					"fRequiredFlag"        => "M",
					"fRequiredFlagComment" => "",
					"fExampleValue" 	   => "000040000000
000000000000
-00000000400",	
					"fValue"			   => $this->footerParams["total-loyalty-points-avios-awarded"],
					"hZeroPadded"		   => true,
			));				


		$footerSummaryGroup = new AviosFileGroup($summaryGroupArray, "summary", "footer");
		return array($footerSummaryGroup);


	}


	// -------------------------------------------------------------------------------
	// content - summary


	private function setConstSummaryParameters(&$paramStore){

		$paramStore["record-type"] = "1";

	}


	public function setSummaryParams($params){

		$paramStore = array();
		foreach ($this->listOfSummaryFields as $key){

			$val = "";
			if (isset($params[$key]))
				$val = $params[$key];
				
			$paramStore[$key] = $val;	
		}

		$this->setConstSummaryParameters($paramStore);

		//var_dump($paramStore); die();

		return $paramStore;
	}



	protected function generateSummaryGroups($summaryValues){

		
			$summaryGroupArray = array();
			$summaryGroupArray[] = new AviosFileField(	
			
				array(

					"fGroup" 		 	   => "summary",
					"fFieldName" 	 	   => "record-type",
					// -----------
					"fDescription"   	   => "Used to identify the different type of records being sent in a multi-record type feed file.",

					"fType" 		 	   => "N",			     //  N/AN/A
					"fSize" 		 	   => 1, //1			// count of chars
					"fStartPosition" 	   => 1,
					"fEndPosition"   	   => 1, // 1
					"fRequiredFlag"        => "M",
					"fRequiredFlagComment" => null,
					"fExampleValue" 	   => "1",	
					"fValue"			   => $summaryValues["record-type"],
					"hZeroPadded"		   => false,
			));

			$summaryGroupArray[] = new AviosFileField(	
			
				array(

					"fGroup" 		 	   => "summary",
					"fFieldName" 	 	   => "transaction-type",
					// -----------
					"fDescription"   	   => "This field will be used to determine if the record is an Activity
record (that needs the Award to be calculated) or an Award
record with or without accompanying Activity information.
01 - Activity Transaction: At least one activity record must be
provided. Activity records will be processed against earning rules
inorder to calculate the appropriate award.
02 - Award with Activity information: An Avios award is
provided with activity information which will be used for MI
purposes only.
03 - Award without Activity information: Only an Avios award is
provided.",

					"fType" 		 	   => "AN",			     //  N/AN/A
					"fSize" 		 	   => 2, //1			// count of chars
					"fStartPosition" 	   => 2,
					"fEndPosition"   	   => 3, // 1
					"fRequiredFlag"        => "M",
					"fRequiredFlagComment" => null,
					"fExampleValue" 	   => "01 (Activity)
					02 (Award - with Activity information)
					03 (Award - without Activity information)",	
					"fValue"			   => $summaryValues["transaction-type"],
					"hZeroPadded"		   => false,
			));


			$summaryGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "summary",
					"fFieldName" 	 	   => "credit-debit",
					// -----------
					"fDescription"   	   => "This field defines if the transaction is a \"Credit\" to or a \"Debit\"
from the members account. Debit transactions are typically used
to resolve incorrect processing.
C - Credit
D - Debit",

					"fType" 		 	   => "A",			     //  N/AN/A
					"fSize" 		 	   => 1, //1			// count of chars
					"fStartPosition" 	   => 4,
					"fEndPosition"   	   => 4, // 1
					"fRequiredFlag"        => "M",
					"fRequiredFlagComment" => null,
					"fExampleValue" 	   => "C
					D",	
					"fValue"			   => $summaryValues["credit-debit"],
					"hZeroPadded"		   => false,
			));


			$summaryGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "summary",
					"fFieldName" 	 	   => "transaction-date",
					// -----------
					"fDescription"   	   => "The date/time the transaction took place within the Partners
systems.",

					"fType" 		 	   => "Date",// IS0 8601 (YYYY-MM-DDThh:mmTZD",			     //  N/AN/A
					"fSize" 		 	   => 22, //1			// count of chars
					"fStartPosition" 	   => 5,
					"fEndPosition"   	   => 26, // 1
					"fRequiredFlag"        => "M",
					"fRequiredFlagComment" => null,
					"fExampleValue" 	   => "2012-05-30T17:09+01:00",	
					"fValue"			   => $summaryValues["transaction-date"],
					"hZeroPadded"		   => false,
			));				


			$summaryGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "summary",
					"fFieldName" 	 	   => "debit-type",
					// -----------
					"fDescription"   	   => "Determines the type of debit. Used by partners to apply partial
or full debits of a previously sent award.
Supported Types:
F - Full",

					"fType" 		 	   => "A",			     //  N/AN/A
					"fSize" 		 	   => 1, //1			// count of chars
					"fStartPosition" 	   => 27,
					"fEndPosition"   	   => 27, // 1
					"fRequiredFlag"        => "C",
					"fRequiredFlagComment" => "(Mandatory if Debit Transaction)",
					"fExampleValue" 	   => "F",	
					"fValue"			   => $summaryValues["debit-type"],
					"hZeroPadded"		   => false,
			));		


				$summaryGroupArray[] = new AviosFileField(array(
			//	$a = new AviosFileField(array(

					"fGroup" 		 	   => "summary",
					"fFieldName" 	 	   => "debit-transaction-reference",
					// -----------
					"fDescription"   	   => "Reference to the partner reference provided for the original
credit transaction.",

					"fType" 		 	   => "AN",			     //  N/AN/A
					"fSize" 		 	   => 30, //1			// count of chars
					"fStartPosition" 	   => 28,
					"fEndPosition"   	   => 57, // 1
					"fRequiredFlag"        => "C",
					"fRequiredFlagComment" => "(Loyalty Programme Dependent)",
					"fExampleValue" 	   => "ABC1234567890123456789
01234567",	
					"fValue"			   => $summaryValues["debit-transaction-reference"],
					"hZeroPadded"		   => false,
			));


			// ----------------------------------------------	

			
				$partnerGroupArray = array();
			    $partnerGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "partner",
					"fFieldName" 	 	   => "sub-partner-code",
					// -----------
					"fDescription"   	   => "A sub category of the partner code, used to identify
organisational units within the partner's organisation.",

					"fType" 		 	   => "AN",			     //  N/AN/A
					"fSize" 		 	   => 20, //1			// count of chars
					"fStartPosition" 	   => 58,
					"fEndPosition"   	   => 77, // 1
					"fRequiredFlag"        => "O",
					"fRequiredFlagComment" => "",
					"fExampleValue" 	   => "",	
					"fValue"			   => $summaryValues["sub-partner-code"],
					"hZeroPadded"		   => false,
			));		



				$partnerGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "partner",
					"fFieldName" 	 	   => "partner-loyalty-programme-member-id",
					// -----------
					"fDescription"   	   => "The customer's membership number within the partner's
scheme. One of Partner Loyalty Programme Member ID or
Loyalty Programme Member ID must be provided.
(Note: Should not be zero padded)",

					"fType" 		 	   => "N",			     //  N/AN/A
					"fSize" 		 	   => 24, //1			// count of chars
					"fStartPosition" 	   => 78,
					"fEndPosition"   	   => 101, // 1
					"fRequiredFlag"        => "C",
					"fRequiredFlagComment" => "(One of Partner
Loyalty Programme
Member ID or Loyalty
Programme Member
ID is required.)",
					"fExampleValue" 	   => "123456",	
					"fValue"			   => $summaryValues["partner-loyalty-programme-member-id"],
					"hZeroPadded"		   => false,
			));			

				$partnerGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "partner",
					"fFieldName" 	 	   => "partner-transaction-reference",
					// -----------
					"fDescription"   	   => "A unique identifier generated and provided by the partner to
allow checking for duplicate transactions and a means for
partners to correlate returned records with records sent to the
hub. Provided references should be unique across all award
records sent to the hub.",

					"fType" 		 	   => "AN",			     //  N/AN/A
					"fSize" 		 	   => 30, //1			// count of chars
					"fStartPosition" 	   => 102,
					"fEndPosition"   	   => 131, // 1
					"fRequiredFlag"        => "M",
					"fRequiredFlagComment" => "",
					"fExampleValue" 	   => "ABC1234567890123456789
01234567",	
					"fValue"			   => $summaryValues["partner-transaction-reference"],
					"hZeroPadded"		   => false,
			));			
			


				$partnerGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "partner",
					"fFieldName" 	 	   => "campaign-code",
					// -----------
					"fDescription"   	   => "A code that identifies the partner campaign relating to this
activity or award.",

					"fType" 		 	   => "AN",			     //  N/AN/A
					"fSize" 		 	   => 10, //1			// count of chars
					"fStartPosition" 	   => 132,
					"fEndPosition"   	   => 141, // 1
					"fRequiredFlag"        => "M",
					"fRequiredFlagComment" => "",
					"fExampleValue" 	   => "1",	
					"fValue"			   => $summaryValues["campaign-code"],
					"hZeroPadded"		   => false,
			));	

				$partnerGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "partner",
					"fFieldName" 	 	   => "business-identifier-сode",
					// -----------
					"fDescription"   	   => "A code used to provide additional information relating to the
partner product or service. For financial partners this could be
used to identify the banking institution for co-branded cards
Example Code Formats:
ISO-13616 - BBAN Bank Identifier Code (Between 2 and 10
digits)
ISO-9362 - Business Identifier Code",

					"fType" 		 	   => "AN",			     //  N/AN/A
					"fSize" 		 	   => 24, //1			// count of chars
					"fStartPosition" 	   => 142,
					"fEndPosition"   	   => 165, // 1
					"fRequiredFlag"        => "O",
					"fRequiredFlagComment" => "",
					"fExampleValue" 	   => "2038
					DEUT",	
					"fValue"			   => $summaryValues["business-identifier-сode"],
					"hZeroPadded"		   => false,
			));	

			$partnerGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "partner",
					"fFieldName" 	 	   => "business-name",
					// -----------
					"fDescription"   	   => "A trading name used by the partner to sell goods and services.
For hotel partners this could be used to provide the name of the
hotel.",

					"fType" 		 	   => "AN",			     //  N/AN/A
					"fSize" 		 	   => 30, //1			// count of chars
					"fStartPosition" 	   => 166,
					"fEndPosition"   	   => 195, // 1
					"fRequiredFlag"        => "O",
					"fRequiredFlagComment" => "",
					"fExampleValue" 	   => "Hilton",	
					"fValue"			   => $summaryValues["business-name"],
					"hZeroPadded"		   => false,
			));		

		// -----------------------------------------------------------		

		/*
			(Where possible, the partner should capture this information by swiping the membership card. )
		*/	

		$loyaltyProgrammeGroupArray = array();	
		$loyaltyProgrammeGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "loyalty-programme",
					"fFieldName" 	 	   => "loyalty-programme-name",
					// -----------
					"fDescription"   	   => "The Loyalty Programme that the submitted membership account
is registered with.
To be provided when an Loyalty Programme Member ID is being
sent.
Should not be provided when only a Partner Loyalty Programme
Member ID is being sent.
AVIOS - Avios TRP
BAEC - British Airways Exec Club
IBPL - Iberia Plus FFP
ISSLP – Meridiana Club FFP",

					"fType" 		 	   => "AN",			     //  N/AN/A
					"fSize" 		 	   => 5, //1			// count of chars
					"fStartPosition" 	   => 196,
					"fEndPosition"   	   => 200, // 1
					"fRequiredFlag"        => "C",
					"fRequiredFlagComment" => "(To be provided only when a Member Number is sent)",
					"fExampleValue" 	   => "AVIOS
BAEC
IBPL
ISSLP",	
					"fValue"			   => $summaryValues["loyalty-programme-name"],
					"hZeroPadded"		   => false,
			));		


			$loyaltyProgrammeGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "loyalty-programme",
					"fFieldName" 	 	   => "loyalty-programme-member-id",
					// -----------
					"fDescription"   	   => "The Loyalty Programme membership number. One of the
Partner Loyalty Programme Member ID or Loyalty Programme
Member ID must be provided.
Note: Should not be zero padded",

					"fType" 		 	   => "N",			     //  N/AN/A
					"fSize" 		 	   => 24, //1			// count of chars
					"fStartPosition" 	   => 201,
					"fEndPosition"   	   => 224, // 1
					"fRequiredFlag"        => "C",
					"fRequiredFlagComment" => "(One of Partner Loyalty Programme Member ID or Loyalty Programme Member ID is required.)",
					"fExampleValue" 	   => "1111222233334444",	
					"fValue"			   => $summaryValues["loyalty-programme-member-id"],
					"hZeroPadded"		   => false,
			));		

				$loyaltyProgrammeGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "loyalty-programme",
					"fFieldName" 	 	   => "member-surname",
					// -----------
					"fDescription"   	   => "The member/customer Surname. This data may be used by the
Loyalty Programmes to validate that the Member ID is for the
correct person but its primary purpose is to support Missing
Avios claims.
Note: Where awards are collected through the use of an Avios
anonymous card or Avios manual entry, surname may not be
available. Certain loyalty programmes (e.g. Avios) will accept the
anonymous card number to be placed in this field in this
scenario.",

					"fType" 		 	   => "A/N",			     //  N/AN/A
					"fSize" 		 	   => 26, //1			// count of chars
					"fStartPosition" 	   => 225,
					"fEndPosition"   	   => 250, // 1
					"fRequiredFlag"        => "M",
					"fRequiredFlagComment" => "",
					"fExampleValue" 	   => "Smith",	
					"fValue"			   => $summaryValues["member-surname"],
					"hZeroPadded"		   => false,
			));		


			$loyaltyProgrammeGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "loyalty-programme",
					"fFieldName" 	 	   => "member-surname-2",
					// -----------
					"fDescription"   	   => "The member/customer's second Surname.",

					"fType" 		 	   => "A",			     //  N/AN/A
					"fSize" 		 	   => 26, //1			// count of chars
					"fStartPosition" 	   => 251,
					"fEndPosition"   	   => 276, // 1
					"fRequiredFlag"        => "O",
					"fRequiredFlagComment" => "",
					"fExampleValue" 	   => "Jones",	
					"fValue"			   => $summaryValues["member-surname-2"],
					"hZeroPadded"		   => false,
			));		


			$loyaltyProgrammeGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "loyalty-programme",
					"fFieldName" 	 	   => "partner-captured-tier-status",
					// -----------
					"fDescription"   	   => "Could be provided by partners who have the ability to swipe Loyalty Programme Membership Cards at point of sale.",

					"fType" 		 	   => "AN",			     //  N/AN/A
					"fSize" 		 	   => 20, //1			// count of chars
					"fStartPosition" 	   => 277,
					"fEndPosition"   	   => 296, // 1
					"fRequiredFlag"        => "O",
					"fRequiredFlagComment" => "",
					"fExampleValue" 	   => "GOLD",	
					"fValue"			   => $summaryValues["partner-captured-tier-status"],
					"hZeroPadded"		   => false,
			));		


			$loyaltyProgrammeGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "loyalty-programme",
					"fFieldName" 	 	   => "partner-captured-join-date",
					// -----------
					"fDescription"   	   => "Could be provided by partners who have the ability to swipe
Loyalty Programme Membership Cards at point of sale.",

					"fType" 		 	   => "Date", // IS0 8601 (including Time zone adjustment)",			     //  N/AN/A
					"fSize" 		 	   => 22, //1			// count of chars
					"fStartPosition" 	   => 297,
					"fEndPosition"   	   => 318, // 1
					"fRequiredFlag"        => "O",
					"fRequiredFlagComment" => "",
					"fExampleValue" 	   => "2008-07-12T12:54+01:00",	
					"fValue"			   => $summaryValues["partner-captured-join-date"],
					"hZeroPadded"		   => false,
			));			

			$loyaltyProgrammeGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "loyalty-programme",
					"fFieldName" 	 	   => "partner-capture-method",
					// -----------
					"fDescription"   	   => "The method in which the partner captured the loyalty
programme specific information (Tier Status and Join Date) e.g.
Card Swipe, From Partner Systems, Manual Entry etc. Used for
audit purposes.",

					"fType" 		 	   => "N",			     //  N/AN/A
					"fSize" 		 	   => 2, //1			// count of chars
					"fStartPosition" 	   => 319,
					"fEndPosition"   	   => 320, // 1
					"fRequiredFlag"        => "C",
					"fRequiredFlagComment" => "(Mandatory if Tier Status or Join Date is provided.)",
					"fExampleValue" 	   => "01 (Card Swipe)
02 (From Partner Systems)
03 (Manual Entry)",	
					"fValue"			   => $summaryValues["partner-capture-method"],
					"hZeroPadded"		   => false,
			));	

			// ---------------------------------

			$activitySummaryGroupArray = array();
			$activitySummaryGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "activity-summary",
					"fFieldName" 	 	   => "partner-location-code-1",
					// -----------
					"fDescription"   	   => "A location code used to apply location based earning rules or as
1
management information.
Typically used to represent a branch, store or establishment for
the purpose of executing earning rules.",

					"fType" 		 	   => "AN",			     //  N/AN/A
					"fSize" 		 	   => 10, //1			// count of chars
					"fStartPosition" 	   => 321,
					"fEndPosition"   	   => 330, // 1
					"fRequiredFlag"        => "O",
					"fRequiredFlagComment" => "",
					"fExampleValue" 	   => "MAYFAIR001",	
					"fValue"			   => $summaryValues["partner-location-code-1"],
					"hZeroPadded"		   => false,
			));	



		$activitySummaryGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "activity-summary",
					"fFieldName" 	 	   => "partner-location-code-2",
					// -----------
					"fDescription"   	   => "An additional location code to allow partners to provide further
location based information for management information.
Locations codes are required to be agreed and preconfigured
within the hub system prior to use and could represent
countries, sales regions, geographical area, branch/store codes
etc.",

					"fType" 		 	   => "AN",			     //  N/AN/A
					"fSize" 		 	   => 10, //1			// count of chars
					"fStartPosition" 	   => 331,
					"fEndPosition"   	   => 340, // 1
					"fRequiredFlag"        => "O",
					"fRequiredFlagComment" => "",
					"fExampleValue" 	   => "GB",	
					"fValue"			   => $summaryValues["partner-location-code-2"],
					"hZeroPadded"		   => false,
			));	


		$activitySummaryGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "activity-summary",
					"fFieldName" 	 	   => "partner-location-code-3",
					// -----------
					"fDescription"   	   => "An additional location code to allow partners to provide further
location based information for management information.
Locations codes are required to be agreed and preconfigured
within the hub system prior to use and could represent
countries, sales regions, geographical area, branch/store codes
etc.",

					"fType" 		 	   => "AN",			     //  N/AN/A
					"fSize" 		 	   => 10, //1			// count of chars
					"fStartPosition" 	   => 341,
					"fEndPosition"   	   => 350, // 1
					"fRequiredFlag"        => "O",
					"fRequiredFlagComment" => "",
					"fExampleValue" 	   => "LON",	
					"fValue"			   => $summaryValues["partner-location-code-3"],
					"hZeroPadded"		   => false,
			));	

		$activitySummaryGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "activity-summary",
					"fFieldName" 	 	   => "loyalty-programme-currency-stock-code",
					// -----------
					"fDescription"   	   => "A code to identify pre-allocated /purchased loyalty programme
currency.",

					"fType" 		 	   => "AN",			     //  N/AN/A
					"fSize" 		 	   => 5, //1			// count of chars
					"fStartPosition" 	   => 351,
					"fEndPosition"   	   => 355, // 1
					"fRequiredFlag"        => "C",
					"fRequiredFlagComment" => "(Loyalty Programme Dependent)",
					"fExampleValue" 	   => "SETA",	
					"fValue"			   => $summaryValues["loyalty-programme-currency-stock-code"],
					"hZeroPadded"		   => false,
			));	


		$activitySummaryGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "activity-summary",
					"fFieldName" 	 	   => "payment-method",
					// -----------
					"fDescription"   	   => "A code to represent the payment method used by the customer
during a spend transaction. Can be used to apply different
earning rules based on payment method.",

					"fType" 		 	   => "AN",			     //  N/AN/A
					"fSize" 		 	   => 20, //1			// count of chars
					"fStartPosition" 	   => 356,
					"fEndPosition"   	   => 375, // 1
					"fRequiredFlag"        => "O",
					"fRequiredFlagComment" => "",
					"fExampleValue" 	   => "AMEX
					MASTERCARD",	
					"fValue"			   => $summaryValues["payment-method"],
					"hZeroPadded"		   => false,
			));	

		$activitySummaryGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "activity-summary",
					"fFieldName" 	 	   => "loyalty-programme-currency",
					// -----------
					"fDescription"   	   => "A code to represent the loyalty programme currency that the
member should be awarded e.g. Avios.
AV - Avios",

					"fType" 		 	   => "A",			     //  N/AN/A
					"fSize" 		 	   => 2, //1			// count of chars
					"fStartPosition" 	   => 376,
					"fEndPosition"   	   => 377, // 1
					"fRequiredFlag"        => "M",
					"fRequiredFlagComment" => "",
					"fExampleValue" 	   => "AV",	
					"fValue"			   => $summaryValues["loyalty-programme-currency"],
					"hZeroPadded"		   => false,
			));	


		$activitySummaryGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "activity-summary",
					"fFieldName" 	 	   => "transaction-currency",
					// -----------
					"fDescription"   	   => "The ISO currency code relevant to the Activity. Will be used to
convert spend to an award using the earning rules setup within
the Hub system.",

					"fType" 		 	   => "A", //ISO 4217",			     //  N/AN/A
					"fSize" 		 	   => 3, //1			// count of chars
					"fStartPosition" 	   => 378,
					"fEndPosition"   	   => 380, // 1
					"fRequiredFlag"        => "C",
					"fRequiredFlagComment" => "(Mandatory for Activity feeds or Award feeds with Activity)",
					"fExampleValue" 	   => "GBP
USD
ZAR",	
					"fValue"			   => $summaryValues["transaction-currency"],
					"hZeroPadded"		   => false,
			));	


		$activitySummaryGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "activity-summary",
					"fFieldName" 	 	   => "total-transaction-value",
					// -----------
					"fDescription"   	   => "The total value of the transaction (in the specified transaction
currency).This will be used to verify the activity detail records
provided are correct.",

					"fType" 		 	   => "ND2!", //"N (Exactly 2 decimal places)",			     //  N/AN/A
					"fSize" 		 	   => 15, //1			// count of chars
					"fStartPosition" 	   => 381,
					"fEndPosition"   	   => 395, // 1
					"fRequiredFlag"        => "C",
					"fRequiredFlagComment" => "(Mandatory for Activity feeds or Award feeds with Activity)",
					"fExampleValue" 	   => "123456789012.00",	
					"fValue"			   => $summaryValues["total-transaction-value"],
					"hZeroPadded"		   => true,
			));	

				// -----------------------------------


		$awardSummaryGroupArray = array();
		$awardSummaryGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "award-summary",
					"fFieldName" 	 	   => "total-loyalty-points",
					// -----------
					"fDescription"   	   => "The total amount of (Avios) Loyalty Points to be awarded or
debited from the member's IAG account.
Includes bonus points.
Negative Values will be rejected.",

					"fType" 		 	   => "N",// (Values greater than zero only)",			     //  N/AN/A
					"fSize" 		 	   => 10, //1			// count of chars
					"fStartPosition" 	   => 396,
					"fEndPosition"   	   => 405, // 1
					"fRequiredFlag"        => "C",
					"fRequiredFlagComment" => "(For Award feeds - Must be provided)",
					"fExampleValue" 	   => "0000000000
0000001000",	
					"fValue"			   => $summaryValues["total-loyalty-points"],
					"hZeroPadded"		   => true,
			));		



			$awardSummaryGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "award-summary",
					"fFieldName" 	 	   => "total-bonus-loyalty-points",
					// -----------
					"fDescription"   	   => "The amount of bonus (Avios) Loyalty Points included within the
Total Loyalty Points amount.
This allows Loyalty Programmes to statement bonus amounts
separately and enforces debiting rules.",

					"fType" 		 	   => "N", // N+ (Values greater than zero only)",			     //  N/AN/A
					"fSize" 		 	   => 10, //1			// count of chars
					"fStartPosition" 	   => 406,
					"fEndPosition"   	   => 415, // 1
					"fRequiredFlag"        => "C",
					"fRequiredFlagComment" => "(For Award feeds - Must be provided)",
					"fExampleValue" 	   => "0000000000
0000001000",	
					"fValue"			   => $summaryValues["total-bonus-loyalty-points"],
					"hZeroPadded"		   => true,
			));		

			$awardSummaryGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "award-summary",
					"fFieldName" 	 	   => "qualifies-for-bonus",
					// -----------
					"fDescription"   	   => "A flag which determines whether an award record should be
excluded from bonus rules configured in the hub. Typically used
where Partners are responsible for calculating and sending
bonuses to the hub.
Defaults to Yes (Y) - Partner Hub will calculate any bonuses using
configured earning rules.
Must have a value of No (N) when a Partner is calculating a
Bonus award (i.e. Total Bonus Loyalty Points is greater than zero)
Note: This value applies to award records only, activity records
will have to be evaluated against earning rules.",

					"fType" 		 	   => "A",			     //  N/AN/A
					"fSize" 		 	   => 1, //1			// count of chars
					"fStartPosition" 	   => 416,
					"fEndPosition"   	   => 416, // 1
					"fRequiredFlag"        => "O",
					"fRequiredFlagComment" => "(Defaults to Y)",
					"fExampleValue" 	   => "Y - Bonus Rules Will Be Calculated By The Hub For Award Records
N - Bonus Rules Will Not Be Calculated By The Hub For Award Records",	
					"fValue"			   => $summaryValues["qualifies-for-bonus"],
					"hZeroPadded"		   => true,
			));	

			//- -------------------------------------


		$billingSummaryGroupArray   = array();
		$billingSummaryGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "billing-summary",
					"fFieldName" 	 	   => "standard-loyalty-points-billing-code",
					// -----------
					"fDescription"   	   => "A billing code to be provided where Partners send a standard
Avios award. Billing codes are required to be agreed and preconfigured within the hub system prior to use.",

					"fType" 		 	   => "AN",			     //  N/AN/A
					"fSize" 		 	   => 10, //1			// count of chars
					"fStartPosition" 	   => 417,
					"fEndPosition"   	   => 426, // 1
					"fRequiredFlag"        => "C",
					"fRequiredFlagComment" => "(For Award feeds - Must be provided where Total Loyalty Points > Total Bonus Loyalty Points )",
					"fExampleValue" 	   => "STAND00001",	
					"fValue"			   => $summaryValues["standard-loyalty-points-billing-code"],
					"hZeroPadded"		   => true,
			));	

			$billingSummaryGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "billing-summary",
					"fFieldName" 	 	   => "bonus-loyalty-points-billing-code",
					// -----------
					"fDescription"   	   => "A billing code to be provided where Partners send a bonus Avios
award. Billing codes are required to be agreed and preconfigured within the hub system prior to use.",

					"fType" 		 	   => "AN",			     //  N/AN/A
					"fSize" 		 	   => 10, //1			// count of chars
					"fStartPosition" 	   => 427,
					"fEndPosition"   	   => 436, // 1
					"fRequiredFlag"        => "C",
					"fRequiredFlagComment" => "(For Award feeds - Must be provided where Total Bonus Loyalty Points > 0)",
					"fExampleValue" 	   => "BONUS00001",	
					"fValue"			   => $summaryValues["bonus-loyalty-points-billing-code"],
					"hZeroPadded"		   => true,
			));			

			// --------------------------------------------------------

			$additionalInfoGroupArray   = array();
			$additionalInfoGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "additional-info",
					"fFieldName" 	 	   => "award-description",
					// -----------
					"fDescription"   	   => "A description of the award which can be communicated via print or electronic medium (e.g. Online) to the loyalty programme member. Format and content to be agreed with the Avios Feed team.",

					"fType" 		 	   => "AN",			     //  N/AN/A
					"fSize" 		 	   => 50, //1			// count of chars
					"fStartPosition" 	   => 437,
					"fEndPosition"   	   => 486, // 1
					"fRequiredFlag"        => "M",
					"fRequiredFlagComment" => "",
					"fExampleValue" 	   => "26May14-LONCDG-PNR0123-Avios Award",	
					"fValue"			   => $summaryValues["award-description"],
					"hZeroPadded"		   => true,
			));	


			$summaryGroup 			= new AviosFileGroup($summaryGroupArray, "summary", "content--summary");
			$partnerGroup 			= new AviosFileGroup($partnerGroupArray, "partner", "content--summary");
			$loyaltyProgrammeGroup  = new AviosFileGroup($loyaltyProgrammeGroupArray, "loyalty-programme", "content--summary"); 
			$activitySummaryGroup   = new AviosFileGroup($activitySummaryGroupArray, "activity-summary", "content--summary"); 
			$awardSummaryGroup 		= new AviosFileGroup($awardSummaryGroupArray, "award-summary", "content--summary");
			$billingSummaryGroup 	= new AviosFileGroup($billingSummaryGroupArray, "billing-summary", "content--summary"); 
			$additionalInfoGroup 	= new AviosFileGroup($additionalInfoGroupArray, "additional-info", "content--summary");

			return array($summaryGroup, $partnerGroup, $loyaltyProgrammeGroup, 
						 $activitySummaryGroup, $awardSummaryGroup, $billingSummaryGroup, 
						 	$additionalInfoGroup);
	
	}



	protected function generateSummaryChunk($summaryValues){


		// summary header, 
		// new AviosFileChunk($detailGroupArray);
		// then we attach collection of details 
		// for details to details lenggenerateSummaryGroups



		$summaryValues = $this->setSummaryParams($summaryValues);
		$summaryGroupArray = $this->generateSummaryGroups($summaryValues);

		return new AviosFileChunk($summaryGroupArray, "content--summary");

	}



	// summary chunk collection includes also details
	protected function generateSummaryChunkCollection(){


		// array of chunks
		$summaryAndDetailChunkCollectionArray = array();
		// TODO : handle empty params
		//if (empty($this->contentParams)){
		//	$this->contentChunkCollection = new AviosFileChunk(array(), "content");
		//	return;
		//}
		foreach ($this->contentParams as $val){

			// TODO : check!
			if (isset($val["summary"])){
				// add new summary chunk to the data storage
				$summaryAndDetailChunkCollectionArray[] = $this->generateSummaryChunk($val["summary"]);
			}
				
			
			if (isset($val["details"])){
				// merge current storage with new details


				$detailChunkCollectionArray =  $this->generateDetailChunkCollection($val["details"]);
				$summaryAndDetailChunkCollectionArray =  array_merge($summaryAndDetailChunkCollectionArray, 
																						$detailChunkCollectionArray); 
			}



			/* [
					0 => [
							summary => [] 		 (chunk)
							details => [0,1,2]   (chunk)
						]
					1 => [
							summry 				 (chunk)
							details => []		 (chunk)
						]	
				]
			*/
		}

		// this chunk stores several nested chunks inside
		$this->contentChunkCollection = new AviosFileChunk($summaryAndDetailChunkCollectionArray, "content");
		return;

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
					"fExampleValue" 	   => "2",	
					"fValue"			   => $detailValues["record-type"],
					"hZeroPadded"		   => true,
			));	

			// -------------------------------------------------


			$activityDetailGroupArray = array();
			$activityDetailGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "activity-detail",
					"fFieldName" 	 	   => "start-date",
					// -----------
					"fDescription"   	   => "The date/time in which the transaction took place (e.g. a purchase of fuel), or started (e.g. in the case of a hotel stay).",

					"fType" 		 	   => "Date",// IS0 8601 (YYYY-MM-DDThh:mmTZD)",			     //  N/AN/A
					"fSize" 		 	   => 22, //1			// count of chars
					"fStartPosition" 	   => 2,
					"fEndPosition"   	   => 23, // 1
					"fRequiredFlag"        => "M",
					"fRequiredFlagComment" => "",
					"fExampleValue" 	   => "2012-05-30T17:09+01:00",	
					"fValue"			   => $detailValues["start-date"],
					"hZeroPadded"		   => false,
			));	

			$activityDetailGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "activity-detail",
					"fFieldName" 	 	   => "end-date",
					// -----------
					"fDescription"   	   => "The date/time in which the transaction ended. Will not be specified if there is no clear end date/time e.g. For instantaneous transactions.",

					"fType" 		 	   => "Date", // IS0 8601 (YYYY-MM-DDThh:mmTZD)",			     //  N/AN/A
					"fSize" 		 	   => 22, //1			// count of chars
					"fStartPosition" 	   => 24,
					"fEndPosition"   	   => 45, // 1
					"fRequiredFlag"        => "O",
					"fRequiredFlagComment" => "",
					"fExampleValue" 	   => "2012-05-30T17:09+01:00",	
					"fValue"			   => $detailValues["end-date"],
					"hZeroPadded"		   => false,
			));	

			$activityDetailGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "activity-detail",
					"fFieldName" 	 	   => "duration-unit",
					// -----------
					"fDescription"   	   => "The unit that should be applied to the Duration Value e.g.
Minutes, Hours, Days, Weeks etc.
Y - Years
M - Months
W - Weeks
D - Days
H - Hours
MIN- Minutes
S - Seconds",

					"fType" 		 	   => "AN", //(Based on ISO-8601 Duration)",			     //  N/AN/A
					"fSize" 		 	   => 5, 			// count of chars
					"fStartPosition" 	   => 46,
					"fEndPosition"   	   => 50, 
					"fRequiredFlag"        => "C",
					"fRequiredFlagComment" => "(Mandatory where Duration Value is provided)",
					"fExampleValue" 	   => "D",	
					"fValue"			   => $detailValues["duration-unit"],
					"hZeroPadded"		   => false,
			));	


			$activityDetailGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "activity-detail",
					"fFieldName" 	 	   => "duration-value",
					// -----------
					"fDescription"   	   => "The duration that applies to the Start Date and End Date when
combined with Duration Unit. This allows Partner's to apply their own rounding policy using Start and End date and can be used within the Earning Rules. For example, the number of nights spent in a hotel.",

					"fType" 		 	   => "ND1",// (Up to 1 decimal places)",			     //  N/AN/A
					"fSize" 		 	   => 4, 			// count of chars
					"fStartPosition" 	   => 51,
					"fEndPosition"   	   => 54, 
					"fRequiredFlag"        => "C",
					"fRequiredFlagComment" => "(Mandatory where Duration Value is provided)",
					"fExampleValue" 	   => "0001
1120
99.5
0.5",	
					"fValue"			   => $detailValues["duration-value"],
					"hZeroPadded"		   => false,
			));	


			$activityDetailGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "activity-detail",
					"fFieldName" 	 	   => "product-category-code",
					// -----------
					"fDescription"   	   => "A code that represents the product, service or activity. Defined
by Avios as part of Partner setup.",

					"fType" 		 	   => "AN",			     //  N/AN/A
					"fSize" 		 	   => 10, 			// count of chars
					"fStartPosition" 	   => 55,
					"fEndPosition"   	   => 64, 
					"fRequiredFlag"        => "M",
					"fRequiredFlagComment" => "(Mandatory where Duration Value is provided)",
					"fExampleValue" 	   => "FUEL
ROOM",	
					"fValue"			   => $detailValues["product-category-code"],
					"hZeroPadded"		   => false,
			));	


			$activityDetailGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "activity-detail",
					"fFieldName" 	 	   => "sub-product-category-code",
					// -----------
					"fDescription"   	   => "A sub code (relates to the Product Category Code) that represents the product, service or activity. Defined by Avios as part of Partner setup.",

					"fType" 		 	   => "AN",			     //  N/AN/A
					"fSize" 		 	   => 10, 			// count of chars
					"fStartPosition" 	   => 65,
					"fEndPosition"   	   => 74, 
					"fRequiredFlag"        => "M",
					"fRequiredFlagComment" => "",
					"fExampleValue" 	   => "DOUBLE
TWIN
SINGLE
DIESELPLUS",	
					"fValue"			   => $detailValues["sub-product-category-code"],
					"hZeroPadded"		   => false,
			));		



			$activityDetailGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "activity-detail",
					"fFieldName" 	 	   => "product-class-code",
					// -----------
					"fDescription"   	   => "Partner specific product classification, typically used for MI purposes. For example to record the specific car rental vehicle class.",

					"fType" 		 	   => "AN",			     //  N/AN/A
					"fSize" 		 	   => 20, 			// count of chars
					"fStartPosition" 	   => 75,
					"fEndPosition"   	   => 94, 
					"fRequiredFlag"        => "O",
					"fRequiredFlagComment" => "",
					"fExampleValue" 	   => "MPVDER001
HBKLSO002",	
					"fValue"			   => $detailValues["product-class-code"],
					"hZeroPadded"		   => false,
			));	



			$activityDetailGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "activity-detail",
					"fFieldName" 	 	   => "tariff-code",
					// -----------
					"fDescription"   	   => "A code to represent the partner's product/service tariff.",

					"fType" 		 	   => "AN",			     //  N/AN/A
					"fSize" 		 	   => 5, 			// count of chars
					"fStartPosition" 	   => 95,
					"fEndPosition"   	   => 99, 
					"fRequiredFlag"        => "O",
					"fRequiredFlagComment" => "",
					"fExampleValue" 	   => "EXCLV",	
					"fValue"			   => $detailValues["tariff-code"],
					"hZeroPadded"		   => false,
			));	

			$activityDetailGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "activity-detail",
					"fFieldName" 	 	   => "quantity",
					// -----------
					"fDescription"   	   => "The number of products that apply. For example, the number of
hotel rooms booked. Number Formats to be agreed with Avios.
Up to 9 significant digits can be passed",

					"fType" 		 	   => "ND4",// (Up To 4 decimal places)",			     //  N/AN/A
					"fSize" 		 	   => 9, 			// count of chars
					"fStartPosition" 	   => 100,
					"fEndPosition"   	   => 108, 
					"fRequiredFlag"        => "O",
					"fRequiredFlagComment" => "",
					"fExampleValue" 	   => "123456789
1234.1234
0.1234567",	
					"fValue"			   => $detailValues["quantity"],
					"hZeroPadded"		   => false,
			));		


				$activityDetailGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "activity-detail",
					"fFieldName" 	 	   => "transaction-value",
					// -----------
					"fDescription"   	   => "The value of the Activity in the specified Cost Currency
(provided in the Summary Record). Where a transaction value is
not applicable a default value of 0.00 should be provided.",

					"fType" 		 	   => "ND2!",// (Exactly 2 decimal places)",			     //  N/AN/A
					"fSize" 		 	   => 15, 			// count of chars
					"fStartPosition" 	   => 109,
					"fEndPosition"   	   => 123, 
					"fRequiredFlag"        => "M",
					"fRequiredFlagComment" => "",
					"fExampleValue" 	   => "123456789123.99
0.00",	
					"fValue"			   => $detailValues["transaction-value"],
					"hZeroPadded"		   => true, // TODO : parse decimal
			));		

			


		$detailSummaryGroup = new AviosFileGroup($summaryGroupArray, "summary", "content--detail");
		$detailActivityDetailGroup = new AviosFileGroup($activityDetailGroupArray, "activity-detail", "content--detail");
		return array($detailSummaryGroup, $detailActivityDetailGroup);


	}


	// detail value will be processed in a loop, along with summary loop
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
		return $detailsChunkCollectionArray;
	}







	// -----------------------------------------------------------------------------------
	// content helpers


	public function generateFullContentChunk(){

		$this->generateSummaryChunkCollection();
	}


	protected function generateSubContentChunk($str, $type){

		$resultChunk = null;	
		switch ($type){
			case "summary" : $resultChunk = $this->generateSummaryChunk(array());
							 break;	
			
			case "detail"  : $resultChunk = $this->generateDetailChunk(array());
							 break;	

			default : return null; 
					  break;		
		}

		$this->fillRegularChunkFromRow($resultChunk, $str); 			
		return $resultChunk;

	}



}