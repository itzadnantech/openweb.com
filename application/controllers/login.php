<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Login extends CI_Controller
{

    function index()
    {

        $this->load->model('membership_model');

        // FORCE SSL
        if ($_SERVER['HTTPS'] != "on" && ($_SERVER['HTTP_HOST'] != STAGE_HOST)) {
            $redirect = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            header("Location:$redirect");
        }

        if ($this->session->userdata('is_logged_in')) {
            if ($this->session->userdata('role') == 'admin' || $this->session->userdata('role') == 'super_admin') {
                redirect('/admin/dashboard', 'refresh');
            } else {
                redirect('/user/dashboard', 'refresh');
            }
        }

        $cookie = isset($_COOKIE['openweb-login']) ? $_COOKIE['openweb-login'] : '';

        if ($cookie) {

            list($user, $token, $mac) = explode(':', $cookie);
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

                if ($role == 'admin') {
                    redirect('/admin/dashboard', 'refresh');
                } else {
                    redirect('/user/dashboard', 'refresh');
                }
            }
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        $ui_prefix = '';
        $this->load->model('flat_ui_model');
        $ui_prefix = $this->flat_ui_model->check_ui_prefix();
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // page marker
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        $data['error_message'] = $this->session->flashdata('error_message');
        $data['success_message'] = $this->session->flashdata('success_message');
        $data['main_content'] = 'login_form';

        $this->load->view($ui_prefix . 'user/includes/template', $data);
        $this->site_data = array();
    }

    function validate_login()
    {

        $this->load->model('membership_model');
        $role = $this->membership_model->validate();
        $globalOauth = $this->membership_model->get_global_oauth();
        // $role = [admin|client] or 0

        if ($role) { // if the user's credentials validated
            $status = $this->membership_model->get_user_status();
            if ($status == 'active') {
                //$username = $this->input->post('username');
                $username = $this->membership_model->get_input_username();

                $use_log = array(
                    'username' => $username,
                );
                $this->db->insert('use_log', $use_log);

                $log = $this->membership_model->get_login_info($username);
                $last_login = date('Y-m-d', strtotime($log['date']));
                $ow = $this->membership_model->get_ow($username);
                $email_z = $this->membership_model->get_email_z($username);
                $has2auth = $this->membership_model->get_2auth($username);

                $data = array(
                    'username' => $username,
                    'is_logged_in' => !$has2auth,
                    'role' => $role,
                    'last_login_time' => $last_login,
                    'ow' => $ow,
                    '2auth' => $has2auth,
                    '2auth_code' => rand(10000, 99999),
                    'email_z' => $email_z
                );
                $this->session->set_userdata($data);

                //Keep me loged in

                $checkbox = $this->input->post('checkbox');
                if (isset($checkbox) && $checkbox == "1") {

                    $token = md5(openssl_random_pseudo_bytes(20));
                    $this->membership_model->storeTokenForUser($username, $token);
                    $cookie = $username . ':' . $token;
                    $mac = hash_hmac('sha256', $cookie, SECRET_KEY);
                    $cookie .= ':' . $mac;
                    setcookie('openweb-login', $cookie, time() + 60 * 60 * 24 * 7, "/");
                }

                if ($has2auth) {
                    $authType = $this->membership_model->get_2auth_type($username);
                    $mobile = $this->membership_model->get_user_mobile($username);

                    if ($globalOauth != 'standart') {
                        $authType = $globalOauth;
                    }

                    if (strlen($mobile) > 0 && $authType == 'sms') {
                        $this->load->model('sms_model');

                        $template = $this->sms_model->get_template('verification_code');

                        if (!empty($template)) {
                            $content = $template['body'];

                            $content = str_ireplace('[OTP]', $data['2auth_code'], $content);

                            $this->sms_model->newSendSms($mobile, $content);
                            $this->session->set_flashdata('success_message', 'For your security, OpenWeb.co.za uses 2 Factor Authentication to protect your account.  We sent you an SMS with a code.  Please enter the code in the box below.');
                        }
                    } else {
                        $this->session->set_userdata(array(
                            'ask_for_mobile' => $authType == 'sms'
                        ));

                        $this->load->library('email');

                        $email = $this->membership_model->get_email($username);

                        $this->db->select('id,title,content,email_address');
                        $this->db->where('purpose', 'verification_code');
                        $query = $this->db->get('email_template');
                        $result = $query->result_array();

                        if (!empty($result)) {
                            $result = $result[0];
                            $email_template_id = $result['id'];
                            $email_address = $result['email_address'];
                            $title = $result['title'];
                            $content = $result['content'];

                            $content = str_ireplace('[OTP]', $data['2auth_code'], $content);

                            $this->db->where('email_template_id', $email_template_id);
                            $attac_query = $this->db->get('email_attachment');
                            $attac_result = $attac_query->result_array();

                            $this->email->from($email_address, 'OpenWeb');
                            $this->email->to($email);
                            $this->email->subject($title);
                            $this->email->message($content);

                            if (!empty($attac_result)) {
                                foreach ($attac_result as $att) {
                                    $path = $att['path'];
                                    $this->email->attach($path);
                                }
                            }
                            $this->email->send();
                        }
                        $this->session->set_flashdata('success_message', 'For your security, OpenWeb.co.za uses 2 Factor Authentication to protect your account.We sent you an Email with a code.Please enter the code in the box below.'); //$data['2auth_code']
                    }
                    redirect('/login/two_factor_auth');
                } else {
                    if ($role == 'client' || $role == 'cloudsl' || $role == 'reseller') {
                        redirect('user/dashboard');
                    } else if ($role == 'admin' || $role == 'super_admin') {
                        redirect('admin/dashboard');
                    } else if ($role == 'super_administrator') {
                        redirect('super_administrator/dashboard');
                    }
                }
            } else {
                $message_data = array(
                    'message_type' => 'warning',
                    'category' => 'login',
                );
                $msg = $this->message_model->get_message($message_data);
                //$msg = 'Whoops, your account still in pending.';
                $this->session->set_flashdata('error_message', $msg);
                redirect("login");
            }
        } else {
            $username = $this->input->post('username');
            $username = strip_tags(mysql_real_escape_string($username));
            $username = trim($username);

            $password = $this->input->post('password');
            $password = strip_tags(mysql_real_escape_string($password));
            $password = trim($password);


            $result = $this->membership_model->get_password($username);
            if ($result) {
                $msg = "Whoops, it seems that is an incorrect password.";
            } else {
                $msg = "Whoops, the username seems doesn't exist.";
            }
            $this->session->set_flashdata('error_message', $msg);
            redirect("login");
        }
    }

    function signup()
    {
        $data['main_content'] = 'signup_form';
        $data['sidebar'] = FALSE;
        $this->load->view('user/includes/template', $data);
    }

    function create_member()
    {
        $this->load->library('form_validation');
        // field name, error message, validation rules
        $this->form_validation->set_rules(
            'first_name',
            'Name',
            'trim|required'
        );
        $this->form_validation->set_rules(
            'last_name',
            'Last Name',
            'trim|required'
        );
        $this->form_validation->set_rules(
            'email_address',
            'Email Address',
            'trim|required|valid_email'
        );
        $this->form_validation->set_rules(
            'username',
            'Username',
            'trim|required|min_length[4]'
        );
        $this->form_validation->set_rules(
            'password',
            'Password',
            'trim|required|min_length[4]|max_length[32]'
        );
        $this->form_validation->set_rules(
            'password2',
            'Confirm Password',
            'trim|required|matches[password]'
        );

        if ($this->form_validation->run() == FALSE) {
            $data['main_content'] = 'signup_form';
            $this->load->view('login/includes/template', $data);
        } else {
            $this->load->model('membership_model');
            if ($query = $this->membership_model->create_member()) {
                //  User inserted
                $data['main_content'] = 'signup_successful';
                $this->load->view('login/includes/template', $data);
            } else {
                $this->load->view('signup_form');
            }
        }
    }

    function forgot_password()
    {
        $data['main_content'] = 'forgot_password';
        $data['sidebar'] = FALSE;
        $this->load->view('user/includes/template', $data);
        $this->site_data = array();
    }

    function get_password()
    {
        $this->load->model('user/user_model');
        if ($_POST) {
            $email = strip_tags(mysql_real_escape_string($_POST['email']));
            $email = trim($email);
            //send email to user
            $result = $this->user_model->email_forgot_password($email);
            if (!$result) {
                $msg = "The email does not exist.Please input it again.";
                $data['error_message'] = $msg;
            } else {
                $msg = "The email has been sent successfully, please check your email.";
                $data['success_message'] = $msg;
            }
            $data['main_content'] = 'forgot_password';
            $data['sidebar'] = FALSE;
            $this->load->view('user/includes/template', $data);
            $this->site_data = array();
        }
    }

    function two_factor_auth()
    {
        $is_two_auth = $this->session->userdata('2auth');

        if ($is_two_auth) {
            $this->load->model('flat_ui_model');
            $ui_prefix = $this->flat_ui_model->check_ui_prefix();

            $data['main_content'] = 'two_factor_auth_form';
            $data['error_message'] = $this->session->flashdata('error_message');
            $data['success_message'] = $this->session->flashdata('success_message');

            $this->load->view($ui_prefix . 'user/includes/template', $data);
            $this->site_data = array();
        } else {
            redirect("login");
        }
    }

    function validate_two_factor_auth()
    {
        // echo '<pre>';
        // print_r($_SESSION);
        // echo '</pre>';
        // die;
        $true_code = $this->session->userdata('2auth_code');
        $ask_for_mobile = $this->session->userdata('ask_for_mobile');
        $user_code = $this->input->post('code');

        $role = $this->session->userdata('role');

        ///new line code here
        // $true_code = 12345;
        // $user_code = 12345;

        if ($true_code == $user_code) {
            $this->session->set_userdata(array(
                '2auth' => false
            ));

            if ($ask_for_mobile) {
                redirect('login/ask_for_mobile');
            } else {
                $this->session->set_userdata(array(
                    'is_logged_in' => true
                ));
            }
            if ($role == 'client' || $role == 'cloudsl') {
                redirect('user/dashboard');
            } else if ($role == 'admin' || $role == 'super_admin') {
                redirect('admin/dashboard');
            } else if ($role == 'super_administrator') {
                redirect('super_administrator/dashboard');
            } else {
                redirect('/');
            }
        } else {
            $msg = "Whoops, it seems that is an incorrect code.";

            $this->session->set_flashdata('error_message', $msg);
            redirect("login/two_factor_auth");
        }
    }

    function ask_for_mobile()
    {
        $this->load->model('flat_ui_model');
        $ui_prefix = $this->flat_ui_model->check_ui_prefix();

        $data['main_content'] = 'ask_for_mobile_form';
        $data['error_message'] = $this->session->flashdata('error_message');
        $data['success_message'] = $this->session->flashdata('success_message');

        $this->load->view($ui_prefix . 'user/includes/template', $data);
        $this->site_data = array();
    }

    function add_mobile()
    {
        $this->load->model('membership_model');

        $user_mobile = $this->input->post('mobile');

        $username = $this->session->userdata('username');

        if (!isset($username) || !$username || $username == 0) {
            redirect('login');
        }

        $this->membership_model->add_mobile($username, $user_mobile);

        $this->session->set_userdata(array(
            'is_logged_in' => true
        ));

        $role = $this->session->userdata('role');

        if ($role == 'client' || $role == 'cloudsl') {
            redirect('user/dashboard');
        } else if ($role == 'admin') {
            redirect('admin/dashboard');
        } else if ($role == 'super_administrator') {
            redirect('super_administrator/dashboard');
        }
    }
}
