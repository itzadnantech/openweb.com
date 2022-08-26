<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Control extends CI_Controller {
	function index () {

        $this->load->model('membership_model');

        if($this->session->userdata('is_logged_in')) {
            redirect('/user/dashboard', 'refresh');
        }

        $cookie = isset($_COOKIE['openweb-login']) ? $_COOKIE['openweb-login'] : '';

        if ($cookie) {

            list ($user, $token, $mac) = explode(':', $cookie);
            if (!hash_equals(hash_hmac('sha256', $user . ':' . $token, SECRET_KEY), $mac)) {
                return false;
            }


            $userData = $this->membership_model->getLoginToken($user);
            $usertoken = $userData[0]['login_token'];
            $role = $userData[0]['role'];

            $log = $this->membership_model->get_login_info($user);
            $last_login = date('Y-m-d', strtotime($log['date']));
            $ow = $this->membership_model->get_ow($user);

            if (hash_equals($usertoken, $token)) {

                $data = array(
                    'username' => $user,
                    'is_logged_in' => true,
                    'role' => $role,
                    'last_login_time' => $last_login,
                    'ow' => $ow,
                );
                $this->session->set_userdata($data);
                redirect('/user/dashboard', 'refresh');
            }

        }

		$data['main_content'] = 'login_form';
		$data['sidebar'] = FALSE;
		$this->load->view('user/includes/template', $data);

	}
	
}