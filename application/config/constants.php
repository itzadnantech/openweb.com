<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

/*
|--------------------------------------------------------------------------
| Pagenation per page number
|--------------------------------------------------------------------------
|
| These modes are used when working with pagenation
|
*/
define('NUM_PER_PAGE', 10);

/* End of file constants.php */
/* Location: ./application/config/constants.php */

// App constans
define('TERMS_OF_SERVICE_LINK','http://openweb.co.za/terms-conditions/');
define('SUPPORT_LINK','http://openweb.co.za/support/');
define("AUTO_CREATE_SUCCESS_MESSAGE","Congratulations!  Your new OpenWeb ADSL account has been Instantly Activated! <br/>
            Herewith your new ADSL account details.  Kindly configure these details into your ADSL router.
            Your OpenWeb Control Panel login details have been emailed to you in a separate mail.");

define('EFT_MESSAGE_FOR_MANUAL','Thank you for your order. Your new account details will be sent shortly.');

define('STAGE_HOST', 'open-web.loc');

define("ADDITIONAL_ORDER_MESSAGE", "
            <p>By creating a username and password and proceeding to the next step you confirm that:<br/><br/>
-                      you have a working Telkom ADSL line installed that can accommodate the package you have chosen;<br/><br/>
-                      the package chosen meets your envisaged needs;<br/><br/>
<span id='span-order-message' style='display: none;'></span> - you understand from having read and accepted the <a href='http://openweb.co.za/terms-conditions/' target='_blank'>terms and conditions</a> and prerequisite debit order mandate that once you have signed up, the only way to cancel the ADSL account and associated debit/credit order is by filling in the required online <a href='http://openweb.co.za/cancellations/' target='_blank'>form</a> and further giving one calendar months’ notice (clause 8 of the terms);<br/><br/>	

- you consent to use the services of OpenWeb, and thereby appreciate that the \"cooling off\" period referred to in the Electronic Communications and Transactions Act is not applicable to this transaction.

");
define("API_BINDTO","0:0");
define("API_URL","http://www.isdsl.net/api/api.php?wsdl");
define("API_ST_URL","https://broadband.is/api/api.php?wsdl");
define("API_REST_URL","https://www.isdsl.net/api/rest");

// Do not touch it, really ! : )
define("FLAT_UI",  false);


// Invoice rows
//define("INVOICE_VAT_ROW", "*All prices are inclusive of 14% VAT.");
define("INVOICE_VAT_ROW", "*Non-Vatable");
define("INVOICE_ORGANIZATION_ID", "Company Registration: "); // was 'Vat Number: '
//define("BASE")

define("GPG_HOMEDIR", FCPATH . "gpg_keys");
define("AVIOS_HOMEDIR", FCPATH . "application/avios/");

define("SECRET_KEY", "0d97f7de826777e31221f60ac050aacf5fbf9900");