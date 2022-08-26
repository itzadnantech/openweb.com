<?php
class AviosFileAwardHandback extends AviosFileAbstract{

    protected $listOfHeaderFields = array(
        "record-type", "format-version", "partner-code",
        "feed-type", "batch-number","handback-type", "reissue-number",
        "file-generation-date",
    );

    protected $listOfFooterFields = array(
        "record-type", "physical-record-count",  "logical-record-count", "success-record-count",
        "reject-record-count", "total-loyalty-points-received", "total-loyalty-points-awarded",
        "total-loyalty-points-rejected", "total-bonus-loyalty-points-received", "total-bonus-loyalty-points-awarded",
        "total-bonus-loyalty-points-rejected", "total-number-of-vouchers-awarded", "total-loyalty-points-rejected-awarded"
    );

    private $listOfSummaryFields = array(
        "record-type", "message-type", "message-code", "partner-transaction-reference",
        "partner-loyalty-programme-member-id", "transaction-type", "transaction-date",
        "credit-debit", "loyalty-programme-name", "loyalty-programme-member-id", "member-surname",
        "transaction-currency", "total-transaction-value", "total-bonus-loyalty-points-received",
        "total-loyalty-points-received", "total-bonus-loyalty-points-awarded", "total-loyalty-points-awarded"
    );

    private $listOfDetailFields = array(
        "record-type", "award-type", "award-amount", "award-description"
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



    function __construct($format = "00301", $partnerCode = "EXAGB00001", $feed = "ANAINP", $mode = null)
    {
        $this->writeMode     = $mode;
        $this->formatVersion = $format;
        $this->partnerCode   = $partnerCode;
        $this->feedType 	 = $feed;
    }


    protected function setConstHeaderParameters(&$paramStore){

        $paramStore["record-type"] 			= $this->recordTypeMapper["header"];
        $paramStore["format-version"] 		= $this->formatVersion;
        $paramStore["partner-code"] 		= $this->partnerCode;
        $paramStore["feed-type"] 		    = $this->feedType;
        $paramStore["file-generation-date"] = $this->generateCurrentDate();

    }

    public function generateFullContentChunk(){

        $this->generateSummaryChunkCollection();
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

        }

        // this chunk stores several nested chunks inside
        $this->contentChunkCollection = new AviosFileChunk($summaryAndDetailChunkCollectionArray, "content");
        return;

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

    protected function generateDetailChunkCollection($arrayOfDetails){

        $detailsChunkCollectionArray = array();
        foreach ($arrayOfDetails as $detail){
            //	$detailsChunkCollectionArray
            $detailsChunkCollectionArray[] = $this->generateDetailChunk($detail);
        }
        return $detailsChunkCollectionArray;
    }
    protected function generateDetailChunk($detailValues){

        $detailValues = $this->setDetailParams($detailValues);
        $detailGroupArray = $this->generateDetailGroups($detailValues);

        return new AviosFileChunk($detailGroupArray, "content--detail");
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

    private function setConstDetailParameters(&$paramStore){

        $paramStore["record-type"] = "2";

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

    public function setSummaryParams($params){

        $paramStore = array();
        foreach ($this->listOfSummaryFields as $key){

            $val = "";
            if (isset($params[$key]))
                $val = $params[$key];

            $paramStore[$key] = $val;
        }

        $this->setConstSummaryParameters($paramStore);

        return $paramStore;
    }

    private function setConstSummaryParameters(&$paramStore){

        $paramStore["record-type"] = "1";

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

    protected function setConstFooterParameters(&$paramStore){

        $paramStore["record-type"] = $this->recordTypeMapper["footer"];
    }

    public function generateEmptyFileObject(){

        $this->generateFileObjectFromParams(array(),array( array("summary" => array(), "details" => array( array() ) ) ), array());
    }

// --------------------------------------------------------------
// header

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


		// ----------------------------------------------------------------------
		// ----------------------------------------------------------------------
		// -------------- Header chunk / File Details group ---------------------
		// ----------------------------------------------------------------------
		// ----------------------------------------------------------------------


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
					"fFieldName" 	 	   => "handback-type",
					// -----------
					"fDescription"   	   => "Used to identify the type of
handback feed being returned,

ERROR - Handback contains only
transactions that resulted in an
error
ALL - Handback contains all
transactions that were submitted
by the partner.",

					"fType" 		 	   => "AN",			     //  N/AN/A
					"fSize" 		 	   => 5, //1			// count of chars
					"fStartPosition" 	   => 27,
					"fEndPosition"   	   => 31, // 1
					"fRequiredFlag"        => "M",
					"fRequiredFlagComment" => null,
					"fExampleValue" 	   => "ALL
ERROR",	
					"fValue"			   => $this->headerParams["handback-type"],
					"hZeroPadded"		   => true,
			));



		$fileDetailsGroupArray[] = new AviosFileField(	
			
				array(

					"fGroup" 		 	   => "file-details",
					"fFieldName" 	 	   => "reissue-number",
					// -----------
					"fDescription"   	   => "
Used to provide the ability to
resubmit feed files which received
a confirmation file with errors.
Default is 0. Incremented by 1 on
resubmission of a file.


Records being resubmitted as a
result of errors being received in a
handback file should use an
incremented batch number and a
reissue number of 0.

Note: The Confirmation and
Handback file resubmission
number sent to partners will
correspond to the resubmission
number used in the input file",

					"fType" 		 	   => "N",			     //  N/AN/A
					"fSize" 		 	   => 1, //1			// count of chars
					"fStartPosition" 	   => 32,
					"fEndPosition"   	   => 32, // 1
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
					"fStartPosition" 	   => 33,
					"fEndPosition"   	   => 54, // 1
					"fRequiredFlag"        => "M",
					"fRequiredFlagComment" => null,
					"fExampleValue" 	   => "2012-05-30T17:09+01:00",	
					"fValue"			   => $this->headerParams["file-generation-date"],
					"hZeroPadded"		   => false,
			));



		// ----------------------------------------------------------------------
		// ----------------------------------------------------------------------
		// ---------------------  save groups -----------------------------------
		// ----------------------------------------------------------------------
		// ----------------------------------------------------------------------

		$headerSummaryGroup = new AviosFileGroup($summaryGroupArray, "summary", "header");
		$headerFileDetailsGroup = new AviosFileGroup($fileDetailsGroupArray, "file-details", "header"); 

		return array($headerSummaryGroup, $headerFileDetailsGroup);

	}




// --------------------------------------------------------------
// footer


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
					"fExampleValue" 	   => "00001234",	
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
					"fExampleValue" 	   => "00001234",	
					"fValue"			   => $this->footerParams["logical-record-count"],
					"hZeroPadded"		   => true,
			));		


			$summaryGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "summary",
					"fFieldName" 	 	   => "success-record-count",
					// -----------
					"fDescription"   	   => "The number of successful activity and
award records contained within the file",

					"fType" 		 	   => "N",			     //  N/AN/A
					"fSize" 		 	   => 8, 			// count of chars
					"fStartPosition" 	   => 18,
					"fEndPosition"   	   => 25, 
					"fRequiredFlag"        => "M",
					"fRequiredFlagComment" => "",
					"fExampleValue" 	   => "00001200",	
					"fValue"			   => $this->footerParams["success-record-count"],
					"hZeroPadded"		   => true,
			));		




				$summaryGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "summary",
					"fFieldName" 	 	   => "reject-record-count",
					// -----------
					"fDescription"   	   => "The number of rejected activity and
award records contained within the file",

					"fType" 		 	   => "N",			     //  N/AN/A
					"fSize" 		 	   => 8, 			// count of chars
					"fStartPosition" 	   => 26,
					"fEndPosition"   	   => 33, 
					"fRequiredFlag"        => "M",
					"fRequiredFlagComment" => "",
					"fExampleValue" 	   => "00000034",	
					"fValue"			   => $this->footerParams["reject-record-count"],
					"hZeroPadded"		   => true,
			));		

			$summaryGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "summary",
					"fFieldName" 	 	   => "total-loyalty-points-received",
					// -----------
					"fDescription"   	   => "Total Number of (Avios) loyalty points
received from the partner, inclusive of
Bonus awards
					Note: This will be the balance from
adding up credit and debit awards
received in the instance this calculates to
a negative amount, the first character
should be provided with a negative sign.
For example a negative 400 avios award
should be provided as -00000000400",

					"fType" 		 	   => "N",			     //  N/AN/A
					"fSize" 		 	   => 12, 			// count of chars
					"fStartPosition" 	   => 34,
					"fEndPosition"   	   => 45, 
					"fRequiredFlag"        => "M",
					"fRequiredFlagComment" => "",
					"fExampleValue" 	   => "000000000050
-00000000050",	
					"fValue"			   => $this->footerParams["total-loyalty-points-received"],
					"hZeroPadded"		   => true,
			));		




			$summaryGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "summary",
					"fFieldName" 	 	   => "total-loyalty-points-awarded",
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
					"fStartPosition" 	   => 46,
					"fEndPosition"   	   => 57, 
					"fRequiredFlag"        => "M",
					"fRequiredFlagComment" => "",
					"fExampleValue" 	   => "000000000100
-00000000050",	
					"fValue"			   => $this->footerParams["total-loyalty-points-rejected-awarded"],
					"hZeroPadded"		   => true,
			));				


		// TODO : Total Loyalty Points Rejected


		$summaryGroupArray[] = new AviosFileField(array(
			
								"fGroup" 		 	   => "summary",
								"fFieldName" 	 	   => "total-loyalty-points-rejected",
								// -----------
								"fDescription"   	   => "Total Number of (Avios) loyalty points
(inclusive of Bonus awards) rejected as a
result of processing received award. This
relates to the Avios awards received from
the Partner.

Note: This will be the balance from
adding up rejected credit and debit
awards received in the instance this
calculates to a negative amount, the first
character should be provided with a
negative sign. For example a negative 400
avios award should be provided as
-00000000400",
			
								"fType" 		 	   => "N",			     //  N/AN/A
								"fSize" 		 	   => 12, 			// count of chars
								"fStartPosition" 	   => 58,
								"fEndPosition"   	   => 69, 
								"fRequiredFlag"        => "M",
								"fRequiredFlagComment" => "",
								"fExampleValue" 	   => "000000000000
-00000000050",	
								"fValue"			   => $this->footerParams["total-loyalty-points-rejected"],
								"hZeroPadded"		   => true,
						));	



		$summaryGroupArray[] = new AviosFileField(array(
			
								"fGroup" 		 	   => "summary",
								"fFieldName" 	 	   => "total-bonus-loyalty-points-received",
								// -----------
								"fDescription"   	   => "Total Number of bonus (Avios) loyalty
points received from the partner.

Note: This will be the balance from
adding up credit and debit bonus awards
received in the instance this calculates to
a negative amount, the first character
should be provided with a negative sign.
For example a negative 400 avios award
should be provided as -00000000400",
			
								"fType" 		 	   => "N",			     //  N/AN/A
								"fSize" 		 	   => 12, 			// count of chars
								"fStartPosition" 	   => 70,
								"fEndPosition"   	   => 81, 
								"fRequiredFlag"        => "M",
								"fRequiredFlagComment" => "",
								"fExampleValue" 	   => "000000000050
-00000000050",	
								"fValue"			   => $this->footerParams["total-bonus-loyalty-points-received"],
								"hZeroPadded"		   => true,
						));	



		$summaryGroupArray[] = new AviosFileField(array(
			
								"fGroup" 		 	   => "summary",
								"fFieldName" 	 	   => "total-bonus-loyalty-points-awarded",
								// -----------
								"fDescription"   	   => "Total Number of bonus (Avios) loyalty
points awarded as a result of processing
activity and award records contained
within the file.

Note: This will be the balance from
adding up credit and debit bonus awards
in the instance this calculates to a
negative amount, the first character
should be provided with a negative sign.
For example a negative 400 avios award
should be provided as -00000000400",
			
								"fType" 		 	   => "N",			     //  N/AN/A
								"fSize" 		 	   => 12, 			// count of chars
								"fStartPosition" 	   => 82,
								"fEndPosition"   	   => 93, 
								"fRequiredFlag"        => "M",
								"fRequiredFlagComment" => "",
								"fExampleValue" 	   => "000000000100
-00000000050",	
								"fValue"			   => $this->footerParams["total-bonus-loyalty-points-awarded"],
								"hZeroPadded"		   => true,
						));	


		$summaryGroupArray[] = new AviosFileField(array(
			
								"fGroup" 		 	   => "summary",
								"fFieldName" 	 	   => "total-bonus-loyalty-points-rejected",
								// -----------
								"fDescription"   	   => "Total Number of bonus (Avios) loyalty
points rejected as a result of processing
received award. This relates to the Avios
awards received from the Partner and
does not factor in Bonus awards
generated by earning rules.

Note: This will be the balance from
adding up rejected credit and debit bonus
awards received in the instance this
calculates to a negative amount, the first
character should be provided with a
negative sign. For example a negative 400
avios award should be provided as
-00000000400",
			
								"fType" 		 	   => "N",			     //  N/AN/A
								"fSize" 		 	   => 12, 			// count of chars
								"fStartPosition" 	   => 94,
								"fEndPosition"   	   => 105, 
								"fRequiredFlag"        => "M",
								"fRequiredFlagComment" => "",
								"fExampleValue" 	   => "000000000000
-00000000050",	
								"fValue"			   => $this->footerParams["total-bonus-loyalty-points-rejected"],
								"hZeroPadded"		   => true,
						));	


			$summaryGroupArray[] = new AviosFileField(array(
			
								"fGroup" 		 	   => "summary",
								"fFieldName" 	 	   => "total-number-of-vouchers-awarded",
								// -----------
								"fDescription"   	   => "Total number of vouchers awarded as a
result of processing activity and award
records contained within the file.",
			
								"fType" 		 	   => "N",			     //  N/AN/A
								"fSize" 		 	   => 8, 			// count of chars
								"fStartPosition" 	   => 106,
								"fEndPosition"   	   => 113, 
								"fRequiredFlag"        => "M",
								"fRequiredFlagComment" => "",
								"fExampleValue" 	   => "000000000000",	
								"fValue"			   => $this->footerParams["total-number-of-vouchers-awarded"],
								"hZeroPadded"		   => true,
						));	


		$footerSummaryGroup = new AviosFileGroup($summaryGroupArray, "summary", "footer");
		return array($footerSummaryGroup);


	}




// -----------------------------------------------------------------------------------------------
// content - summary

protected function generateSummaryGroups($summaryValues){

		
			$summaryGroupArray = array();
			$summaryGroupArray[] = new AviosFileField(	
			
				array(

					"fGroup" 		 	   => "summary",
					"fFieldName" 	 	   => "record-type",
					// -----------
					"fDescription"   		    => "Used to identify the different type of records being sent in a multi-record type feed file.",

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
					"fFieldName" 	 	   => "message-type",
					// -----------
					"fDescription"   	   => "The type of message.
I=Informational Message
E=Record Level Error Message",

					"fType" 		 	   => "A",			     //  N/AN/A
					"fSize" 		 	   => 1, //1			// count of chars
					"fStartPosition" 	   => 2,
					"fEndPosition"   	   => 2, // 1
					"fRequiredFlag"        => "M",
					"fRequiredFlagComment" => null,
					"fExampleValue" 	   => "I
E",
					"fValue"			   => $summaryValues["message-type"],
					"hZeroPadded"		   => false,
			));


			$summaryGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "summary",
					"fFieldName" 	 	   => "message-code",
					// -----------
					"fDescription"   	   => "The code specifying the message.",

					"fType" 		 	   => "AN",			     //  N/AN/A
					"fSize" 		 	   => 4, //1			// count of chars
					"fStartPosition" 	   => 3,
					"fEndPosition"   	   => 6, // 1
					"fRequiredFlag"        => "M",
					"fRequiredFlagComment" => null,
					"fExampleValue" 	   => "0001",	
					"fValue"			   => $summaryValues["message-code"],
					"hZeroPadded"		   => false,
			));

			// ----------------------------------------------	

			
				$partnerGroupArray = array();
			    $partnerGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "partner",
					"fFieldName" 	 	   => "partner-transaction-reference",
					// -----------
					"fDescription"   	   => "The unique identifier for the
activity/award transaction supplied by
the partner.",

					"fType" 		 	   => "AN",			     //  N/AN/A
					"fSize" 		 	   => 30, //1			// count of chars
					"fStartPosition" 	   => 7,
					"fEndPosition"   	   => 36, // 1
					"fRequiredFlag"        => "M",
					"fRequiredFlagComment" => "",
					"fExampleValue" 	   => "ABC123456789012345
678901234567",	
					"fValue"			   => $summaryValues["partner-transaction-reference"],
					"hZeroPadded"		   => false,
			));		



				$partnerGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "partner",
					"fFieldName" 	 	   => "partner-loyalty-programme-member-id",
					// -----------
					"fDescription"   	   => "
The membership number within the
partner scheme. Either the Partner
Loyalty Programme Member ID or
Loyalty Programme Member ID must be
provided.

(Will not be zero padded))",

					"fType" 		 	   => "N",	//	(Will not be zero padded)
					"fSize" 		 	   => 24, //1			// count of chars
					"fStartPosition" 	   => 37,
					"fEndPosition"   	   => 60, // 1
					"fRequiredFlag"        => "C",
					"fRequiredFlagComment" => "(One of Partner Loyalty Programme Member ID or 
										       Loyalty Programme Member ID is required.)",
					"fExampleValue" 	   => "123456",	
					"fValue"			   => $summaryValues["partner-loyalty-programme-member-id"],
					"hZeroPadded"		   => false,
			));			

			

		$transactionSummaryGroupArray = array();	
		$transactionSummaryGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "transaction-summary",
					"fFieldName" 	 	   => "transaction-type",
					// -----------
					"fDescription"   	   => "
This field will be used to determine if
the record is an Activity record (that
needs the Award to be calculated) or an
Award record with or without
accompanying Activity information.",

					"fType" 		 	   => "AN",	//	(Will not be zero padded)
					"fSize" 		 	   => 2, //1			// count of chars
					"fStartPosition" 	   => 61,
					"fEndPosition"   	   => 62, // 1
					"fRequiredFlag"        => "M",
					"fRequiredFlagComment" => "",	
					"fExampleValue" 	   => "01 (Activity)
02 (Award - with
Activity information)
03 (Award - without
Activity information)",
					"fValue"			   => $summaryValues["transaction-type"],
					"hZeroPadded"		   => false,
			));	


		$transactionSummaryGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "transaction-summary",
					"fFieldName" 	 	   => "transaction-date",
					// -----------
					"fDescription"   	   => "
The specific date/time the overall
transaction was completed.",

					"fType" 		 	   => "Date",	//	Date IS0 8601 (+UTC)
					"fSize" 		 	   => 22, //1			// count of chars
					"fStartPosition" 	   => 63,
					"fEndPosition"   	   => 84, // 1
					"fRequiredFlag"        => "M",
					"fRequiredFlagComment" => "",	
					"fExampleValue" 	   => "2012-05-30T17:09+01:00",														
					"fValue"			   => $summaryValues["transaction-date"],
					"hZeroPadded"		   => false,
			));	




			$transactionSummaryGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "transaction-summary",
					"fFieldName" 	 	   => "credit-debit",
					// -----------
					"fDescription"   	   => "This field defines if the transaction is a
\"Credit\" to or a \"Debit\" from the
members account. Debit transactions
are typically used to resolve incorrect
processing.
C - Credit
D - Debit",

					"fType" 		 	   => "A",	//	Date IS0 8601 (+UTC)
					"fSize" 		 	   => 1, //1			// count of chars
					"fStartPosition" 	   => 85,
					"fEndPosition"   	   => 85, // 1
					"fRequiredFlag"        => "C",
					"fRequiredFlagComment" => "(Mandatory for
Activity feeds or
Award feeds
with Activity)",	
					"fExampleValue" 	   => "C
					D",														
					"fValue"			   => $summaryValues["credit-debit"],
					"hZeroPadded"		   => false,
			));	


	
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

AVIOS - Avios TRP
BAEC - British Airways Exec Club
IBPL - Iberia Plus FFP
ISSLP – Meridiana Club FFP",

					"fType" 		 	   => "AN",			     //  N/AN/A
					"fSize" 		 	   => 5, //1			// count of chars
					"fStartPosition" 	   => 86,
					"fEndPosition"   	   => 90, // 1
					"fRequiredFlag"        => "M",
					"fRequiredFlagComment" => "",
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
					"fDescription"   	   => "The IAG Loyalty Programme
membership number.
(Will not be zero padded)",

					"fType" 		 	   => "N",			     //  N/AN/A
					"fSize" 		 	   => 24, //1			// count of chars
					"fStartPosition" 	   => 91,
					"fEndPosition"   	   => 114, // 1
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
					"fStartPosition" 	   => 115,
					"fEndPosition"   	   => 140, // 1
					"fRequiredFlag"        => "M",
					"fRequiredFlagComment" => "",
					"fExampleValue" 	   => "Smith",	
					"fValue"			   => $summaryValues["member-surname"],
					"hZeroPadded"		   => false,
			));		


			$activitySummaryGroupArray = array();
			$activitySummaryGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "activity-summary",
					"fFieldName" 	 	   => "transaction-currency",
					// -----------
					"fDescription"   	   => "The ISO currency code relevant to the
Activity.",

					"fType" 		 	   => "A", //ISO 4217",			     //  N/AN/A
					"fSize" 		 	   => 3, //1			// count of chars
					"fStartPosition" 	   => 141,
					"fEndPosition"   	   => 143, // 1
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
					"fDescription"   	   => "The total value of the transaction.",

					"fType" 		 	   => "ND2!", //"N (Exactly 2 decimal places)",			     //  N/AN/A
					"fSize" 		 	   => 15, //1			// count of chars
					"fStartPosition" 	   => 144,
					"fEndPosition"   	   => 158, // 1
					"fRequiredFlag"        => "C",
					"fRequiredFlagComment" => "(Mandatory for Activity feeds or Award feeds with Activity)",
					"fExampleValue" 	   => "123456789012.99",	
					"fValue"			   => $summaryValues["total-transaction-value"],
					"hZeroPadded"		   => true,
			));	

				// -----------------------------------


		$awardSummaryGroupArray = array();
		$awardSummaryGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "award-summary",
					"fFieldName" 	 	   => "total-bonus-loyalty-points-received", // (Avios)
					// -----------
					"fDescription"   	   => "The amount of bonus Loyalty Points
(Avios) sent in an award transaction by
the partner in the input file

Note: This will be the balance from
adding up credit and debit bonus
awards received, in the instance this
calculates to a negative amount, the
first character should be provided with
a negative sign. For example a negative
400 avios award should be provided as
-000004000",

					"fType" 		 	   => "N",// (Values greater than zero only)",			     //  N/AN/A
					"fSize" 		 	   => 10, //1			// count of chars
					"fStartPosition" 	   => 159,
					"fEndPosition"   	   => 168, // 1
					"fRequiredFlag"        => "C",
					"fRequiredFlagComment" => "(Mandatory for Award feeds)",
					"fExampleValue" 	   => "0000000000
0000001000
-000004000",	
					"fValue"			   => $summaryValues["total-bonus-loyalty-points-received"],
					"hZeroPadded"		   => true,
			));		



			$awardSummaryGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "award-summary",
					"fFieldName" 	 	   => "total-loyalty-points-received", // (Avios)
					// -----------
					"fDescription"   	   => "The total amount of Loyalty Points
(Avios), inclusive of Bonus, sent in an
award transaction by the partner in the
input file.
x
Note: This will be the balance from
adding up credit and debit awards
received, in the instance this calculates
to a negative amount, the first
character should be provided with a
negative sign. For example a negative
400 avios award should be provided as
-000004000",

					"fType" 		 	   => "N", // N+ (Values greater than zero only)",			     //  N/AN/A
					"fSize" 		 	   => 10, //1			// count of chars
					"fStartPosition" 	   => 169,
					"fEndPosition"   	   => 178, // 1
					"fRequiredFlag"        => "C",
					"fRequiredFlagComment" => "(Mandatory for Award feeds)",
					"fExampleValue" 	   => "0000000000
0000001000
-000004000",	
					"fValue"			   => $summaryValues["total-loyalty-points-received"],
					"hZeroPadded"		   => true,
			));		

			$awardSummaryGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "award-summary",
					"fFieldName" 	 	   => "total-bonus-loyalty-points-awarded",
					// -----------
					"fDescription"   	   => "The total amount of bonus Loyalty
Points awarded to the IAG member's
account for the respective transaction.

",

					"fType" 		 	   => "N",			     //  N/AN/A
					"fSize" 		 	   => 10, //1			// count of chars
					"fStartPosition" 	   => 179,
					"fEndPosition"   	   => 188, // 1
					"fRequiredFlag"        => "C",
					"fRequiredFlagComment" => "(Mandatory for
Award feeds)",
					"fExampleValue" 	   => "0000000000
0000001000
-000004000",	
					"fValue"			   => $summaryValues["total-bonus-loyalty-points-awarded"],
					"hZeroPadded"		   => true,
			));	


				$awardSummaryGroupArray[] = new AviosFileField(array(

					"fGroup" 		 	   => "award-summary",
					"fFieldName" 	 	   => "total-loyalty-points-awarded",
					// -----------
					"fDescription"   	   => "The total amount of Loyalty Points
(inclusive Bonus) awarded to the IAG
member's account for the respective
transaction.

Note: This will be the balance from
adding up credit and debit awards in
the instance this calculates to a
negative amount, the first character
should be provided with a negative
sign. For example a negative 400 avios
award should be provided as
-00000000400",

					"fType" 		 	   => "N",			     //  N/AN/A
					"fSize" 		 	   => 10, //1			// count of chars
					"fStartPosition" 	   => 189,
					"fEndPosition"   	   => 198, // 1
					"fRequiredFlag"        => "C",
					"fRequiredFlagComment" => "(Mandatory for
Award feeds)",
					"fExampleValue" 	   => "0000000000
0000001000",	
					"fValue"			   => $summaryValues["total-loyalty-points-awarded"],
					"hZeroPadded"		   => true,
			));	



			$summaryGroup 			 = new AviosFileGroup($summaryGroupArray, "summary", "content--summary");
			$partnerGroup 			 = new AviosFileGroup($partnerGroupArray, "partner", "content--summary");
			$transactionSummaryGroup = new AviosFileGroup($transactionSummaryGroupArray, "transaction-summary", "content--summary");
			$loyaltyProgrammeGroup  = new AviosFileGroup($loyaltyProgrammeGroupArray, "loyalty-programme", "content--summary"); 
			$activitySummaryGroup   = new AviosFileGroup($activitySummaryGroupArray, "activity-summary", "content--summary");
			$awardSummaryGroup 		= new AviosFileGroup($awardSummaryGroupArray, "award-summary", "content--summary");
			
			return array($summaryGroup, $partnerGroup, $transactionSummaryGroup, $loyaltyProgrammeGroup, $activitySummaryGroup, $awardSummaryGroup);
	
	}	




	// -------------------------------------------------------------------------------
	// content - detail


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

			$awardInfoGroupArray = array();
			$awardInfoGroupArray[] = new AviosFileField(array(

				"fGroup" 		 	   => "award-info",
				"fFieldName" 	 	   => "award-type",
				// -----------
				"fDescription"   	   => "Indicates the type of award that has been
generated as a result of sending the
award/activity record",

				"fType" 		 	   => "AN",			     //  N/AN/A
				"fSize" 		 	   => 15, //1			// count of chars
				"fStartPosition" 	   => 2,
				"fEndPosition"   	   => 16, // 1
				"fRequiredFlag"        => "M",
				"fRequiredFlagComment" => "",
				"fExampleValue" 	   => "STANDARD_AWARD
BONUS_AWARD
VOUCHER
ENHNCMNT_BONUS
FREQ_BONUS
REG_BONUS",	
				"fValue"			   => $detailValues["award-type"],
				"hZeroPadded"		   => true,
			));	


			$awardInfoGroupArray[] = new AviosFileField(array(

				"fGroup" 		 	   => "award-info",
				"fFieldName" 	 	   => "award-amount",
				// -----------
				"fDescription"   	   => "Where an award or bonus has been applied
due to a successful processing an
award/activity record, this field will provide
the amount of Avios awarded.

Note: Negative amounts will be provided with
the first character as a negative sign. For
example a negative 400 award amount will be
provided as
-00000000400",

				"fType" 		 	   => "N",			     //  N/AN/A
				"fSize" 		 	   => 15, //1			// count of chars
				"fStartPosition" 	   => 17,
				"fEndPosition"   	   => 31, // 1
				"fRequiredFlag"        => "C",
				"fRequiredFlagComment" => "(Mandatory for AWARD and BONUS)",
				"fExampleValue" 	   => "000000000000100",	
				"fValue"			   => $detailValues["award-amount"],
				"hZeroPadded"		   => true,
			));	


			$awardInfoGroupArray[] = new AviosFileField(array(

				"fGroup" 		 	   => "award-info",
				"fFieldName" 	 	   => "award-description",
				// -----------
				"fDescription"   	   => "A description of the award",

				"fType" 		 	   => "AN",			     //  N/AN/A
				"fSize" 		 	   => 50, //1			// count of chars
				"fStartPosition" 	   => 32,
				"fEndPosition"   	   => 81, // 1
				"fRequiredFlag"        => "M",
				"fRequiredFlagComment" => "",
				"fExampleValue" 	   => "Companion Voucher",	
				"fValue"			   => $detailValues["award-description"],
				"hZeroPadded"		   => true,
			));	



		$summaryGroup = new AviosFileGroup($summaryGroupArray, "summary", "content--detail");
		$awardInfoGroup = new AviosFileGroup($awardInfoGroupArray, "award-info", "content--detail");

		return array($summaryGroup, $awardInfoGroup);


	}



}