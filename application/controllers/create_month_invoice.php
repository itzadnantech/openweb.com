<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Create_month_invoice extends CI_Controller {
	
	function index()
	{
		$last_month = date('Y-m-01',strtotime(date('Y',time()).'-'.(date('m',time())-1)));
		$today  = date('Y-m-01', time());
		$query = $this->db->query('select distinct user_name from invoices 
									where create_date between "'.$last_month.'" and "'.$today.'"');
		$result = $query->result_array();
		//echo '<pre>';print_r($result);die;

		if ($result){
			foreach ($result as $name){
				$username = $name['user_name'];
				$month_invoice = $this->get_invoice_by_user($username, $last_month, $today);
				$month_invoice_id = $this->save_month_invoice($username, $month_invoice, $last_month);

				$pdf_id = $this->save_PDF($username, $month_invoice, $last_month, $month_invoice_id);
				$this->send_email($pdf_id, $username, $last_month);
			}
		}
	}
	
	function get_invoice_by_user($user, $last_month, $today)
	{
		$query = $this->db->query('select * from invoices where  user_name = "'.$user.'"
								   and create_date between "'.$last_month.'" and "'.$today.'"');
		$result = $query->result_array();
		return $result;
	}
	
	function save_month_invoice($username, $month_invoice, $last_month)
	{
		$ids = '';
		$total = 0;
		$id_user = $this->get_user_id($username);

		foreach ($month_invoice as $iv){
			$order_id = $iv['order_id'];
			$price = $this->get_order_price($order_id);
			$invoice_id = $iv['id'];
			$ids .= $invoice_id.',';
			$total = $total + $price;
		}
		$month = date('F Y', strtotime($last_month));
		$title = "Tax Invoice for $username in $month";
		
		//save the invoice
		$invoice = array(
			'invoice_name' => $title,
			'create_date' => date('Y-m-d', time()),
			'user_name' => $username,
			'invoices_id' => $ids,
			'type' => 'auto',
			'user_id' => $id_user,
		);
		$this->db->insert('invoices', $invoice);
		$month_invoice_id = $this->db->insert_id();
		return $month_invoice_id;
	}
	
	function get_order_price($order_id)
	{
		$this->db->where('id',$order_id);
		$query = $this->db->get('orders');
		$result = $query->first_row();
		$price = $result->price;
		return $price;
	}
	
	function get_user_id($username)
	{
		$this->db->where('username',$username);
		$query = $this->db->get('membership');
		$result = $query->first_row();
		$user_id = $result->id;
		return $user_id;
	}
	
	function get_product_name($order_id)
	{
		$this->db->where('id',$order_id);
		$query = $this->db->get('orders');
		$result = $query->first_row();
		$id_prdoct = $result->product;

		if($id_prdoct == 0){
            $this->db->where('order_id',$order_id);
            $query = $this->db->get('fibre_orders');
            $result = $query->first_row();
            $product_name = $result->product_name;

            if(!empty($product_name))
                return $product_name;
        }

		$this->db->where('id',$id_prdoct);
		$query = $this->db->get('products');
		$result = $query->first_row();
		$product_name = $result->name;
		
		return $product_name;
	}
	
	function save_PDF($username, $month_invoice, $last_month, $month_invoice_id)
	{	
		//$this->load->library('FPDF/fpdf');
		//$pdf = new FPDF();
		$this->load->library('tfpdf/MC_Table');
		$pdf=new MC_Table();
		
		$user_billing = $this->get_user_billing_info($username);
		if($user_billing){
			$billing_name = $user_billing['billing_name'];
			$user_address = $user_billing['address_1'].' '.$user_billing['address_2'];
			$user_city = $user_billing['city'];
			$user_country = $user_billing['country'];
			$user_province = $user_billing['province'];
			$user_phone = 'Phone: '.$user_billing['contact_number'];
			$user_p_c = $user_province.', '.$user_country;
		}else{
			$billing_name = '';
			$user_address = '';
			$user_city = '';
			$user_country = '';
			$user_province = '';
			$user_phone = '';
			$user_p_c = '';
		}
		
		$open_ISP = $this->get_open_ISP();
		$open_name = $open_ISP['name'];
		$vat_number = $open_ISP['vat_number'];
		$country = $open_ISP['country'];
		$province = $open_ISP['province'];
		$address = $open_ISP['address'];
		$phone = $open_ISP['phone'];
		
		$month = date('F Y', strtotime($last_month));
		$title = "Tax Invoice for $username in $month";
		
		//create PDF file page
		$pdf->AddPage();
		
		$pdf->SetFont('Arial','',20);
//		$image = base_url().'img/main.png';
		$image = '/home/home/public_html/img/main.png';
		$pdf->Image($image,70,5,60);
		
		$pdf->SetFont('Arial','',20);
		$pdf->SetXY(40, 30 );
		$pdf->Cell(20,8,$title,'C',true);
		$pdf->Ln();
		
		//set invoice info
		$invoice_date = date('d/m/Y', time()) ;
		$invoice_id_format = "Tax Inv # : $month_invoice_id";
		$invoice_date_format = "Date : $invoice_date";
		 
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(20, 4, $invoice_id_format, '',true);
		$pdf->Cell(36, 10, $invoice_date_format, 0,0,'R',false,'');
		$pdf->Ln();
		
		//set open info
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(20,4,$open_name,'',true);
		$pdf->Cell(185,3,$billing_name,0,0,'R',false,'');
		$pdf->Ln();
		 
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(20,3, INVOICE_ORGANIZATION_ID.$vat_number,'',true);
		$pdf->Cell(185,3,$user_address.' '.$user_city,0,0,'R',false,'');
		$pdf->Ln();
		 
		$pdf->Cell(20,3, $address,'',true);
		$pdf->Cell(185,3, $user_p_c,0,0,'R',false,'');
		$pdf->Ln();
		 
		$pdf->Cell(20,3, $province.', '.$country,'',true);
		$pdf->Cell(185,3, $user_phone,0,0,'R',false,'');
		$pdf->Ln();
		 
		$pdf->Cell(20,3,'Phone: '.$phone,'',true);
		$pdf->Ln();
		
		//set the body
		$pdf->SetFillColor(128,128,128);
		$pdf->SetTextColor(255);
		$pdf->SetDrawColor(92,92,92);
		
		$pdf->Cell(95,8,"Product",1,0,'C',true);
	    $pdf->Cell(40,8,"Date Ordered",1,0,'C',true);
	    $pdf->Cell(50,8,"Cost this month",1,0,'C',true);
	    $pdf->Ln();
		
		$total = 0;
		$pdf->SetWidths(array(110,45,30));
		//$pdf->SetAligns('C');
		foreach ($month_invoice as $iv){
			$order_id = $iv['order_id'];
			$product_name = $this->get_product_name($order_id);
			$date = date('Y-m-d', strtotime($iv['create_date']));
			$price = $this->get_order_price($order_id);
			$cost = 'R '.$price;
			$total = $total + $price;
				
			$pdf->Row(array($product_name, $date, $cost));
		}	
		
		$pdf->SetFillColor(255,255,255);
		$pdf->Ln(1);
		$pdf->Cell(185,8,'Total: R '.$total, 0, 0,'R',true);
		$pdf->Ln();
		$pdf->Cell(0,8, INVOICE_VAT_ROW, 0, 0,'',true);
		
		//save the pdf file to local file 
		$path_name = APPPATH.'PDFfiles/'.$username;  
		if(is_dir($path_name) == false){
			mkdir($path_name,0777);
		}
		
		$file_name = $month_invoice_id.'.pdf';
		$file_save_path = $path_name.'/'.$file_name;   
		$pdf->Output($file_save_path, 'F');
		//$pdf->close();
		
		//save the pdf
		$pdf_data = array(
			'name' => $file_name,
			'path' => $file_save_path,
			'create_date' => date('Y-m-d H:i:s',strtotime('now')),
			'user_name' => $username,
			'invoices_id' => $month_invoice_id
		);
		$result = $this->db->insert('invoice_pdf',$pdf_data);
		$pdf_id = $this->db->insert_id();
		return $pdf_id;
	}
	
	function send_email($pdf_id, $username, $month)
	{
		$this->load->library('email');
		
		$this->db->select('name,path');
		$this->db->where('id',$pdf_id);
		$query = $this->db->get('invoice_pdf');
		$pdf_result = $query->first_row('array');
		$pdf_name = $pdf_result['name'];
		$pdf_path = $pdf_result['path'];
		
		$this->db->select('first_name,email_address');
		$this->db->where('username', $username);
		$query = $this->db->get('membership');
		$result = $query->first_row('array');
		$email = $result['email_address'];
		$name = $result['first_name'];
		
		$link = "http://home.openweb.co.za/user/invoices";
		$msg = "Dear $name,
Please visit the following link for your invoice.
		$link
If you have any billing queries, please do not hesitate to contact admin@openweb.co.za
Kind regards
Keoma Wright
Founder
OpenWeb.co.za";
		$this->email->from('admin@openweb.com', 'OpenWeb Home');
		$this->email->to($email);
		$this->email->subject("Tax Invoice for $month");
		$this->email->message($msg);
		$this->email->attach($pdf_path);
		$this->email->send();
        $this->email->clear(TRUE);
	}
	
	function get_open_ISP()
	{
		$query = $this->db->get('openisp_cc');
		$result = $query->result_array();
		return $result[0];
	}
	
	function get_user_billing_info($user)
	{
		$this->db->where('username', $user);
		$query = $this->db->get('billing');
		$result = $query->result_array();
		if($result){
			return $result[0];
		}else{
			return false;
		}
	}
}
?>
