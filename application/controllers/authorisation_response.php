<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* p1 : VCS Terminal ID, allocated by VCS
 * p2 : Unique Transaction Reference Number from the incoming request.
 * p3 : Authorisation Response returned by the bank. 
 * 		For an APPROVED bank response expect:
 * 		Characters	The first 6 characters contain the 1 �C 6 bank��s alphanumeric authorisation number, e.g. 123456
 *		
 *		Characters	From character 7 the constant word 7 �C 16 APPROVED, left justified right space filled.
 * p5 : Name  entered  by  the  cardholder  on  the  VCS authorisation page.
 * p6 : Amount authorised by the bank.
 * p7 : Card  Type	selected  by  the  cardholder  from dropdown menu on the VCS page: MasterCard,Visa, Amex or Diners.
 * p8 : Description of Goods from the incoming request.
 * p10 : Budget Period entered by the cardholder on the VCS authorisation page. 00 = straight
 * p11 : Expiry Date entered by the cardholder on the VCS authorisation page �C format yymm.
 * p12 : Authorisation Response Code received from the bank, e.g.
 *		 00 = Approved or 0 = Approved (where Nedbank is the acquiring bank.)
 *	     05 = Do not honour etc.
 * pam �� PAM �C PERSONAL AUTHENTICATION MESSAGE
 * ip ��IP address 
 * card_number : Masked Card Number
 *				 As it was entered by the cardholder on the payment page e.g. 545454******1234.
 * uti : Unique transaction id required in South Africa   
 */

class Authorisation_response extends CI_Controller 
{
	function __construct() {
		parent::__construct();
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
		$this->output->set_header("Pragma: no-cache");
		$this->load->model('user/product_model');
		$this->load->model('user/category_model');
		$this->load->model('user/is_classes');
		$this->load->model('user/user_model');
		$this->load->model('user/cloudsl_model');
		
		$category_list = $this->category_model->get_categories();
		$this->site_data['category_list'] = $category_list;
		
		$sub_assoc = $this->category_model->get_subcategories_assoc();
		$this->site_data['subcategories_assoc'] = $sub_assoc;
		
		$last_login_time = $this->session->userdata('last_login_time');
		$this->site_data['last_login_time'] = $last_login_time;
		
		$cart = $this->session->userdata('cart');
		$this->site_data['cart'] = $cart;
		
		$username = $this->session->userdata('username');
		$this->site_data['username'] = $username;
		
		$first_name = $this->membership_model->get_name($username);
		$this->site_data['first_name'] = $first_name;
	}
	
	function get_rand_str()
	{
		$chars = '0123456789';
		$str = '';
		for ( $i = 0; $i <5; $i++ ){
			$str .= $chars[mt_rand(0, strlen($chars) - 1)];
		}
		return 'isp'.$str;
	}
	
	function response_success()
	{
        die;
		if(isset($_POST['m_8'])&&$_POST['m_8']!=''){
			$credit = $_POST['m_1'];
			$username = $_POST['m_2'];
			//$number = $this->membership_model->get_number($user);
			//$ac_email = $this->membership_model->get_email($user);
			//$user_name_nice = $this->membership_model->get_user_name_nice($user);
			$id_user = $this->membership_model->get_user_id($username);
			$this->cloudsl_model->add_credit($id_user,$credit);
			$username=$this->site_data['username'];
			$ow=$this->site_data['ow'];
			$id = $this->user_model->get_user_id($username);
			$cloudsl = $this->cloudsl_model->get_user_cloudsl($id,$ow);
			$data['msg'] = "Your credit has been successfully loaded.";
			$data['main_content'] = 'user/addcredit';
			$data['sidebar'] = TRUE;
			$this->load->view('user/includes/template', $data);
			//redirect('user/addcredit');
		}
		else{
		if($_POST && (strpos($_POST['p3'], "APPROVED") != false)){
			$post_data = $_POST;
			$product = $post_data['m_1']; //product id
			$user = $post_data['m_2']; //username
			$acc_username = !empty($post_data['m_3']) ? $post_data['m_3'] : $this->get_rand_str(); //account username
			$acc_password = !empty($post_data['m_4']) ? $post_data['m_4'] : $this->get_rand_str(); //account password
			$realm = $post_data['m_5']; //realm
			$product_name = $post_data['p8'];
			$price = $post_data['p6'];
			$m_6 = $post_data['m_6']; //mark it to distinguish between user panel and client panel
			$billing_cycle = $post_data['m_7']; //order billing cycle
			
			$data = array(
				'username' => $user,
				'product_id' => $product,
				'acc_username' => $acc_username,
				'acc_password' => $acc_password,
				'payment_type' => 'credit_card',
				'choose_cycle' => $billing_cycle,
			);
			$order_id = $this->product_model->insert_order($data);
			$invoice_id = $this->user_model->save_invoices($order_id, $user);
			$this->getPDF($invoice_id, $user, $product_name, $price);
			
			$auto = $this->product_model->get_is_auto($product);
			$error = '';
			if($auto){
				if (!empty($product)) {
					$number = $this->membership_model->get_number($user);//get mobile number from billing
					$ac_email = $this->membership_model->get_email($user);//get email address from membership
					$user_name_nice = $this->membership_model->get_user_name_nice($user);//get full name from membership
			
					$class = $this->product_model->get_is_class($order_id);//get class from products
					//$realm_data = $this->product_model->get_is_details($class);//get user and pwd from realm

                    $realm_data = $this->order_model->get_realm_data_by_order_id($order_id, $class);

					$rl_user = $realm_data['user'];
					$rl_pass = $realm_data['pass'];
			
					$sess = 0;
					$new_account_data = $this->product_model->get_order_data($order_id);
					$comment = $new_account_data['account_comment'];//get account_comment form orders
					$acc_realm_user = $acc_username.'@'.$realm;
						
					//after successfull pay the order -->active the order and add to ISDSL
					//Add to ISDSL
					$sess = $this->is_classes->is_connect_new($rl_user, $rl_pass); //get session_id
					$resp = $this->is_classes->add_realm_new($sess, $class, $acc_username, $acc_password, $comment, $ac_email);
					if ($resp == 5 || $resp == 8 || $resp == 11) {
						$error = "There was an error (code: $resp). Please try again.";
					} else{
						//if sunccessfully payment then send email and invoice , active orders
						$this->load->model('admin/order_model');
						$this->order_model->email_activation($ac_email, $product_name, $acc_username, $realm, $acc_password, $user);
						$this->order_model->set_activated($order_id);
			
						//use admin order_model
						$sms_content = "Your ADSL product has been successfully created. See email for more details. Username: $acc_realm_user Password: $acc_password - OpenWeb";
						$this->order_model->send_sms($number, $sms_content);
					}
					$data['acc_username'] = $acc_realm_user;
					$data['acc_password'] = $acc_password;
					$data['comment'] = $comment;
				}
			}
			$this->session->set_userdata('cart', '');
			$this->site_data['cart'] = '';
			$data['error'] = $error;
			$data['product_name'] = $product_name;
			$data['auto'] = $auto;
			
			$message_data = array(
					'id' => 5,
			);
			$message = $this->message_model->get_message($message_data);
			$data['message'] = $message;
			
			if($m_6 == 'client'){
				$data['main_content'] = 'client/congratulations';
				$data['sidebar'] = FALSE;
				$this->load->view('client/includes/template', $data);
			}else{
				$data['sidebar'] = TRUE;
				$data['main_content'] = 'user/product/congratulations';
				$this->load->view('user/includes/template', $data);
			}
		}else{
			die("There have some errors about the payment, please try it again.");			
		}	
	}
	}
	function response_fail()
	{
		if(!empty($_POST)){
			$error = $_POST['p3'];
			$m_6 = $_POST['m_6'];
		}elseif (!empty($_GET)){
			$error = $_GET['p3'];
			$m_6 = $_GET['m_6'];
		}
		
		$data['error'] = $error;
		if($m_6 == 'client'){
			$data['sidebar'] = FALSE;
			$data['main_content'] = 'client/fail';
			$this->load->view('client/includes/template', $data);
		}else{
			$data['sidebar'] = TRUE;
			$data['main_content'] = 'user/product/fail';
			$this->load->view('user/includes/template', $data);
		}
	}
	
	function getPDF($invoice_id, $username, $product_name, $price)
	{
		$this->load->model('admin/user_model');
	
		$this->load->library('tfpdf/MC_Table');
		$pdf=new MC_Table();
	
		$date = date('Y-m-d', time());
		$cost = 'R '.$price;
	
		$user_billing = $this->get_user_billing_info($username);
		$billing_name = $user_billing['billing_name'];
		$user_address = $user_billing['address'];
		$user_city = $user_billing['city'];
		$user_country = $user_billing['country'];
		$user_province = $user_billing['province'];
		$user_phone = 'Phone: '.$user_billing['phone'];
		$user_p_c = $user_province.', '.$user_country;
	
		$open_ISP = $this->get_open_ISP();
		$open_name = $open_ISP['name'];
		$vat_number = $open_ISP['vat_number'];
		$country = $open_ISP['country'];
		$province = $open_ISP['province'];
		$address = $open_ISP['address'];
		$phone = $open_ISP['phone'];
	
		$title = 'New Order Tax Invoice for '.$date;
		$pdf->AddPage();
	
		$pdf->SetFont('Arial','',20);
		$image = base_url().'img/main.png';
		$pdf->Image($image,70,5,60);
	
		$pdf->SetFont('Arial','',20);
		$pdf->SetXY(40, 30 );
		$pdf->Cell(20,8,$title,'C',true);
		$pdf->Ln();
		 
		//set invoice info
		$invoice_date = date('d/m/Y', time()) ;
		$invoice_id_format = "Tax Inv # : $invoice_id";
		$invoice_date_format = "Date : $invoice_date";
		 
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(20, 4, $invoice_id_format, '',true);
		$pdf->Cell(36, 10, $invoice_date_format, 0,0,'R',false,'');
		$pdf->Ln();
		 
		//set open info
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(20,4,$open_name,'',true);
		//$pdf->SetXY(150,50);
		$pdf->Cell(185,3,$billing_name,0,0,'R',false,'');
		$pdf->Ln();
		 
		//Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(20,3,INVOICE_ORGANIZATION_ID.$vat_number,'',true);
		$pdf->Cell(185,3,$user_address.' '.$user_city,0,0,'R',false,'');
		$pdf->Ln();
		 
		$pdf->Cell(20,3,$address,'',true);
		$pdf->Cell(185,3, $user_p_c,0,0,'R',false,'');
		$pdf->Ln();
		 
		$pdf->Cell(20,3,$province.', '.$country,'',true);
		$pdf->Cell(185,3, $user_phone,0,0,'R',false,'');
		$pdf->Ln();
		 
		$pdf->Cell(20,3,'Phone: '.$phone,'',true);
		$pdf->Ln();
	
		//set the body
		$pdf->SetFillColor(128,128,128);
		$pdf->SetTextColor(255);
		$pdf->SetDrawColor(92,92,92);
	
		//$pdf->Cell(50,8,"Username",1,0,'C',true);
		$pdf->Cell(95,8,"Product",1,0,'C',true);
		$pdf->Cell(40,8,"Date Ordered",1,0,'C',true);
		$pdf->Cell(50,8,"Cost this month",1,0,'C',true);
		$pdf->Ln();
	
		//$pdf->SetFillColor(224,235,255);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetTextColor(0);
		 
		$pdf->SetWidths(array(95,40,50));
		$pdf->Row(array($product_name, $date, $cost));
	
		$pdf->SetFillColor(255,255,255);
		$pdf->Ln(1);
		$pdf->Cell(185,8,'Total:'.$cost, 0, 0,'R',true);
		$pdf->Ln();
		$pdf->Cell(0,8, INVOICE_VAT_ROW, 0, 0,'',true);
		$pdf->Ln();
		$pdf->Ln();
		$pdf->Write(8, 'If you are on Debit Order you do not need to pay this invoice.');
		$pdf->Cell(90,8,'Banking Details:',0,0,'R',false,'');
		$pdf->Ln();
		$pdf->Write(8, 'Please note, accounts are payable on the 27th of each month,');
		$pdf->Cell(89,8,'Bank: ABSA',0,0,'R',false,'');
		$pdf->Ln();
		$pdf->Write(8, 'for the following months access.');
		$pdf->Cell(135,8,'Account Number: 4064449626',0,0,'R',false,'');
		$pdf->Ln();
		$pdf->Write(8, 'Please remember, you have to send us proof of payment,');
		$pdf->Cell(96,8,'Account Type: Cheque',0,0,'R',false,'');
		$pdf->Ln();
		$pdf->Write(8, 'otherwise we cannot honour the payment.');
		$pdf->Cell(120,8,'Branch Code: 632005',0,0,'R',false,'');
		$pdf->Ln();
		$pdf->Write(8, 'Kindly email your proof of payment to : admin@openweb.co.za');
		$pdf->Cell(88,8,'Reference: 45345 45345',0,0,'R',false,'');
		//$pdf->Ln();
		//$pdf->Write(8, 'Fax proof to: 0866912166');
		 
		$title = 'New Order Invoice for '.$date;
	
		$path_name = APPPATH.'PDFfiles/'.$username;
		if(is_dir($path_name) == false){
			mkdir($path_name,0777);
		}
	
		$file_name = $invoice_id.'.pdf';
		$file_save_path = $path_name.'/'.$file_name;
		$pdf->Output($file_save_path, 'F');
	
		$data = array(
				'name' => $file_name,
				'path' => $file_save_path,
				'create_date' => date('Y-m-d H:i:s',strtotime('now')),
				'user_name' => $username,
				'invoices_id' => $invoice_id
		);
		$id = $this->user_model->save_pdf($invoice_id, $data);
	
		$this->load->model('admin/order_model');
		$this->order_model->email_invoices_individual($username, $id);
	}
	
	function get_user_billing_info($username){
		$this->load->model('user/user_model');
		$user_list = $this->user_model->get_user_data($username);
		$result = $user_list['user_billing'];
		if($result){
			$billing_name = $result['billing_name'];
			$address = $result['address_1'].' '.$result['address_2'];
			$city = $result['city'];
			$province = $result['province'];
			$country = $result['country'];
			$phone = $result['contact_number'];
				
			$billing_data = array(
					'billing_name' => $billing_name,
					'address' => $address,
					'city' => $city,
					'province' => $province,
					'country' => $country,
					'phone' => $phone,
			);
			return $billing_data;
		}else{
			return false;
		}
	}
	
	function get_open_ISP(){
		$result = $this->user_model->get_open_ISP();
		return $result;
	}
}