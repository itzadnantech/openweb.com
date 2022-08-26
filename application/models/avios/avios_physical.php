<?php
//  related to physical awarads (file generation, etc)
class Avios_physical extends CI_Model 
{

	// map virtual avios to physical ? - ?
	// generate physical avios based on virtual
	// move files / delete
	// CRUD file listing (db)

	/*
		Partner Code: EXAGB00001
		Campaign Code: 00001GBEXA
		Standard Billing Code: GBEXASTAND
		Bonus Billing Code: GBEXABONUS
	*/

	private $partnerCode = "OPNZA00001";
	private $campaignCode = "00001OPNZA";
	private $standardBillingCode = "";
	private $bonusBillingCode = "";


	/*

	The IAG Input file name will be as follows:

		EXAGB00001ANAINP0030100010.gpg
		EXAGB00001 => Partner code (to be defined in due course)   (Type : AN, 10 [page 56])
		ANAINP => Feed Type
		00301 => Format Version
		0001 => Batch number (incremental)
		0 => Reissue number
		.gpg => File extension (to be confirmed)


	{PartnerCode}{FileType}{VersionNumber}{BatchNumber}{ReissueNumber}

	{PartnerCode}   : AN, Exactly 10
	Code used to identify the partner. Equivalent to the partner code being used in the feeds

	{FileType}      : AN, Exactly 6
	Code used to identify the type of file being sent or received.

	{VersionNumber} : N, Exactly 5, Zero-Padded
	Number used to identify the feed format version being submitted. A five digit number, zero padded.

	{BatchNumber}   : N, Exactly 4, Zero-Padded
	Sequence number used to identify a batch feed file. A four digit number, zero padded. The sequence starts at
	0001, ends at 9999, after which it resets to 0001. The batch number increments by 1 for each new data file of
	the same file type or where a file is being resent after handback errors have been received and corrected.

	Note: The Confirmation and Handback file batch number will correspond to the batch number used in the input file

	{ReissueNumber} : N, Exactly 1
	Used to provide the ability to resubmit feed files which received a confirmation file with errors. 
	Default is 0. Incremented by 1 on resubmission of a file.
	
	Records being resubmitted as a result of errors being received in a handback file 
	should use an incremented batch number and a reissue number of 0.

	Note: The Confirmation and Handback file resubmission number will correspond to the resubmission 
	number used in the input file

	*/

	const FilenameVersionNumberLenth = 5;
	const FilenameBatchNumberLenth = 4;


	private $fileTypes = array(  // Type : AN, 6 [page 56]

			// key   =>   description
			"REGINP" => "Registration Input File",
			"REGCFM" => "Registration Confirmation File",
			"REGHDB" => "Registration Handback File",
			"REGHDE" => "Registration Handback File (Errors Only)",
			"ANAINP" => "Activity & Award Input File",
			"ANACFM" => "Activity & Award Confirmation File",
			"ANAHDB" => "Activity & Award Handback File", 
			"ANAHDE" => "Activity & Award Handback File (Errors Only)",
			"ANAHDR" => "Activity & Award Handback File (Reversed Feed)",

		);


	public function __construct(){

			// init sub-classes
			require_once "avios_files/avios_files_structure/avios_file_field.php";
			require_once "avios_files/avios_files_structure/avios_file_group.php";
			require_once "avios_files/avios_files_structure/avios_file_chunk.php";


			require_once "avios_files/avios_file_abstract.php";
			require_once "avios_files/avios_file_award_input.php";
			require_once "avios_files/avios_file_award_confirmation.php";
            require_once "avios_files/avios_file_award_handback.php";
            require_once "avios_error.php";
			//$this->load->model("avios/avios_data");
            $this->load->model('avios/avios_logs');

            require_once 'Crypt/GPG.php';

            //$this->parseConfirmationFile("OPNZA00001ANACFM0030100030.gpg");
            //$this->createFile($this->avios_data->third, 3);
			//$this->debug($transact);
			//die('- inside avios physical - debug-end');

	}

    public function createFile($transact, $reissueBatch = null) {

	    if($reissueBatch == null) {
            //get batch and file_id
            $indexes = $this->avios_logs->getLastIndexes();
        } else {
	        $indexes = $this->avios_logs->getIndexesReissue($reissueBatch);
        }

        //generate file name for new file
        $batchNumber = $indexes["batch_number"];
        $reissueNumber = $indexes['reissue_number'];
        $newFileName = $this->generateFileName("ANAINP", "00301", $batchNumber, $reissueNumber);

        //add file to logs with "creating" status
        $this->avios_logs->addAwardFile($newFileName);

        //Fill data into from input to Object
        $map = $this->mapping($indexes["file_id"], $transact, $batchNumber, $reissueNumber);

        $fields = $map;

        //File path
        $folderPath = AVIOS_HOMEDIR;  //TODO use AVIOS_HOMEDIR instead of this for production "application/avios/inbound/"
        $awardFileName = $folderPath . $newFileName;

        $aviosInputObj = new AviosFileAwardInput();

        // generate object from params
        $aviosInputObj->generateFileObjectFromParams($fields[0], $fields[1], $fields[2]);

        // generate array with file rows from object
        $fileStrData = $aviosInputObj->convertChunksToRows();

        // write rows to file (creates EXAGB00001ANAINP0030100013)
        $result1 = $aviosInputObj->writeStrChunksToFile($awardFileName, $fileStrData);

        //update file status
        $this->avios_logs->fileStatusUpdate("created", $indexes["file_id"]);

        //Encrypt file (creates File.gpg)
        if($this->encryptFile($folderPath, $newFileName)) {
            //update file status
            $this->avios_logs->fileStatusUpdate("encrypted", $indexes["file_id"]);
        }

        return true;
    }

	// called from constructor
	public function debug($transact){

        // get all Input params (incl. header and footer)
        $paramsForTest = $this->debugReturnAwardInputParams();

        //reset points params<-----TODO
        $paramsForTest[2]["physical-record-count"] = 0;
        $paramsForTest[2]["total-bonus-loyalty-points-avios-awarded"] = 0;
        $paramsForTest[2]["total-loyalty-points-avios-awarded"] = 0;
        $paramsForTest[2]["logical-record-count"] = 0;

        $paramsForTest[1] = array();

        foreach ($transact as $user_id => $order_id) {

            $map = $this->mapping(15, $user_id, $order_id);

            $paramsForTest[1][] = $map;
            $paramsForTest[2]["physical-record-count"] += 1;
            $paramsForTest[2]["logical-record-count"] += 1;

            $paramsForTest[2]["total-bonus-loyalty-points-avios-awarded"] += $map["summary"]["total-bonus-loyalty-points"];
            $paramsForTest[2]["total-loyalty-points-avios-awarded"] += $map["summary"]["total-loyalty-points"];
        }
        //$paramsForTest[1][] = $summaryChunkParams1;
        //$paramsForTest[1][] = $summaryChunkPArams2;

		// $footerParams = $this->blah-blah-regenerate-footer($params);	
		// $paramsForTest[2] = $footerParams;

        //$paramsForTest[2]["physical-record-count"] = $paramsForTest[2]["logical-record-count"] = 2;
        //$paramsForTest[2]["total-bonus-loyalty-points-avios-awarded"] = 300 * 2;
        //$paramsForTest[2]["total-loyalty-points-avios-awarded"] = 200 * 2;

        // -----------------------------------------------------------------------
            
        echo "<pre>";
        print_r($paramsForTest);
        echo "</pre>";
        echo "<hr/>";



        //die('end of tests');
		// AWARD FILE (1st type)
		// ------------------------------------------------------------

        //generate file name for new file TODO: use MySQL table for getting batch number and reissue
        $newFileName = $this->generateFileName("ANAINP", "00301", "0001", "0");

		$folderPath = FCPATH . "avios_test/";  // use AVIOS_HOMEDIR instead of this for production
		$awardFileName = $folderPath . $newFileName;
		$awardJSONOutput = $awardFileName . ".json";

		$aviosInputObj = new AviosFileAwardInput();

		// get cutom awards params
		//$paramsForTest = $this->debugReturnAwardInputParams();
/*
		// generate object from params
		$aviosInputObj->generateFileObjectFromParams($paramsForTest[0], array(), $paramsForTest[2]);

		// generate array with file rows from object
		$fileStrData = $aviosInputObj->convertChunksToRows();

		// write rows to file (creates EXAGB00001ANAINP0030100013)
		$result1 = $aviosInputObj->writeStrChunksToFile($awardFileName, $fileStrData);

		// generate JSON from award (creates EXAGB00001ANAINP0030100013.json)
		$result3 =  $aviosInputObj->mapObjectIntoJSONFile($awardJSONOutput);

		//Encrypt file (creates File.gpg)
        $this->encryptFile($folderPath, "OPNZA00001ANAINP003010001");

		// -------------------------------------------------------------
		// -------------------------------------------------------------
*/      $folderPath = FCPATH . "avios_test/";
        //$this->decryptFile($folderPath."out/", "OPNZA00001ANAHDB0030100010.gpg");

		// CONFIRMATION FILE (2nd type)
		// -------------------------------------------------------------

		$confimrationFileName = $folderPath . "out/". "OPNZA00001ANAHDB0030100010.gpg";
		$confirmationJSONOutput = $confimrationFileName . ".json";
		$aviosConfirmationObj = new AviosFileAwardConfirmation();

		// parse Confirmation file and save it into our object
		$result4 = $aviosConfirmationObj->mapFileIntoObject($confimrationFileName);

		// save parsed confirmation file as JSON file
		$result5 = $aviosConfirmationObj->mapObjectIntoJSONFile($confirmationJSONOutput);

	}

	public function parseConfirmationFile($fileName) {
	    //OPNZA00001ANACFM0030100021.gpg
        $folderPath = AVIOS_HOMEDIR;
        $decrypt = $this->decryptFile($folderPath."outbound/", $fileName);

        if($decrypt !== true) {
            return $decrypt;
        }

        $confimrationFileName = $folderPath . "dec/". $fileName.".txt";
        //$confirmationJSONOutput = $confimrationFileName . ".json";

        $dataFromName = $this->parseFileName($fileName);

        switch ($dataFromName["feedType"]) {
            case "ANACFM":
                $aviosConfirmationObj = new AviosFileAwardConfirmation($dataFromName["formatVersion"], $dataFromName["partnerCode"],
                    $dataFromName["feedType"]);
                break;
            case "ANAHDB":
                $aviosConfirmationObj = new AviosFileAwardHandback($dataFromName["formatVersion"], $dataFromName["partnerCode"],
                    $dataFromName["feedType"]);
                break;
        }

        // parse Confirmation file and save it into our object
        $result4 = $aviosConfirmationObj->mapFileIntoObject($confimrationFileName);

        // save parsed confirmation file as JSON file
        //$result5 = $aviosConfirmationObj->mapObjectIntoJSONFile($confirmationJSONOutput);

        if($dataFromName["feedType"] == "ANACFM") {
            $this->errorConfirmationHandler($result4, $fileName, $dataFromName['batchNumber'], $dataFromName['reissueNumber']);
        }

        //Update awards statuses from confirmation file
        if($dataFromName["feedType"] == "ANAHDB") {
            $this->logHandbackFile($result4);
        }

        return true;
    }

    public function logHandbackFile($fileObj) {

	    foreach ($fileObj->contentChunkCollection as $award) {

	        if($award["record-type"] == "1") {

	            $this->avios_logs->confirmAwardStatus($award);
            }
        }
    }

    public function errorConfirmationHandler($fileObj, $fileName, $batch, $reissue) {

	    $this->load->model("message_model");
	    $code = "0000";

        if($fileObj->contentChunkCollection[0]['message-type'] == "F") {

            $error = new Avios_error($fileObj->contentChunkCollection[0]['message-code']);
            $code = $error->code;
            $codeMess = $error->message;

            //mail about error file
            $message = "Avios return error code ".$code." - ".$codeMess.". In file ".$fileName;
            $this->message_model->send_email("avios_error@openweb.co.za", "ceo@openweb.email", "Avios award file error", $message, array());
            $this->message_model->send_email("avios_error@openweb.co.za", "sergey.gerashchenko@lamp-dev.com",
                "Avios award file error", $message, array());
        }

        $this->avios_logs->updateFileConfirmation($code, $batch, $reissue);
    }

	public function returnAwardInputParams($batchNumber, $reissueNumber) {

        $headerParams = array(
            "batch-number" 			=> $batchNumber,
            "reissue-number"		=> $reissueNumber,
        );

        $footerParams = array(
            "physical-record-count" => 0,
            "logical-record-count" => 0,
            "total-bonus-loyalty-points-avios-awarded" => "0", // TODO : can be negative include this
            "total-loyalty-points-avios-awarded" => "0", // TODO : can be negative
        );

        $awardContentArray = array();

        $allParams = array($headerParams, $awardContentArray, $footerParams);
        return $allParams;
    }

	// Hardcoded Input Award fields

	public function debugReturnAwardInputParams($params = null){

		/*  These fields were used inside sample Input Award file, sent by Avios Support Team. 

"record-type", "transaction-type",  "credit-debit",  "transaction-date",  "partner-transaction-reference" (" AWARD REFERENCE NUMBER 1001"), 
"campaign-code"  (00001GBEXA), 
"loyalty-programme-name" ("AVIOS"), "loyalty-programme-member-id" (9999999999999999"),  "member-surname" (" HOAD"),
		"loyalty-programme-currency"  ("AV"),  "total-loyalty-points" ("0000003002"),  "total-bonus-loyalty-points" ("0000001001"),
		"qualifies-for-bonus" ("N"),  "standard-loyalty-points-billing-code"  ("GBEXASTAND"),
		"bonus-loyalty-points-billing-code" ("GBEXABONUS"), "award-description" (" TRENITALIA SPA-BOOKING REF P0123 -AVIOS AWARD")

		*/


		$dummyObj = new AviosFileAwardInput();
		$headerParams = array(

				"batch-number" 			=> 1, 
				"reissue-number"		=> 0,
			);



		$footerParams = array(
			"physical-record-count"					   => 1,
			"logical-record-count" 					   => 1,
			"total-bonus-loyalty-points-avios-awarded" => "200", // TODO : can be negative include this
			"total-loyalty-points-avios-awarded" 	   => "300", // TODO : can be negative
		);


		$awardContentArray = array();
		$awardContentArray[] = array(

				"summary" => array(

						//"record-type" => "", 
						"transaction-type" => "03",  // - Award without Activity information: Only an Avios award is provided3

						"credit-debit" => "C", 

						"transaction-date" => $dummyObj->generateCurrentDate(), 
						//"debit-type" => "", 
						//"debit-transaction-reference" => "", 
						"sub-partner-code" => "", 

						//"partner-loyalty-programme-member-id" => "3242", // can be user_id
						"partner-transaction-reference" => "AWARD NUMBER 0004",

						"campaign-code" => "00001GBEXA",

						//"business-identifier-сode" => "",
						// "business-name" => "", 
					
						"loyalty-programme-name" => "AVIOS", 
						//"loyalty-programme-member-id" => "0101010103",
					
						"member-surname" => " SURNAME2", 
					//	"member-surname-2" => "", 
					//	"partner-captured-tier-status => "",
					//	"partner-captured-join-date" => "", 
					//	"partner-capture-method"  => "", 
					//	"partner-location-code-1" => "",
				    // 	"partner-location-code-2" => "", 
					//	"partner-location-code-3" => "", 
					
					//	"loyalty-programme-currency-stock-code" => "",
					//	"payment-method" => "", 

						"loyalty-programme-currency" => "AV", 
						//"transaction-currency" => "ZAR",
						//"total-transaction-value" => "20.00", 
						"total-loyalty-points" => "200", 
						"total-bonus-loyalty-points" => "300",
						"qualifies-for-bonus" => "N", 
						
						"standard-loyalty-points-billing-code" => "GBEXASTAND",
						"bonus-loyalty-points-billing-code" => "GBEXABONUS", 


						"award-description" => "PRODUCT #24 AVIOS AWARD",

					),

				"details" => array(), 

					/*array(

								array(


									//"record-type", 
									 "start-date" => $dummyObj->generateCurrentDate(), 
									//"end-date", 
									//"duration-unit", 
									//"duration-value",
									"product-category-code" => "CODE", 
									"sub-product-category-code" => "SINGLE", 
									//"product-class-code",
									//"tariff-code", 
									//"quantity", 
									"transaction-value" => "0.00",

									)			

					)
					*/

			);



		$paramsForTest = array($headerParams, $awardContentArray, $footerParams);
		return $paramsForTest;

	}


	// harcoded Confirmation fields

	public function debugReturnAwardConfirmationParams(){

		$headerParams = array(

			"batch-number" 			=> 1, 
			"reissue-number"		=> 0,
		);

		$footerParams = array(

			"physical-record-count"		=> 3,
			"logical-record-count" 		=> 1,
		);


		$awardConfirmationContentArray = array();
		$awardConfirmationContentArray[] = array(

				"message-type" => "I",
				"message-code" => "0001",
			);
		$awardConfirmationContentArray[] = array(

				"message-type" => "F",
				"message-code" => "1404", //1404 Currency Exchange Rate Unavailable

			);

		$paramsForTest = array($headerParams, $awardConfirmationContentArray, $footerParams);
		return $paramsForTest;

	}



	function generateFileName($fileType,  $versionType, $batchNumer, $reissueNumber){

		//{PartnerCode}{FileType}{VersionNumber}{BatchNumber}{ReissueNumber}

		// format numbers

		$versionType = sprintf("%0". self::FilenameVersionNumberLenth . "d", $versionType);
		$batchNumer = sprintf("%0". self::FilenameBatchNumberLenth . "d", $batchNumer);

		return $this->partnerCode . $fileType . $versionType . $batchNumer . $reissueNumber;
	}

    public function parseFileName($fileName) {

	    $partnerCode = substr($fileName, 0, 10);
	    $feedType = substr($fileName, 10, 6);
	    $formatVersion = substr($fileName, 16, 5);
	    $batchNumber = substr($fileName, 21, 4);
	    $reissueNumber = substr($fileName, 25, 1);

	    $data = array(
	        "partnerCode" => $partnerCode,
            "formatVersion" => $formatVersion,
            "feedType" => $feedType,
            "batchNumber" => $batchNumber,
            "reissueNumber" => $reissueNumber
        );

	    return $data;
    }

	function makeOrdersArray($str) {

	    if(!strpos($str, " ")) {
            return $str;
        }

        $str_array = [];

        while(strlen($str) > 0) {

            $id = strpos($str, " ") ? substr($str, 0, strpos($str, " ")) : $str;
            array_push($str_array, $id);
            $str = strpos($str, " ") ? substr($str, strpos($str, " ")+1) : "";

        }

        return $str_array;
    }


    function mapping($log_id, $params, $batchNumber, $reissueNumber) {

	    $this->load->model('membership_model');
        $this->load->model('admin/order_model');
        $this->load->model('admin/product_model');
        $this->load->model('avios/avios_logs');

        $fields =  array();
        $fileBatchNumber = sprintf("%0". self::FilenameBatchNumberLenth . "d", $batchNumber);
        $date = date("m-y");

        $dummyObj = new AviosFileAwardInput();

        $headerParams = array(
            "batch-number" 			=> $fileBatchNumber,
            "reissue-number"		=> $reissueNumber,
        );

        $footerParams = array(
            "physical-record-count" => 0,
            "logical-record-count" => 0,
            "total-bonus-loyalty-points-avios-awarded" => "0", // TODO : can be negative include this
            "total-loyalty-points-avios-awarded" => "0", // TODO : can be negative
        );

        //For aech array of input params we generate summary block
        for($i = 0; $i < count($params); $i++) {

            $user_id = $params[$i]['user_id'];
            $order_id_array = $this->makeOrdersArray($params[$i]['order_id']);
            $order_id = $params[$i]['order_id'];

            if(is_array($order_id_array)) {
                $order_id = $order_id_array[0];
            }

            //Referense data gathering
            if($params[$i]['order_id'] == 0) {
                $product_id = 7777; //some "interesting" decision, for right way need add special product in DB
                $biling_cycle = "admin";
            } else {
                $order_data = $this->order_model->get_order_data($order_id);
                $product_id = $order_data["product"];
                $biling_cycle = $order_data["billing_cycle"];
            }

            $reference = $log_id." ".$user_id." ".$date." ".$biling_cycle;

            //award description gathering
            if($product_id == 7777) {
                $product_name = "admin reward";
            } else {
                $product_data = $this->product_model->get_product_data($product_id);
                $product_name = $product_data['product_settings']['name'];
            }

            $award_description = $product_id." ".$product_name." ".$date." ".$biling_cycle;

            //Get surname and programme-name
            $surname = $this->membership_model->get_second_name_by_id($user_id);

            $fields[$i] = array(

                "summary" => array(

                    //"record-type" => "",
                    "transaction-type" => "03",  // - Award without Activity information: Only an Avios award is provided3

                    "credit-debit" => "C",

                    "transaction-date" => $dummyObj->generateCurrentDate(),//$params[$i]['date'], // TODO come back after testing
                    //"debit-type" => "",
                    //"debit-transaction-reference" => "",
                    "sub-partner-code" => "",

                    //"partner-loyalty-programme-member-id" => "3242", // can be user_id
                    "partner-transaction-reference" => $reference, //$params[$i]['partner-transaction-reference'],

                    "campaign-code" => $this->campaignCode,

                    //"business-identifier-сode" => "",
                    // "business-name" => "",

                    "loyalty-programme-name" => $params[$i]['loyalty-programme-name'], // TODO depends of member_id
                    "loyalty-programme-member-id" => $params[$i]['loyalty-programme-member-id'], // TODO get from DB

                    "member-surname" => $surname, //$params[$i]['member-surname'], // TODO get from DB
                    //	"member-surname-2" => "",
                    //	"partner-captured-tier-status => "",
                    //	"partner-captured-join-date" => "",
                    //	"partner-capture-method"  => "",
                    //	"partner-location-code-1" => "",
                    // 	"partner-location-code-2" => "",
                    //	"partner-location-code-3" => "",

                    //	"loyalty-programme-currency-stock-code" => "",
                    //	"payment-method" => "",

                    "loyalty-programme-currency" => "AV",
                    //"transaction-currency" => "ZAR",
                    //"total-transaction-value" => "20.00",
                    "total-loyalty-points" => $params[$i]['points']+$params[$i]['bonus-points'],
                    "total-bonus-loyalty-points" => $params[$i]['bonus-points'],
                    "qualifies-for-bonus" => "N",

                    "bonus-loyalty-points-billing-code" => $params[$i]['bonus-billing-code'],
                    "standard-loyalty-points-billing-code" => $params[$i]['billing-code'],

                    "award-description" => $award_description,
                ),

                "details" => array(),

            );

            $footerParams["physical-record-count"] += 1;
            $footerParams["logical-record-count"] += 1;
            $footerParams["total-bonus-loyalty-points-avios-awarded"] += $params[$i]['bonus-points']; // TODO : can be negative include this
            $footerParams["total-loyalty-points-avios-awarded"] += $params[$i]['points']+$params[$i]['bonus-points'];


            $this->avios_logs->addAwardRow(
                $log_id,
                $params[$i]['user_id'],
                serialize($order_id_array),
                $product_name, // reason
                $params[$i]["points"],
                $params[$i]['loyalty-programme-member-id'],
                $params[$i]['bonus-points'],
                $params[$i]['billing-code']
            );
        }


        return array($headerParams, $fields, $footerParams);

    }

    public function encryptFile($pathToFile, $fileName) {

        $gpg = new Crypt_GPG(array('homedir' => '/home/home/.gnupg')); //TODO : path to .gnupg on server '/home/.gnupg'

        $gpg->addEncryptKey('boomi');
        // you can use any fopen-able stream
        try {
            $gpg->encryptFile($pathToFile . $fileName, $pathToFile . "inbound/" . $fileName . ".gpg");
        } catch (Exception $exception) {
            return $exception->getMessage();
        }

        return true;
    }

    public function decryptFile($pathToFile, $fileName) {

        $gpg = new Crypt_GPG(array('homedir' => '/home/home/.gnupg')); //TODO : path to .gnupg on server '/home/.gnupg'

        $gpg->addDecryptKey('bfbgmain@gmail.com', 'J(A9kd@%91--4jnga;lo');

        try {
            $gpg->decryptFile($pathToFile.$fileName, AVIOS_HOMEDIR."dec/".$fileName.".txt");
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return true;
    }
}