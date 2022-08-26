<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Call_back extends CI_Controller
{
	function call_back_success()
	{
		$data = $_POST;
		$str = '';
		if(is_array($data)){
			foreach($data as $key => $value){
				$str .= $key.':		'.$value."\r\n";
			}
		}else{
			$str = $data;
		}
		$str .= "\r\n\r\n";
		$handle=fopen("tt.txt", "a+");
		fwrite($handle, $str);
		fclose($handle); 
	}
	
	function call_back_fail()
	{
		$data = !empty($_POST) ? $_POST : $_GET;
		$str = '';
		if(is_array($data)){
			foreach($data as $key => $value){
				$str .= $key.':		'.$value."\r\n";
			}
		}else{
			$str = $data;
		}
		$str .= "\r\n\r\n";
		$handle=fopen("tt.txt", "a+");
		fwrite($handle, $str);
		fclose($handle);
	}

}
