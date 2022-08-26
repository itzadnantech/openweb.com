<!-- http://home.openweb.co.za/
Login details for VCS Development Terminal:
https://www.vcs.co.za> Admin Login > Virtual Terminal > login
Username:  openweb
Password:  Maniac200
Terminal ID:  9506  (test)
       		  6125  (live)

       		  
	
Web Site URL: http://www.openweb.co.za
Go to https://www.vcs.co.za> Admin Login > Virtual Terminal > login > Merchant Administration > 3. Vcs Interfacing (page 1).
Approved page URL:  https://mastercp.openweb.co.za/payment_success.asp
Declined page URL:  https://mastercp.openweb.co.za/payment_fail.asp
Http method : FETCH&POST

http://home.openweb.co.za/product/authorisation_response_success

Test Card Number	
------------------------------------------------------------------------------------
number				cvc 					Fixed simulated authorisation response	|
																					|
4242424242424242    123 					XXXXXXApproved							|
											where XXXXXX = any alphanumeric value	|
																					|
5454545454545454							XXXXXXApproved							|
											where XXXXXX = any alphanumeric value	|
																					|
5221001010000024	Use any valid 			Call									|
					expiry  date.													|
																					|
5221001010000032							Invalid Expiry							|
																					|
5221001010000040							No Active Connection to Acquirer exists	|
------------------------------------------------------------------------------------

THE RECURRING TEMPLATE ADMINISTRATION WEB SERVICE.
https://www.vcs.co.za/wscs/svc_virtualrecur.asmx

ActivateCCTransaction
AddCCTransaction
DeleteCCTransaction
GetCCTransactionDetail
GetCCTransactionList
SuspendCCTransaction
UpdateCCNumber
UpdateCCTransaction


Go to https://www.vcs.co.za> Admin Login > Virtual Terminal > login > Merchant Administration > 10. Callback Settings
Approved Callback : http://vcs.openweb.co.za/vcs_approved_callback_control.asp
Declined Callback: http://vcs.openweb.co.za/vcs_failed_callback_control.asp
Callback Protocol: Http
Callback Method: POST
Response Format: Name value pairs

callback 
http://cp.openweb.co.za/vcs.asp?p1=9506&p2=139460596912345&p3=623955APPROVED%20%20&p4=&p5=ling
								&p6=1.00&p7=Visa&p8=Goods&p9=&p10=00
p1==VCS Terminal ID
p2==Reference Number
p3==Reference Number
p4==Constant: Duplicate (if applicable)
p5==Card Holder Name
p6==Amount
p7==Card Type
p8==Description of Goods
p9==Cardholder email Address
p10==Budget Period


http://home.openweb.co.za/payment_fail?
p1=9506&
p2=139502629912345&
p3=This+cellular+network+is+not+supported+for+this+payment+type%2c+please+note+that+only+MTN+and+Vodacom+supports+payD+%2f+Debit+Card+with+PIN+payments&
p4=&
p5=PAYD+from+15158117030&
p6=1.00&
p7=payd&
p8=Goods&
p9=&
p10=00&
p11=&
p12=IS&
pam=81KJS81KW9SK1&
m_1=&m_2=&m_3=&m_4=&m_5=&m_6=&m_7=&m_8=&m_9=&
m_10=&
CardHolderIpAddr=112.54.204.254&
MaskedCardNumber=000000******0000&
TransactionType=Authorisation
-->

<!--  139460596912345  -->

<form method="POST" action="https://www.vcs.co.za/vvonline/vcspay.aspx">
	<!--VCS Terminal Id  test Terminal ID 9506 / live Terminal ID 6125-->
	<?php $terminalID = '9506'; ?>
	<input type="text" name="p1" value="<?php echo $terminalID; ?>">
	
	<!-- If you send a request to VCS and you do get a response then you cannot use that reference number again.  -->
	<!-- Reference Number ccyymmdd  must 15 chars -->
	<?php $reference = time().'12345';?>
	<input type="text" name="p2" value="<?php echo $reference;?>"> 
	
	<!-- Description of Goods  -->
	<?php $description = 'Goods';?>
	<input type="text" name="p3" value="<?php echo $description;?>">
	
	<!-- Amount -->
	<?php $amount = '1.00';?>
	<input type="text" name="p4" value="<?php echo $amount;?>">
	
	<!-- Occurrence frequency M--monthly  -->
	<!-- <input type="text" name="p7" value="M"> -->
	
	<!-- hash code -->
	<?php $str = $terminalID.$reference.$description.$amount.'Help';
		  $hash = md5($str);
	?>
	<input type="text" name="hash" value="<?php echo $hash?>">

	<input type="submit" value="Proceed to Payment">
</form>


