 <?php

class Order_model extends CI_Model
{

    function get_pro_ratas()
    {
        // First we do the pro-rata thing.
        $this->db->select('pro_rata_extra, user, id, date, product, account_username');
        $first_of_month = date('Y-m-01');
        $formated_date = date('Y-m-d H:i:s', strtotime($first_of_month));
        //$formated_date = date('Y-m-d',strtotime('now'));
        $this->db->where('date >', $formated_date);
        $query = $this->db->get('orders');
        $result = $query->result_array();
        // These were the orders that were placed this month, and their pro-rata extra.
        return $result;
        // This has all of the orders

    }

    function get_user_orders($user)
    {
        $this->load->model('product_model');
        //$this->db->select('id');
        $this->db->order_by('date', 'desc');
        $this->db->where('user', $user);
        $this->db->from('orders');
        $query = $this->db->get();
        $result = $query->result_array();
        $orders = array();

        $result = $this->get_updated_cancellations($result);

        if (!empty($result)) {
            foreach ($result as $res) {
                $order_data = $this->get_order_data($res['id']);
                $orders[$res['id']] = $order_data;
                $product_id = $order_data['product'];
                $product_name = $this->product_model->get_product_name($product_id);
                $orders[$res['id']]['product_name'] = $product_name;
            }
        }
        // Get updated cancellations				
        return $orders;
    }

    //update the order's status pending cancellation to  cancelled in the 1th next month
    function get_updated_cancellations($result)
    {
        if (!empty($result)) {
            foreach ($result as $i => $or) {
                if ($or['status'] == 'pending cancellation') {
                    // now we check the date
                    if (isset($or['date_cancelled']) && trim($or['date_cancelled'] != '')) {
                        $cancelled = $or['date_cancelled'];
                        // check if it was cancelled before this month.
                        $this_month = date('M', strtotime('now'));
                        $cancelled_month = date('M', strtotime($cancelled));
                        //echo "$this_month vs $cancelled_month";
                        if ($this_month != $cancelled_month) {
                            $id = $or['id'];
                            $this->db->where('id', $id);
                            $this->db->update('orders', array('status' => 'cancelled'));
                            $result[$i]['status'] = 'cancelled';
                        }
                    }
                }
            }
        }
        return $result;
    }

    //revoke the cancelled order
    //update the order's status cancelled to active in the 1th next month
    function get_cancellations_revoke($result)
    {
        if (!empty($result)) {
            foreach ($result as $i => $or) {
                if ($or['status'] == 'cancelled') {
                    // now we check the date
                    if (isset($or['date_revoke']) && trim($or['date_revoke'] != '')) {
                        $revoke = $or['date_revoke'];
                        // check if it was cancelled before this month.
                        $this_month = date('M', strtotime('now'));
                        $revoke_month = date('M', strtotime($revoke));
                        //echo "$this_month vs $cancelled_month";
                        if ($this_month != $revoke_month) {
                            $id = $or['id'];
                            $this->db->where('id', $id);
                            $this->db->update('orders', array('status' => 'active'));
                            $result[$i]['status'] = 'active';
                        }
                    }
                }
            }
        }
        return $result;
    }

    function email_invoices()
    {
        $this->load->model('admin/user_model');
        $users = $this->user_model->get_user_list();
        $this->load->library('email');

        if (!empty($users)) {
            foreach ($users as $user) {
                $username = $user['username'];
                $name = $user['first_name'];
                $email = $this->user_model->get_email($username);
                $date = date('F o', strtotime('now'));
                $link_date = strtolower(date('F-o', strtotime('now')));
                $link = "http://home.openweb.co.za/user/invoices/$username/$link_date";
                $msg = "Dear $name,
Please visit the following link for your invoice dated $date.
				
$link
				
If you have any billing queries, please do not hesitate to contact admin@openweb.co.za
Kind regards
Keoma Wright
Founder
OpenWeb.co.za";
                $this->email->from('admin@openweb.com', 'OpenWeb Home');
                $this->email->to($email);
                $this->email->subject("Invoice for $date");
                $this->email->message($msg);
                $this->email->send();
            }
        }
    }

    function  email_invoice_inv($username)
    {
        $this->load->library('email');

        if (isset($username)) {
            $this->db->select('first_name,email_address');
            $this->db->where('username', $username);
            $query = $this->db->get('membership');
            $result = $query->first_row('array');
            $email = $result['email_address'];
            $name = $result['first_name'];
            $date = date('F o', strtotime('now'));
            $link_date = strtolower(date('F-o', strtotime('now')));
            $link = "http://home.openweb.co.za/user/invoices/$username/$link_date";
            $msg = "Dear $name,
Please visit the following link for your invoice dated $date.
		
$link
		
If you have any billing queries, please do not hesitate to contact admin@openweb.co.za
Kind regards
Keoma Wright
Founder
OpenWeb.co.za";

            $this->email->from('admin@openweb.com', 'OpenWeb Home');
            $this->email->to($email);
            $this->email->subject("Tax Invoice for $date");
            $this->email->message($msg);
            $this->email->send();
        }
    }

    function email_invoices_individual($username, $pdf_id)
    {
        $this->load->library('email');
        $this->db->where('id', $pdf_id);
        $this->db->from('invoice_pdf');
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $pdf_name = $row->name;
            $path = $row->path;
            $user = $row->user_name;
        }

        if (isset($username)) {
            //$this->db->select('first_name,email_address');
            $this->db->where('username', $username);
            $this->db->from('membership');
            $user_query = $this->db->get();
            foreach ($user_query->result() as $row) {
                $username = $row->username;
                $first_name = $row->first_name;
                $last_name = $row->last_name;
                $password = $row->password;
                $email = $row->email_address;
                $joined = $row->joined;
                $status = $row->status;
            }

            $date = date('Y-m-d', time());

            $this->db->select('email_address, title, content');
            $this->db->where('purpose', 'send_invoice');
            $email_template = $this->db->get('email_template');
            $email_result = $email_template->result_array();

            if (!empty($email_result)) {
                $email_result = $email_result[0];
                $from = $email_result['email_address'];
                $subject = $email_result['title'];
                $content = $email_result['content'];

                $content = str_ireplace('[User_Name]', $username, $content);
                $content = str_ireplace('[First_Name]', $first_name, $content);
                $content = str_ireplace('[Last_Name]', $last_name, $content);
                $content = str_ireplace('[Password]', $password, $content);
                $content = str_ireplace('[Email_Address]', $email, $content);
                $content = str_ireplace('[Register_Date]', $joined, $content);
                $content = str_ireplace('[Current_Status]', $status, $content);

                $this->load->library('email');
                $this->email->from($from, 'OpenWeb Home');
                $this->email->to($email);
                $this->email->subject($subject);
                $this->email->message($content);
                $this->email->attach($path);
                $this->email->send();
                $this->email->clear(TRUE);
            }
        }
    }

    function get_invoice_for_month($username, $month = '')
    {
        $this->load->model('admin/product_model');

        //get the order this month
        $pro_ratas = $this->get_pro_ratas();
        $this_months = array(); // list of IDs of orders this month
        if (!empty($pro_ratas)) {
            foreach ($pro_ratas as $pr) {
                $acc_username = $pr['account_username'];
                $product_name = $this->product_model->get_product_name($pr['product']);
                $realm = $this->product_model->get_product_realm($pr['product']);
                $acc_username = $acc_username . '@' . $realm;
                $this_months[$pr['id']] = array(
                    'price' => $pr['pro_rata_extra'],
                    'date' => $pr['date'],
                    'product_name' => $product_name,
                    'account_username' => $acc_username,
                );
            }
        }

        //get the order before this month
        $this->db->select('id, price, date, product, account_username');
        $this->db->where('status', 'active');
        $this->db->where('user', $username);
        if ($month != '') {
            $first_of_month = date('Y-m-01', strtotime($month));
            $formated_date = date('Y-m-d H:i:s', strtotime($first_of_month));
            //$formated_date = date('Y-m-d',strtotime('now'));			
            $this->db->where('date <=', $formated_date);
        }
        $query = $this->db->get('orders');
        $result = $query->result_array();
        $monthly_prices = array();
        $user_costs = array();
        if (!empty($result)) {
            foreach ($result as $r) {
                $id = $r['id'];
                $acc_username = $r['account_username'];
                //if (!array_key_exists($id, $this_months)) {
                $product_name = $this->product_model->get_product_name($r['product']);
                $realm = $this->product_model->get_product_realm($r['product']);
                $acc_username = $acc_username . '@' . $realm;

                // Then we need to add it to the cost for the user
                $monthly_prices[$id] = array(
                    'order_id' => $r['id'],
                    'price' => $r['price'],
                    'date' => $r['date'],
                    'product_name' => $product_name,
                    'account_username' => $acc_username,
                );
                //}
                // Now that we have this cost, we
            }
        }
        //echo"<pre>"; print_r($monthly_prices);die();
        return $monthly_prices;
    }

    function get_invoice_this_month($username, $month)
    {
        $this->db->select('id, price, date, product, account_username');
        $this->db->where('status', 'active');
        $this->db->where('user', $username);
        $query = $this->db->get('orders');
        $result = $query->result_array();

        $monthly_prices = array();
        $user_costs = array();
        if (!empty($result)) {
            foreach ($result as $r) {
                $id = $r['id'];
                $order_date = $r['date'];
                $formate_order_date = date('Y-m', strtotime($order_date));

                if ($formate_order_date == $month) {
                    $acc_username = $r['account_username'];

                    $product_name = $this->product_model->get_product_name($r['product']);
                    $realm = $this->product_model->get_product_realm($r['product']);
                    $acc_username = $acc_username . '@' . $realm;

                    // Then we need to add it to the cost for the user
                    $monthly_prices[$id] = array(
                        'order_id' => $r['id'],
                        'price' => $r['price'],
                        'date' => $r['date'],
                        'product_name' => $product_name,
                        'account_username' => $acc_username,
                    );
                }
            }
        }
        //echo "<pre>";print_r($monthly_prices);die;
        return $monthly_prices;
    }

    function get_all_monthly_bills()
    {
        $pro_ratas = $this->get_pro_ratas();
        $this_months = array(); // list of IDs of orders this month
        if (!empty($pro_ratas)) {
            foreach ($pro_ratas as $pr) {
                $this_months[$pr['id']] = $pr['pro_rata_extra'];
            }
        }
        $this->db->select('id, price, user');
        $this->db->where('status', 'active');
        $query = $this->db->get('orders');
        $result = $query->result_array();
        $monthly_prices = array();
        $user_costs = array();
        if (!empty($result)) {
            foreach ($result as $r) {
                $id = $r['id'];
                $user = $r['user'];
                if (!array_key_exists($id, $this_months)) {
                    // Then we need to add it to the cost for the user
                    if (isset($user_costs[$user])) {
                        $user_costs[$user] = $user_costs[$user] + $r['price'];
                    } else {
                        $user_costs[$user] = $r['price'];
                    }
                } else {
                    if (isset($user_costs[$user])) {
                        $user_costs[$user] =
                            $user_costs[$user] + $this_months[$id];
                    } else {
                        $user_costs[$user] = $this_months[$id];
                    }
                }
                // Now that we have this cost, we
            }
        }
        return $user_costs;
    }

    function update_changed_bills()
    {
        $monthly_bills = $this->get_all_monthly_bills();
        $has_changed = array();
        if (!empty($monthly_bills)) {
            foreach ($monthly_bills as $user => $cost) {
                $this->db->select('cost, last_updated');
                $this->db->where('user', $user);
                $query = $this->db->get('monthly_bills');
                if ($query->num_rows) {
                    $result = $query->first_row('array');
                    $last_month = $result['cost'];
                    $last_updated = $result['last_updated'];
                    if ($cost != $last_month) {
                        // Then we need to show last month. If exist user then update
                        $data = array(
                            'cost' => $cost,
                            'last_updated' => date("Y-m-d H:i:s"),
                        );
                        $this->db->where('user', $user);
                        $this->db->update('monthly_bills', $data);
                    }
                } else {
                    // Has changed from nothing. If doesn't exist user then insert 
                    $data = array(
                        'user' => $user,
                        'cost' => $cost,
                        'last_updated' => date("Y-m-d H:i:s"),
                    );
                    $this->db->insert('monthly_bills', $data);
                }
            }
        }
    }

    function get_changed_bills()
    {
        $monthly_bills = $this->get_all_monthly_bills();
        //print_r($monthly_bills);die();
        $has_changed = array();
        if (!empty($monthly_bills)) {
            foreach ($monthly_bills as $user => $cost) {
                $this->db->select('cost, last_updated');
                $this->db->where('user', $user);
                $query = $this->db->get('monthly_bills');

                if ($query->num_rows) {
                    $result = $query->first_row('array');
                    $last_month = $result['cost'];
                    $last_updated = $result['last_updated'];
                    if ($cost != $last_month) {
                        // Then we need to show last month.
                        $has_changed[$user] = array(
                            'last_cost' => $last_month,
                            'new_cost' => $cost,
                            'last_updated' => $last_updated,
                        );
                    }
                } else {
                    // Has changed from nothing.
                    $data = array(
                        'user' => $user,
                        'cost' => $cost,
                    );
                    //$this->db->insert('monthly_bills', $data);
                    $has_changed[$user] = array(
                        'last_cost' => '0.00',
                        'new_cost' => $cost,
                        'last_updated' => 'Not set for this user',
                    );
                }
            }
        }
        return $has_changed;
    }

    /* SMS sending */
    function send_sms($number, $content)
    {
        $data = array(
            "Type" => "sendparam",
            "Username" => "keoma",
            "Password" => "maniac20",
            "live" => "true",
            "numto" => $number,
            "data1" => $content,
        );
        $data = http_build_query($data);
        return $this->do_post_request('http://www.mymobileapi.com/api5/http5.aspx', $data);
    }

    function do_post_request($url, $data, $optional_headers = null)
    {
        $params = array('http' => array(
            'method' => 'POST',
            'content' => $data
        ));
        if ($optional_headers !== null) {
            $params['http']['header'] = $optional_headers;
        }
        $ctx = stream_context_create($params);
        $fp = @fopen($url, 'rb', false, $ctx);
        if (!$fp) {
            throw new Exception("Problem with $url, $php_errormsg");
        }
        $response = @stream_get_contents($fp);
        if ($response === false) {
            throw new Exception("Problem reading data from $url, $php_errormsg");
        }
        $response;
        return $this->formatXmlString($response);
    }

    function formatXmlString($xml)
    {
        $xml = preg_replace('/(>)(<)(\/*)/', "$1\n$2$3", $xml); //"262168553True"==>" 262168553 True"
        $token      = strtok($xml, "\n"); //" 262168553 True"==>" 262168553 True "
        $result     = ''; // holds formatted version as it is built
        $pad        = 0; // initial indent
        $matches    = array(); // returns from preg_matches()
        while ($token !== false) :
            if (preg_match('/.+<\/\w[^>]*>$/', $token, $matches)) :
                $indent = 0;
            elseif (preg_match('/^<\/\w/', $token, $matches)) :
                $pad--;
            elseif (preg_match('/^<\w[^>]*[^\/]>.*$/', $token, $matches)) :
                $indent = 1;
            else :
                $indent = 0;
            endif;
            $line    = str_pad($token, strlen($token) + $pad, ' ', STR_PAD_LEFT);
            $result .= $line . "\n"; // add to the cumulative result, with linefeed
            $token   = strtok("\n"); // get the next token
            $pad    += $indent; // update the pad size for subsequent lines
        endwhile;
        return $result;
    }


    function get_is_class($order_id)
    {
        // First get product ID
        $this->db->select('product, billing_cycle');
        $this->db->where('id', $order_id);
        $this->db->limit(1);
        $query = $this->db->get('orders');
        $result = $query->result_array();
        $product_id = $result[0]['product'];
        $billing_cycle = $result[0]['billing_cycle'];

        if ($billing_cycle == 'Once-Off') {
            $this->db->select('id');
            $this->db->where('desc', 'ISDSL No Service');
            $this->db->limit(1);
            $query = $this->db->get('is_classes');
            $result = $query->result_array();
            $product_class = $result[0]['id'];
        } else {
            // Then get product class
            $this->db->select('class');
            $this->db->where('id', $product_id);
            $this->db->limit(1);
            $query = $this->db->get('products');
            $result = $query->result_array();
            $product_class = $result[0]['class'];
        }
        return $product_class;
    }

    function set_activated($order_id)
    {
        $data = array(
            'status' => 'active',
        );

        $this->db->where('id', $order_id);
        $this->db->update('orders', $data);
    }
    function set_activated_cloudsl($order)
    {
        $data = array(
            'status' => 'active',
            'date' => date('Y-m-d H:i:s', strtotime('now')),
        );

        $this->db->where('id', $order['id']);
        $this->db->update('orders', $data);
    }
    function get_is_details($product_class)
    {
        // From here we will get the class's realm details!
        $this->db->select('realm');
        $this->db->where('id', $product_class);
        $this->db->limit(1);
        $query = $this->db->get('is_classes');
        $result = $query->result_array();
        $realm = $result[0]['realm'];

        if ($realm) {
            // Now we get realm user and password
            $this->db->select('user, pass');
            $this->db->where('realm', $realm);
            $query = $this->db->get('realms');
            $result = $query->result_array();
            $user = $result[0]['user'];
            $password = $result[0]['pass'];

            $data = array(
                'user' => "$user@$realm",
                'pass' => $password,
                'realm' => $realm,
            );
            return $data;
        }
        return false;
    }

    function order_key()
    {
        $order_key = array(
            'user' => 'User',
            'product' => 'Product',
            'date' => 'Date ordered',
            'price' => 'Price Paid',
            'pro_rata_extra' => 'Pro Rata Extra',
            'account_username' => 'Account Username',
            'account_password' => 'New Account Password',
            'account_comment' => 'Account Comment',
            'status' => 'Order Status',
            'change_flag' => '',
            'display_usage' => '',
            'cancel_flage' => '',
            'payment_method' => 'Payment Method',
        );
        return $order_key;
    }


    function order_key_with_realm()
    {
        $order_key = array(
            'user' => 'User',
            'product' => 'Product',
            'realm' => 'Realm',
            'date' => 'Date ordered',
            'price' => 'Price Paid',
            'pro_rata_extra' => 'Pro Rata Extra',
            'account_username' => 'Account Username',
            'account_password' => 'New Account Password',
            'account_comment' => 'Account Comment',
            'status' => 'Order Status',
            'change_flag' => '',
            'display_usage' => '',
            'cancel_flage' => '',
        );
        return $order_key;
    }

    function assign_order_ltea_fiber_data($order_data)
    {

        $insert_result = $this->db->insert('fibre_orders', $order_data);
        if ($insert_result)
            $insert_result = $this->db->insert_id();

        return $insert_result;
    }
    function assign_order_ltea($order_data)
    {

        $insert_result = $this->db->insert('orders', $order_data);
        if ($insert_result)
            $insert_result = $this->db->insert_id();

        return $insert_result;
    }

    function assign_order($order_data)
    {
        // print_r($order_data);die();
        $insert_result = $this->db->insert('orders', $order_data);
        if ($insert_result)
            $insert_result = $this->db->insert_id();

        return $insert_result;
    }

    function email_activation($email, $product_name, $username, $realm, $acc_password, $user_name)
    {
        $this->load->model('crypto_model');

        $this->db->select('id, email_address, title, content');
        $this->db->where('purpose', 'active_account');
        $query = $this->db->get('email_template');
        $result = $query->result_array();

        if (!empty($result)) {
            $this->db->where('email_address', $email);
            $this->db->from('membership');
            $user_query = $this->db->get();
            foreach ($user_query->result() as $row) {
                echo '<pre>';


                $home_username = $row->username;
                $first_name = $row->first_name;
                $last_name = $row->last_name;
                $password = $this->crypto_model->decode($row->password);
                $email = $row->email_address;
                $joined = $row->joined;
                $status = $row->status;
            }



            $result = $result[0];

            $email_template_id = $result['id'];
            $from = $result['email_address'];
            $subject = $result['title'];
            $content = $result['content'];

            $content = str_replace('[User_Name]', $home_username, $content);
            $content = str_replace('[Account_username]', "$username", $content);
            $content = str_replace('[Account_password]', $acc_password, $content);
            $content = str_ireplace('[First_Name]', $first_name, $content);
            $content = str_ireplace('[Last_Name]', $last_name, $content);
            $content = str_ireplace('[Password]', $password, $content);
            $content = str_ireplace('[Email_Address]', $email, $content);
            $content = str_ireplace('[Register_Date]', $joined, $content);
            $content = str_ireplace('[Current_Status]', $status, $content);




            $this->db->where('email_template_id', $email_template_id);
            $attac_query = $this->db->get('email_attachment');
            $attac_result = $attac_query->result_array();

            $this->load->library('email');
            $this->email->from($from, 'OpenWeb');
            $this->email->to($email);
            $this->email->subject($subject);
            $this->email->message($content);
            if ($attac_result) {
                foreach ($attac_result as $att) {
                    $path = $att['path'];
                    $this->email->attach($path);
                }
            }
            $this->email->send();
            $this->email->clear(TRUE);
        }
    }


    function email_activate_fibre_order($email, $service, $data_array, $fiber_data = array())
    {

        if (empty($email))
            return false;

        if (empty($service))
            return false;

        // service : fibre-data ,  purpose : fibre_data_activation
        // service : fibre-line ,  purpose : fibre_line_activation

        $purpose_value = '';
        switch ($service) {
            case 'fibre-data':
                $purpose_value = 'fibre_data_activation';
                break;
            case 'fibre-line':
                $purpose_value = 'fibre_line_activation';
                break;
            case 'lte-a':
                $purpose_value = 'lte_a_activation';
                break;
            case 'mobile':
                $purpose_value = 'mobile_activation';
                break;
            default:
                return false;
                break;
        }



        $this->db->select('id, email_address, title, content');
        $this->db->where('purpose', $purpose_value);
        $query = $this->db->get('email_template');
        $result = $query->result_array();


        if (!empty($result)) {
            $this->db->where('id', $data_array['id_user']);
            $this->db->from('membership');
            $user_query = $this->db->get();

            foreach ($user_query->result() as $row) {


                $username = $row->username;
                $first_name = $row->first_name;
                $last_name = $row->last_name;
                $password = $row->password;
                $email = $row->email_address;
                $joined = $row->joined;
                $status = $row->status;
            }


            $result = $result[0];


            $email_template_id = $result['id'];
            $from = $result['email_address'];
            $subject = $result['title'];
            $content = $result['content'];

            $content = str_replace('[User_Name]', $username, $content);
            $content = str_ireplace('[First_Name]', $first_name, $content);
            $content = str_ireplace('[Last_Name]', $last_name, $content);
            $content = str_ireplace('[Password]', $password, $content);
            $content = str_ireplace('[Email_Address]', $email, $content);
            //   $content = str_ireplace('[Register_Date]', $joined, $content);
            $content = str_ireplace('[Current_Status]', $status, $content);


            // add product name
            $content = str_ireplace('[Fibre_Line]', $fiber_data['number_fl'], $content);
            $content = str_ireplace('[Fibre_Username]', $data_array['username_fd'], $content);
            $content = str_ireplace('[Fibre_Provider]', $data_array['provider_fd'], $content);
            $content = str_ireplace('[Fibre_Password]', $data_array['password_fd'], $content);
            $content = str_ireplace('[Fibre_Username]', $data_array['username_la'], $content);
            $content = str_ireplace('[Fibre_Password]', $data_array['password_la'], $content);


            $this->db->where('email_template_id', $email_template_id);
            $attac_query = $this->db->get('email_attachment');
            $attac_result = $attac_query->result_array();




            $this->load->library('email');
            $this->email->from($from, 'OpenWeb');
            $this->email->to($email); //$email
            $this->email->subject($subject);
            $this->email->message($content);
            if ($attac_result) {
                foreach ($attac_result as $att) {
                    $path = $att['path'];
                    $this->email->attach($path);
                }
            }



            $this->email->send();
            $this->email->clear(TRUE);
        }
    }

    function get_all_orders_count($services = array('adsl'))
    {

        if (empty($services))
            return 0;

        $this->db->select('id');
        $this->db->from('orders');
        $this->db->where_in('service_type', $services);
        $query = $this->db->get();
        return $query->num_rows();
    }



    function local_order_remove($order_id, $user_id = null)
    {

        $order_data = $this->get_order_data($order_id);
        $type = $order_data["service_type"];
        $delete = true;
        $result = false;

        if ($type == 'lte-a') {
            $this->db->where('order_id', $order_id);
            $delete = $this->db->delete('fibre_orders');
        }

        if ($delete) {
            $this->db->where('id', $order_id);
            if (!empty($user_id))
                $this->db->where('id_user', $user_id);
            $result = $this->db->delete('orders');
        }
        return $result;
    }


    function delete_order($order_id)
    {
        // First remove ISDSL account
        //$this->load->model('admin/is_classes');


        $class = $this->get_is_class($order_id); //get class name form the products table 


        // $realm_data = $this->get_is_details($class);
        $realm_data = $this->get_realm_data_by_order_id($order_id, $class);

        $account_data = $this->order_model->get_order_data($order_id); //order data
        //$order_user = $account_data['user'];
        $order_acc_user = $account_data['account_username']; //username
        $order_acc_pwd  = $account_data['account_password'];

        $this->db->select('id');
        $this->db->where('id !=', $order_id);
        $this->db->where('account_username', $order_acc_user);
        $this->db->where('account_password', $order_acc_pwd);
        $query = $this->db->get('orders');

        if ($query->result_array()) {
            $result = $query->result_array();
            $change_service = $result[0]['id'];
            $this->db->delete('orders', array('id' => $change_service));
        }

        $rl_user = $realm_data['user'];
        $rl_pass = $realm_data['pass'];
        $realm = $realm_data['realm'];
        $sess = 0;
        $sess = $this->is_classes->is_connect_new($rl_user, $rl_pass);
        //echo "Delete: $sess, $order_acc_user";
        if ($account_data['billing_cycle'] != 'Daily') {
            $resp = $this->is_classes->delete_account_new($sess, "$order_acc_user@$realm");
        }
        //var_dump('resp----'.$resp);die();//echo null
        //if($resp == 1){
        // Now remove order
        $this->db->where('id', $order_id);
        //if($account_data['billing_cycle']!='Daily'){
        $this->db->update(
            'orders',
            array(
                'status' => 'pending cancellation',
                'date_cancelled' => date('Y-m-d H:i:s', strtotime('now')),
                'date_revoke' => '',
                'modify_service' => '',
            )
        );
        //	}
        /* 	else{
				$this->db->update('orders', array(
						'status' => 'pending',
						'date_cancelled' => date('Y-m-d H:i:s',strtotime('now')),
						'date_revoke' => '',
						'modify_service' => '',
						'product' => 0,
						'price' => 0,
						'account_comment' =>'',
						//'date' => date("Y-m-d H:i:s",strtotime('now')),
						)
					);
			} */
        //}else{
        //	die('Failed to delete account from the api.');
        //}

    }

    function revoke_order($order_id)
    {
        $class = $this->get_is_class($order_id);
        //$realm_data = $this->get_is_details($class);
        $realm_data = $this->get_realm_data_by_order_id($order_id, $class);
        $account_data = $this->order_model->get_order_data($order_id);

        $order_acc_user = $account_data['account_username'];
        $rl_user = $realm_data['user'];
        $rl_pass = $realm_data['pass'];
        $realm = $realm_data['realm'];
        $acc_username = $order_acc_user . '@' . $realm;
        $sess = 0;
        $sess = $this->is_classes->is_connect_new($rl_user, $rl_pass);
        if ($account_data['billing_cycle'] != 'Daily')
            $resp = $this->is_classes->restore_account_new($sess, $acc_username);
        //var_dump('rep---->'.$resp);  die; //echo null
        //if($resp == 1){
        $revoke_date = date('Y-m-d H:i:s', strtotime('now'));
        //update the order status in db
        $this->db->where('id', $order_id);
        $this->db->update(
            'orders',
            array(
                'status' => 'active',
                'date_revoke' => $revoke_date,
                'date_cancelled' => NULL,
            )
        );
        return $revoke_date;
        //}else{
        //	die('Failed to revoke account from the api.');
        //}
    }

    function update_order($order_id, $order_data)
    {
        // if the password has changed, we need to change that on ISDSL.
        $isdsl_update = false;
        if (isset($order_data['account_password'])) {
            // the password has definitely changed
            $isdsl_update = true;
            $new_password = $order_data['account_password'];
            $new_username = $order_data['account_username'];
        }

        if ($isdsl_update && $_POST['status'] == 'active') {
            // perform the update
            if ((isset($new_password) && trim($new_password) != '') || (isset($new_username) && trim($new_username) != '')) {
                $this->load->model('admin/is_classes');

                $class = $this->get_is_class($order_id);
                //$realm_data = $this->get_is_details($class);
                $realm_data = $this->order_model->get_realm_data_by_order_id($order_id, $class);

                $rl_user = $realm_data['user'];
                $rl_pass = $realm_data['pass'];
                $lm = explode('@', $realm_data['user']);
                $realm = $lm[1];
                $sess = 0;
                $sess = $this->is_classes->is_connect_new($rl_user, $rl_pass);

                //$username = $order_data['account_username'];
                $data['password'] = $new_password;
                $data = array(
                    'strSessionID' => $sess,
                    'strUserName' => $new_username,
                    'strValue' => $new_password,
                );

                $resp = $this->is_classes->is_setAccountPassword_new($data);
            }
        }

        if (isset($order_data['id'])) {
            unset($order_data['id']);
        }
        $this->db->where('id', $order_id);
        $this->db->update('orders', $order_data);
    }

    function get_order_data($order_id)
    {
        $this->db->where('id', $order_id);
        $query = $this->db->get('orders');
        $result = $query->first_row('array');
        return $result;
    }

    function get_all_orders($num = 10, $start = 0, $services = array('adsl'))
    {

        if (empty($services))
            $services = array('adsl');

        $this->db->select();
        $this->db->where_in('service_type', $services);
        $this->db->limit($num, $start);
        $this->db->order_by('date', 'desc');
        $query = $this->db->get('orders');
        $result = $query->result_array();
        $result = $this->get_updated_cancellations($result);

        foreach ($result as $r) {
            if (($r['service_type'] == 'fibre-data') || ($r['service_type'] == 'fibre-line')) {
                $fibre = $this->get_fibre_data_by_order($r['id']);
                if (!empty($fibre))
                    $r['fibre'] = $fibre;
            }
            $data[] = $r;
        }

        return $data;
    }

    function get_with_avios_id($num = 10, $start = 0)
    {

        $str = "SELECT o.* FROM orders as o LEFT JOIN membership as m ON o.id_user = m.id WHERE ((m.avios_id IS NOT NULL AND m.avios_id<>'') OR";
        $str .= " (m.br_a_id IS NOT NULL AND m.br_a_id<>''))";
        $str .= " AND o.date_avios IS NULL AND o.status='active'";
        $str .= " LIMIT 10 OFFSET " . $start;

        $query = $this->db->query($str);

        if ($query) {
            $result = $query->result_array();
            return $result;
        }

        return $query;
    }

    function get_without_billing_code($num = 10, $start = 0)
    {

        $str = "SELECT o.* FROM orders as o LEFT JOIN membership as m ON o.id_user = m.id WHERE ((m.avios_id IS NOT NULL AND m.avios_id<>'') OR";
        $str .= " (m.br_a_id IS NOT NULL AND m.br_a_id<>''))";
        $str .= " AND o.status='active' AND o.avios_code IS NULL";
        $str .= " LIMIT 10 OFFSET " . $start;

        $query = $this->db->query($str);

        if ($query) {
            $result = $query->result_array();
            return $result;
        }

        return $query;
    }

    function get_orders_avios_awards()
    {

        $str = "SELECT o.* FROM orders as o LEFT JOIN membership as m ON o.id_user = m.id WHERE";
        $str .= " (m.br_a_id IS NOT NULL AND m.br_a_id<>'')";
        $str .= " AND o.status='active' AND (o.avios_code IS NOT NULL AND o.avios_code<>'')";

        $query = $this->db->query($str);

        if ($query) {
            $result = $query->result_array();
            return $result;
        }

        return $query;
    }

    function get_count_with_avios()
    {
        $str = "SELECT o.* FROM orders as o LEFT JOIN membership as m ON o.id_user = m.id WHERE ((m.avios_id IS NOT NULL AND m.avios_id<>'') OR";
        $str .= " (m.br_a_id IS NOT NULL AND m.br_a_id<>''))";
        $str .= " AND o.date_avios IS NULL AND o.status='active'";
        $query = $this->db->query($str);

        if ($query) {
            $res = $query->num_rows();
            return $res;
        }

        return 0;
    }

    function get_count_withot_billing_code()
    {
        $str = "SELECT o.* FROM orders as o LEFT JOIN membership as m ON o.id_user = m.id WHERE (m.avios_id IS NOT NULL OR m.br_a_id IS NOT NULL)";
        $str .= " AND o.avios_code IS NULL AND o.status='active'";
        $query = $this->db->query($str);

        if ($query) {
            $res = $query->num_rows();
            return $res;
        }

        return 0;
    }

    function addAviosAwardDate($order_id)
    {

        $query = $this->db->query('UPDATE orders SET date_avios=NOW() WHERE id=' . $order_id);
    }

    function checkAviosDate($order_id)
    {

        $this->db->select('date_avios');
        $this->db->where('id', $order_id);
        $query = $this->db->get('orders');
        $res = $query->result();
        return $res;
    }

    function reseteAviosDate()
    {
        $this->db->query('UPDATE orders SET date_avios=NULL WHERE date_avios IS NOT NULL');
    }

    function addBillingCode($order_id, $billing_data)
    {

        $this->db->where('id', $order_id);
        $this->db->update('orders', $billing_data);
    }

    function get_pending_orders_count()
    {
        $this->db->select('id');
        $this->db->from('orders');
        $this->db->where('status', 'pending');
        $query = $this->db->get();
        return $query->num_rows();
    }

    function get_pending_orders($num = 10, $start = 0)
    {
        $this->db->limit($num, $start);
        $this->db->where('status', 'pending');
        $this->db->order_by('date', 'desc');
        $query = $this->db->get('orders');
        return $query->result_array();
    }

    function get_product_data($product_id)
    {
        $this->db->where('id', $product_id);
        $query = $this->db->get('products');
        $result = $query->first_row('array');
        return $result;
    }

    function get_realm($class)
    {
        $this->db->select('realm');
        $this->db->where('id', $class);
        $query = $this->db->get('is_classes');
        $result = $query->result_array();
        return $result[0]['realm'];
    }

    function send_system_ceo($account_id)
    {
        $this->db->where('id', $account_id);
        $query = $this->db->get('membership');
        $result = $query->first_row('array');

        $firstname = $result['first_name'];
        $lastname = $result['last_name'];
        $email = $result['email_address'];
        $ow = $result['ow'];

        $content = "
First Name and Last Name: $firstname  $lastname
Email Address: $email
OW Number: $ow";

        $this->load->library('email');
        $this->email->from('noreply@openweb.co.za', 'OpenWeb');
        $this->email->to('ceo@openweb.co.za');
        $this->email->subject('New User Signup');
        $this->email->message($content);
        $this->email->send();
    }

    function email_ceo_product($user, $product_name, $payment_method)
    {

        // echo " !in";
        //  die;
        $this->db->where('username', $user);
        $query = $this->db->get('membership');
        $result = $query->first_row('array');


        $firstname = $result['first_name'];
        $lastname = $result['last_name'];
        $email = $result['email_address'];
        $ow = $result['ow'];



        if ($payment_method == 'credit_card_auto') {
            $payment_method = 'Auto Billing using your Credit Card';
        } elseif ($payment_method == 'credit_card') {
            $payment_method = 'Once off payment from your Credit Card';
        } elseif ($payment_method == 'debit_order') {
            $payment_method = 'Debit Order';
        } elseif ($payment_method == 'eft') {
            $payment_method = 'EFT';
        }

        $content = "
First Name and Last Name: $firstname  $lastname
Email Address: $email
OW Number: $ow
Product Purchased: $product_name
Payment Method: $payment_method";

        $this->load->library('email');
        $this->email->from('noreply@openweb.co.za', 'OpenWeb');
        $this->email->to('ceo@openweb.co.za');
        $this->email->subject('New Product Created');
        $this->email->message($content);
        $this->email->send();
    }

    function get_user_active_orders($id_user)
    {
        $this->db->where('id_user', $id_user);
        $this->db->where('status', 'active');
        $query = $this->db->get('orders');
        $result = $query->result_array();
        if ($result) {
            return $result;
        } else {
            return null;
        }
    }

    function get_invoice_by_fields($fields_array)
    {

        if (empty($fields_array))
            return false;
        unset($fields_array['create_date']);
        $this->db->select();
        foreach ($fields_array as $field_key => $field_val)
            $this->db->where($field_key, $field_val);

        $this->db->order_by('create_date', 'desc');
        $query = $this->db->get('invoices');
        $result = $query->first_row('array');
        return $result;
    }

    function save_montly_inovice($username, $user_id)
    {
        $date = date('M/Y', strtotime('+1 month'));
        $title = "Tax Invoice for $username in $date";
        $invoice = array(
            'invoice_name' => $title,
            'create_date' => date('Y-m-d', time()),
            'user_name' => $username,
            'type' => 'auto',
            'user_id' => $user_id,
        );
        // check if this invoice already exist and, return ID if exist
        $result = $this->get_invoice_by_fields($invoice);

        // excludes duplicates
        if (empty($result)) {
            $this->db->insert('invoices', $invoice);
            $month_invoice_id = $this->db->insert_id();
        } else {
            $month_invoice_id = $result['id'];
        }

        return $month_invoice_id;
    }

    function remove_montly_inovice($invoice_id, $username, $user_id)
    {

        $this->db->where('user_name', $username);
        $this->db->where('user_id', $user_id);
        $this->db->where('id', $invoice_id);

        $result = $this->db->delete('invoices');
        return $result;
    }


    function create_invoice_pdf_error_handler($code)
    {

        // 0 - default answer
        // 1 - success
        $message = '';
        switch ($code) {

            case 2:
                $message = 'billing info is empty';
                break;
            case 3:
                $message = 'save process was failed';
                break;
        }

        return $message;
    }




    function create_month_invoice_pdf_hash($username, $user_id, $month_invoice_id, $user_data)
    {

        $returnArray = array(
            'result'  => false,
            'code'    => '0',
            'message' => '',
            'pdf_id'  => '',
        );


        // check if PDF already exist
        $invoice_pdf_data = $this->get_invoice_pdf_by_invoice_id($month_invoice_id);

        if (!empty($invoice_pdf_data)) {

            // prepare success answer and return id
            $pdf_id = $invoice_pdf_data[0]['id'];
            $returnArray = array(

                'result'  => true,
                'code'    => '1',
                'message' => '',
                'pdf_id'  => $pdf_id,
            );

            return $returnArray;
        }

        $orders = $this->get_user_active_orders($user_id);

        $this->load->library('tfpdf/MC_Table');
        $pdf = new MC_Table();

        $first_name = $user_data['user_settings']['first_name'];
        $last_name = $user_data['user_settings']['last_name'];
        $user_billing = $user_data['user_billing'];

        if ($user_billing) {
            $billing_name = $user_billing['billing_name'];
            $user_address = $user_billing['address_1'] . ' ' . $user_billing['address_2'];
            $user_city = $user_billing['city'];
            $user_country = $user_billing['country'];
            $user_province = $user_billing['province'];
            $user_phone = 'Phone: ' . $user_billing['contact_number'];
            $user_p_c = $user_province . ', ' . $user_country;
        } else {
            $billing_name = $user_address = $user_city = $user_country = $user_province = $user_phone = $user_p_c = '';
            // break , return, or save log and email to CEO ?
            $resultArray['result'] = false;
            $resultArray['code'] = '2';
            $resultArray['message'] = $this->create_invoice_pdf_error_handler($resultArray['code']);
            return $resultArray;
        }

        $ISP_query = $this->db->get('openisp_cc');
        $ISP_result = $ISP_query->result_array();
        $open_ISP = $ISP_result[0];
        $open_name = $open_ISP['name'];
        $vat_number = $open_ISP['vat_number'];
        $country = $open_ISP['country'];
        $province = $open_ISP['province'];
        $address = $open_ISP['address'];
        $phone = $open_ISP['phone'];

        $month = date('M Y', strtotime(date('Y', time()) . '-' . (date('m', time()) + 1))); // TODO: BUGfix
        $month = date('M Y', strtotime('+1 month'));


        $title = "Tax Invoice for $username in $month";

        //create PDF file page
        $pdf->AddPage();

        $pdf->SetFont('Arial', '', 20);
        //        $image = base_url().'img/main.png';
        $image = '/home/home/public_html/img/main.png';
        $pdf->Image($image, 70, 5, 60);

        $pdf->SetFont('Arial', '', 20);
        $pdf->SetXY(40, 30);
        $pdf->Cell(20, 8, $title, 'C', true);
        $pdf->Ln();

        //set invoice info
        $invoice_date = date('d/m/Y', time());
        $invoice_id_format = "Tax Inv # : $month_invoice_id";
        $invoice_date_format = "Date : $invoice_date";

        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(20, 4, $invoice_id_format, '', true);
        $pdf->Cell(36, 10, $invoice_date_format, 0, 0, 'R', false, '');
        $pdf->Ln();

        //set open info
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(20, 4, $open_name, '', true);
        $pdf->Cell(185, 3, $billing_name, 0, 0, 'R', false, '');
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(20, 3, INVOICE_ORGANIZATION_ID . $vat_number, '', true);
        $pdf->Cell(185, 3, $first_name . ' ' . $last_name, 0, 0, 'R', false, '');
        $pdf->Ln();

        $pdf->Cell(20, 3, $address, '', true);
        $pdf->Cell(185, 3, $user_address . ' ' . $user_city, 0, 0, 'R', false, '');
        $pdf->Ln();

        $pdf->Cell(20, 3, $province . ', ' . $country, '', true);
        $pdf->Cell(185, 3, $user_p_c, 0, 0, 'R', false, '');
        $pdf->Ln();

        $pdf->Cell(20, 3, 'Phone: ' . $phone, '', true);
        $pdf->Cell(185, 3, $user_phone, 0, 0, 'R', false, '');
        $pdf->Ln();
        $pdf->Ln();

        //set the body
        $pdf->SetFillColor(128, 128, 128);
        $pdf->SetTextColor(255);
        $pdf->SetDrawColor(92, 92, 92);

        $pdf->Cell(110, 8, "Product", 1, 0, 'C', true);
        $pdf->Cell(45, 8, "Invoice Date", 1, 0, 'C', true);
        $pdf->Cell(30, 8, "Cost this month", 1, 0, 'C', true);
        $pdf->Ln();


        $total = 0;
        $pdf->SetWidths(array(110, 45, 30));

        if (!isset($this->product_model))
            $this->load->model('admin/product_model');

        //$pdf->SetAligns('C');
        foreach ($orders as $key => $value) {
            $order_id = $value['id'];
            $product_name = $this->product_model->get_product_name($value['product']);
            //if fibre or lte
            if ($value['product'] == 0) {
                $product_name = $this->product_model->get_product_name_fibre($order_id);
            }

            $date = $month;
            $price = $value['price'];

            $cost = 'R ' . $price;
            $total = $total + $price;

            $pdf->Row(array($product_name, $date, $cost));
        }


        $pdf->SetFillColor(255, 255, 255);
        $pdf->Ln(1);
        $pdf->Cell(185, 8, 'Total: R ' . $total, 0, 0, 'R', true);
        $pdf->Ln();
        $pdf->Cell(0, 8, INVOICE_VAT_ROW, 0, 0, '', true);


        //save the pdf file to local file
        $path_name = APPPATH . 'PDFfiles/' . $username;
        if (is_dir($path_name) == false) {
            // echo "<br/>path not exist";
            mkdir($path_name, 0777);
        }

        $file_name = $month_invoice_id . '.pdf';
        $file_save_path = $path_name . '/' . $file_name;

        $pdf->Output($file_save_path, 'F');
        //save the pdf
        $pdf_data = array(
            'name' => $file_name,
            'path' => $file_save_path,
            'create_date' => date('Y-m-d H:i:s', time()),
            'user_name' => $username,
            'invoices_id' => $month_invoice_id
        );
        $result = $this->db->insert('invoice_pdf', $pdf_data);
        $pdf_id = $this->db->insert_id();
        $returnArray = array(

            'result'  => true,
            'code'    => '1',
            'message' => '',
            'pdf_id'  => $pdf_id,
        );

        return $returnArray;
    }

    function get_invoice_pdf_by_invoice_id($invoice_id)
    {

        $this->db->select();
        $this->db->where('invoices_id', $invoice_id);
        $this->db->order_by('create_date', 'desc');

        $query = $this->db->get('invoice_pdf');
        $result = $query->result_array();

        return $result;
    }


    function create_month_invoice_pdf($username, $user_id, $month_invoice_id, $user_data)
    {

        $orders = $this->get_user_active_orders($user_id);

        $this->load->library('tfpdf/MC_Table');
        $pdf = new MC_Table();

        $first_name = $user_data['user_settings']['first_name'];
        $last_name = $user_data['user_settings']['last_name'];
        $user_billing = $user_data['user_billing'];

        if ($user_billing) {
            $billing_name = $user_billing['billing_name'];
            $user_address = $user_billing['address_1'] . ' ' . $user_billing['address_2'];
            $user_city = $user_billing['city'];
            $user_country = $user_billing['country'];
            $user_province = $user_billing['province'];
            $user_phone = 'Phone: ' . $user_billing['contact_number'];
            $user_p_c = $user_province . ', ' . $user_country;
        } else {
            $billing_name = $user_address = $user_city = $user_country = $user_province = $user_phone = $user_p_c = '';
        }

        $ISP_query = $this->db->get('openisp_cc');
        $ISP_result = $ISP_query->result_array();
        $open_ISP = $ISP_result[0];
        $open_name = $open_ISP['name'];
        $vat_number = $open_ISP['vat_number'];
        $country = $open_ISP['country'];
        $province = $open_ISP['province'];
        $address = $open_ISP['address'];
        $phone = $open_ISP['phone'];

        $month = date('M Y', strtotime(date('Y', time()) . '-' . (date('m', time()) + 1))); // TODO: BUGfix
        $month = date('M Y', strtotime('+1 month'));


        $title = "Tax Invoice for $username in $month";

        //create PDF file page
        $pdf->AddPage();

        $pdf->SetFont('Arial', '', 20);
        //		$image = base_url().'img/main.png';
        $image = '/home/home/public_html/img/main.png';
        $pdf->Image($image, 70, 5, 60);

        $pdf->SetFont('Arial', '', 20);
        $pdf->SetXY(40, 30);
        $pdf->Cell(20, 8, $title, 'C', true);
        $pdf->Ln();

        //set invoice info
        $invoice_date = date('d/m/Y', time());
        $invoice_id_format = "Tax Inv # : $month_invoice_id";
        $invoice_date_format = "Date : $invoice_date";

        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(20, 4, $invoice_id_format, '', true);
        $pdf->Cell(36, 10, $invoice_date_format, 0, 0, 'R', false, '');
        $pdf->Ln();

        //set open info
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(20, 4, $open_name, '', true);
        $pdf->Cell(185, 3, $billing_name, 0, 0, 'R', false, '');
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(20, 3, INVOICE_ORGANIZATION_ID . $vat_number, '', true);
        $pdf->Cell(185, 3, $first_name . ' ' . $last_name, 0, 0, 'R', false, '');
        $pdf->Ln();

        $pdf->Cell(20, 3, $address, '', true);
        $pdf->Cell(185, 3, $user_address . ' ' . $user_city, 0, 0, 'R', false, '');
        $pdf->Ln();

        $pdf->Cell(20, 3, $province . ', ' . $country, '', true);
        $pdf->Cell(185, 3, $user_p_c, 0, 0, 'R', false, '');
        $pdf->Ln();

        $pdf->Cell(20, 3, 'Phone: ' . $phone, '', true);
        $pdf->Cell(185, 3, $user_phone, 0, 0, 'R', false, '');
        $pdf->Ln();
        $pdf->Ln();

        //set the body
        $pdf->SetFillColor(128, 128, 128);
        $pdf->SetTextColor(255);
        $pdf->SetDrawColor(92, 92, 92);

        $pdf->Cell(110, 8, "Product", 1, 0, 'C', true);
        $pdf->Cell(45, 8, "Invoice Date", 1, 0, 'C', true);
        $pdf->Cell(30, 8, "Cost this month", 1, 0, 'C', true);
        $pdf->Ln();


        $total = 0;
        $pdf->SetWidths(array(110, 45, 30));



        if (!isset($this->product_model))
            $this->load->model('admin/product_model');

        //$pdf->SetAligns('C');
        foreach ($orders as $key => $value) {
            $order_id = $value['id'];
            $product_name = $this->product_model->get_product_name($value['product']);
            $date = $month;
            $price = $value['price'];

            $cost = 'R ' . $price;
            $total = $total + $price;

            $pdf->Row(array($product_name, $date, $cost));
        }



        $pdf->SetFillColor(255, 255, 255);
        $pdf->Ln(1);
        $pdf->Cell(185, 8, 'Total: R ' . $total, 0, 0, 'R', true);
        $pdf->Ln();
        $pdf->Cell(0, 8, INVOICE_VAT_ROW, 0, 0, '', true);


        //save the pdf file to local file
        $path_name = APPPATH . 'PDFfiles/' . $username;
        if (is_dir($path_name) == false) {
            // echo "<br/>path not exist";
            mkdir($path_name, 0777);
        }

        $file_name = $month_invoice_id . '.pdf';
        $file_save_path = $path_name . '/' . $file_name;
        $pdf->Output($file_save_path, 'F');

        //save the pdf
        $pdf_data = array(
            'name' => $file_name,
            'path' => $file_save_path,
            'create_date' => date('Y-m-d H:i:s', time()),
            'user_name' => $username,
            'invoices_id' => $month_invoice_id
        );
        $result = $this->db->insert('invoice_pdf', $pdf_data);
        $pdf_id = $this->db->insert_id();
        return $pdf_id;
    }


    function generate_invoice_data_incl_skip_id($data, $skip_id, $last_invoice)
    {

        if ($skip_id == 0)
            return $data;

        $last_invoice_id = $last_invoice[0]['id'];
        $new_invoice_id = $last_invoice_id + $skip_id;
        //$new_invoice_id = $skip_id;

        if (empty($new_invoice_id))
            return $data;

        // check if this ID already exist
        $invoice = $this->get_invoice_by_id($new_invoice_id);
        if (empty($invoice))
            $data['id'] = $new_invoice_id;

        return $data;
    }


    function generate_invoice_data_incl_custom_id($data, $custom_id, $last_invoice)
    {

        if ($custom_id == 0)
            return $data;

        $new_invoice_id = $custom_id;

        if (empty($new_invoice_id))
            return $data;

        // check if this ID already exist
        $invoice = $this->get_invoice_by_id($new_invoice_id);
        if (empty($invoice))
            $data['id'] = $new_invoice_id;

        return $data;
    }


    function get_invoice_by_id($id)
    {

        if (empty($id))
            return false;

        $this->db->where('id', $id);
        $query = $this->db->get('invoices');
        $result = $query->first_row('array');

        return $result;
    }


    function insert_month_log($month)
    {

        // check if this log already exist
        $checkLog = $this->get_month_log($month);
        if (!empty($checkLog)) {
            return $checkLog['id'];
        }

        $data = array(
            'month_invoice' => $month,
            'create_status' => 1,
            'send_email_status' => 0,
        );
        $this->db->insert('month_invoice_log', $data);
        $invoice_log_id = $this->db->insert_id();
        return $invoice_log_id;
    }

    function update_month_log($month)
    {
        $this->db->where('month_invoice', $month);
        $this->db->update('month_invoice_log', array('send_email_status' => 1));
    }

    function get_month_log($month)
    {
        $this->db->where('month_invoice', $month);
        $query = $this->db->get('month_invoice_log');
        $result = $query->first_row('array');
        return $result;
    }

    function update_invoice_log($invoice_log_id, $month_invoice_id)
    {
        $this->db->where('id', $month_invoice_id);
        $this->db->update('invoices', array('month_invoice_log_id' => $invoice_log_id));
    }

    function get_invoice_log_id($current_month)
    {
        $this->db->select('id');
        $this->db->where('month_invoice', $current_month);
        $query = $this->db->get('month_invoice_log');
        $result = $query->first_row('array');
        if ($result) {
            return $result['id'];
        } else {
            return null;
        }
    }

    function get_invoices_user($invoice_id)
    {
        $this->db->select('user_id');
        $this->db->where('id', $invoice_id);
        $query = $this->db->get('invoices');
        $result = $query->first_row('array');

        if ($result) {
            return $result['user_id'];
        } else {
            return null;
        }
    }

    function get_pdf_path($invoice_id)
    {
        $this->db->select('path');
        $this->db->where('invoices_id', $invoice_id);
        $query = $this->db->get('invoice_pdf');
        $result = $query->first_row('array');

        if ($result) {
            return $result['path'];
        } else {
            return null;
        }
    }

    function get_month_invoices($invoice_log_id)
    {
        $this->db->where('month_invoice_log_id', $invoice_log_id);
        $query = $this->db->get('invoices');
        $result = $query->result_array();
        if ($result) {
            return $result;
        } else {
            return null;
        }
    }

    function save_month_invoice_toggle($val)
    {

        if (!isset($val)) {

            return false;
        }

        $update_array = array('toggle' => $val);
        // month_invoice
        $this->db->where('action', 'month_invoice');
        $result =  $this->db->update('system_param', $update_array);

        return $result;
    }


    function get_month_invoice_toggle()
    {



        $this->db->select('toggle');
        $this->db->where('action', 'month_invoice');
        $query = $this->db->get('system_param');
        $result = $query->result_array();


        return $result[0]['toggle'];
    }



    // ================= invoices cron =========================


    function create_next_invoice()
    {

        // $this->invoice_cron_log("'create nex invoice' start");
        $month = date('M/Y', strtotime('+1 month'));

        // get active users
        $user_query = $this->db->query('select distinct id_user, user from orders
										where status ="active"');
        $user_list = $user_query->result_array();

        //$this->invoice_cron_log("active users count : " . count($user_list));

        $data = array();
        if ($user_list) {


            if (!isset($this->user_model))
                $this->load->model('admin/user_model');

            // month_invoice_log
            $invoice_log_id = $this->insert_month_log($month);
            foreach ($user_list as $key => $value) {
                $username = $value['user'];
                $user_id = $value['id_user'];

                // $this->invoice_cron_log(" users : " . $username . "   enter loop");

                $month_invoice_id = $this->save_montly_inovice($username, $user_id);
                // $this->invoice_cron_log(" users : " . $username . "   saved monthly invoice");

                $user_data = $this->user_model->get_user_data_by_username_and_id($username, $user_id);

                $this->create_month_invoice_pdf($username, $user_id, $month_invoice_id, $user_data);
                //  $this->invoice_cron_log(" users : " . $username . "   created pdf");


                $this->update_invoice_log($invoice_log_id, $month_invoice_id);
                //  $this->invoice_cron_log(" users : " . $username . "   invoice updated") ;
                $data[] = $month_invoice_id;
            }
        }

        //  $this->invoice_cron_log("'create nex invoice' end");
        return $data;
        //  echo json_encode($data);
    }


    function send_invoices()
    {

        //  $this->invoice_cron_log("'send invoices' - start");
        if (!isset($this->membership_model)) {
            $this->load->model('membership_model');
        }


        $date = date('M/Y', strtotime('+1 month'));
        $invoice_log_id = $this->get_invoice_log_id($date);
        // $this->invoice_cron_log("get invoice log");

        if ($invoice_log_id) {
            $invs = $this->get_month_invoices($invoice_log_id);
            //$invs = explode(',', $_POST['invoices']);

            if (!isset($this->user_model))
                $this->load->model('admin/user_model');


            foreach ($invs as $key => $value) {



                $invoice_id = $value['id'];

                $pdf_path = $this->get_pdf_path($invoice_id);
                $user_id = $this->get_invoices_user($invoice_id);

                $user_bulk_invoice_param = $this->user_model->get_user_invoice_mail_param_by_id($user_id);
                //email some
                $username = $this->user_model->get_user_name_by_id($user_id);
                //  $this->invoice_cron_log("user : " . $username . "  |  bulk  : " . $user_bulk_invoice_param);

                if ($user_id &&  $user_bulk_invoice_param) {

                    // TODO: CHECK !
                    $email = $this->membership_model->get_user_email($user_id);
                    $name = $this->membership_model->get_user_name($user_id);

                    $body = "Dear $name,
This is the invoices for $date,
Please check the attachment.
If you have any billing queries, please do not hesitate to contact admin@openweb.co.za
Kind regards
Keoma Wright
Founder
OpenWeb.co.za";

                    $this->load->library('email');
                    $this->email->from('admin@openweb.com', 'OpenWeb Home');
                    $this->email->to($email);
                    $this->email->subject("Invoice for $date");
                    $this->email->message($body);
                    $this->email->attach($pdf_path);
                    if ($this->email->send()) {


                        $this->update_month_log($date);
                        //  $this->invoice_cron_log("user : " . $username . "  | email sent");
                    }
                    $this->email->clear(TRUE);
                }
            }
        }


        // $this->invoice_cron_log("'send invoices' - end");
    }


    function month_auto_invoice_with_email()
    {


        $this->create_next_invoice();
        $this->send_invoices();
    }



    function month_auto_cron_check($check_param)
    {


        // DATE
        $currentDay = date('d');
        $currentH   =  date('H');
        $cron_date = date("Y-m-d 00:00:00");
        $lastDayDateTime = new DateTime('last day of this month');
        $lastDay = date_format($lastDayDateTime, 'd');
        // if ($lastDay == 29)
        //     $lastDay = 28;

        // TODO : CHANGE_THIS_DATA_FOR_PROD ===
        // $currentDay = 31;
        // $currentH = 23;
        //
        // =================================================

        // Secure check
        if ($check_param !=  "fgf6kocujghbe32s") {
            // # write date & die + reason
            //  $this->invoice_cron_log("'check' param is wrong => " . $check_param);
            return false;
        }

        if (($currentDay != $lastDay) || ($currentH < 23)) {

            // # write date & die + reason
            /* $this->invoice_cron_log("wrong date    =>  current day :  " . $currentDay .
                                    "\n             last day :  " . $lastDay .
                                    "\n              currentH : " . $currentH
            ); */
            return false;
        }

        // ==================================================

        $invoice_toggle = $this->get_month_invoice_toggle();


        if (!isset($invoice_toggle) || ($invoice_toggle == 0)) {


            // # write date & die + reason
            // $this->invoice_cron_log("toggle is OFF");
            show_404();
            die;
        }


        $month = $date = date('M/Y', strtotime('+1 month'));
        $month_invs_log = $this->get_month_log($month);


        if (!empty($month_invs_log)) {

            // # write date & die + reason
            //  $this->invoice_cron_log("month invoice log already exist (database)");
            return false;
        }
        // =======================================================
        return true;
    }

    // ======================================================
    // =======================================================



    function invoice_start_cron_log()
    {

        $current_date = date("y-m-d H:i:s");
        $str = "\n\n\n   cron start : " . $current_date;
        $str .= "\n ===========================================";
        // $log_file_path = dirname(__FILE__) . "/../logs/invoice_cron_log.txt";
        $log_file_path = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..')  . "/logs/invoice_cron_log.txt";
        $log_handle = fopen($log_file_path, 'a+');
        fwrite($log_handle, $str);
        fclose($log_handle);
    }



    function invoice_end_cron_log()
    {

        $current_date = date("y-m-d H:i:s");
        $str = "\n   cron end : " . $current_date;
        $str .= "\n =========================================== \n";
        $log_file_path = dirname(__FILE__) . "/../logs/invoice_cron_log.txt";
        $log_handle = fopen($log_file_path, 'a+');
        fwrite($log_handle, $str);
        fclose($log_handle);
    }



    function invoice_cron_log($reason)
    {

        $current_date = date("y-m-d H:i:s");
        $str = "\n   " . $current_date . "     " . $reason;
        $log_file_path = dirname(__FILE__) . "/../logs/invoice_cron_log.txt";
        $log_handle = fopen($log_file_path, 'a+');
        fwrite($log_handle, $str);
        fclose($log_handle);
    }


    // ===========================================


    function get_full_order_list()
    {

        $this->db->order_by('date', 'desc');
        $query = $this->db->get('orders');
        $result = $query->result_array();

        return $result;
    }

    function get_is_details_by_id($product_class_id)
    {


        // From here we will get the class's realm details!
        $this->db->select('realm');
        $this->db->where('table_id', $product_class_id);
        $this->db->limit(1);
        $query = $this->db->get('is_classes');
        $result = $query->result_array();

        if ($result) {
            $realm = $result[0]['realm'];
            // Now we get realm user and password
            $this->db->select('user, pass');
            $this->db->where('realm', $realm);
            $query = $this->db->get('realms');
            $result = $query->result_array();
            $user = $result[0]['user'];
            $password = $result[0]['pass'];

            $data = array(
                'user' => $user . "@" . $realm,
                'pass' => $password,
                'realm' => $realm,
            );
            return $data;
        }
        return false;
    }

    function update_order_realm($order_id, $realm)
    {

        $this->db->where('id', $order_id);
        $update_result = $this->db->update('orders', array('realm' => $realm));
        return $update_result;
    }


    function get_order_realm($order_id)
    {

        $this->db->select('realm');
        $this->db->where('id', $order_id);
        $query = $this->db->get('orders');
        $result = $query->first_row('array');

        $return_realm = null;
        if (!empty($result))
            $return_realm = $result['realm'];

        return $return_realm;
    }




    function get_is_class_id($order_id)
    {
        // First get product ID
        $this->db->select('product');
        $this->db->where('id', $order_id);
        $this->db->limit(1);
        $query = $this->db->get('orders');
        $result = $query->result_array();
        $product_id = $result[0]['product'];

        // Then get product class_id
        $this->db->select('class_id');
        $this->db->where('id', $product_id);
        $this->db->limit(1);
        $query = $this->db->get('products');
        $result = $query->result_array();
        $product_class_id = $result[0]['class_id'];

        return $product_class_id;
    }

    // ex get_is_detail
    function get_realm_data_by_order_id($order_id, $class = null)
    {

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // get realm by id
        $this->db->select('realm');
        $this->db->where('id', $order_id);
        $query = $this->db->get('orders');
        $result_realm = $query->first_row('array');

        if (empty($result_realm))
            return false;

        $realm_name = $result_realm['realm'];

        $detail_array = false;
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        // echo "<br/> realm name : $realm_name";

        if ($realm_name != null) {

            // echo "<br/> realm exist";
            //                     relm exist
            // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

            $this->db->select('user, pass');
            $this->db->where('realm', $realm_name);
            $query2 = $this->db->get('realms');
            $result_detail = $query2->result_array();
            $user = $result_detail[0]['user'];
            $password = $result_detail[0]['pass'];

            $detail_array = array(
                'user' => $user . "@" . $realm_name,
                'pass' => $password,
                'realm' => $realm_name,
            );

            // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        } else {

            //  echo "<br/> realm not exist";
            // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

            // get product by order id
            $order_array = $this->get_order_data($order_id);
            if (empty($order_array))
                return false;  // order data is not exist


            //  echo "<br/>order data exist";

            $product_id = $order_array['product'];
            if (!isset($this->product_data))
                $this->load->model('admin/product_model');


            // get class_id by product
            $class_id = $this->product_model->get_product_class_id($product_id);


            // if class id is  exist
            if ($class_id != null) {

                // echo "<br/> class id exist";
                // get detail by class
                $detail_array = $this->get_is_details_by_id($class_id);
            } else {

                //  echo "<br/>class id not exist";

                // ##try old IS DETAIL##
                if ($class != null) {

                    //   echo "<br/>class is set";
                    $detail_array = $this->get_is_details($class);
                }
            }
        } // realm name null (end)

        return $detail_array;
    }

    function get_all_undef_orders($num = 10, $start = 0)
    {

        $this->db->select();
        $this->db->where('realm IS NULL');
        $this->db->limit($num, $start);
        $this->db->order_by('date', 'desc');
        $query = $this->db->get('orders');
        $result = $query->result_array();
        // ?
        $result = $this->get_updated_cancellations($result);
        return $result;
    }


    function get_all_undef_orders_count()
    {
        $this->db->select('id');
        $this->db->where('realm IS NULL');
        $this->db->from('orders');
        $query = $this->db->get();
        return $query->num_rows();
    }

    function get_order_id_by_username_and_realm($username, $realm = null)
    {

        $this->db->select('id');
        $this->db->where('account_username', $username);
        if ($realm != null) {
            $this->db->where('realm', $realm);
        }
        $query = $this->db->get('orders');
        $result = $query->first_row('array');
        if (!empty($result))
            return $result['id'];

        return false;
    }

    function get_last_order_by_username($username)
    {


        if (empty($username))
            return false;

        $this->db->select('');
        $this->db->where('user', $username);
        $this->db->where('type', 'auto');
        $this->db->order_by('id', 'desc');

        $query = $this->db->get('orders');
        $result = $query->first_row('array');


        if (!empty($result)) {

            return $result;
        }

        return false;
    }

    function check_order_by_username_realm($user_name, $realm)
    {

        $answer = false;

        $this->db->select();
        $this->db->where('account_username', $user_name);
        $this->db->where('realm', $realm);


        $query = $this->db->get('orders');
        $result = $query->first_row('array');

        if (!empty($result))
            $answer  =  true;

        return $answer;
    }

    /**
     *  Process post data and assign order according to the service
     *
     * @param  array $post_data
     * @param  string $service
     * @return array|bool
     */
    function handle_assign_order_process($post_data, $service)
    {

        if (empty($post_data['username']))
            return false;

        // check service and assign corresponding order
        switch ($service) {
            case "adsl":
                return;
                echo "1";
                break;
            case "lte-a":
                retrun;
                echo "1";
                break;
            case "fibre-data":
            case "fibre-line":
                $result = $this->manual_assign_fibre_order($post_data, $service);
                break;
            case "showmx-sub":
                $result = $this->assign_showmax_subscription($post_data, $service);
                break;


                //case "fibre-data" : $result  =  $this->manual_assign_fibre_data($post_data);  break;
                //case "fibre-line" : $result  =  $this->manual_assign_fibre_line($post_data);  break;
        }

        return $result;
    }

    /**
     *  Assign fibre order
     *
     * @param $post_data
     * @param $service
     * @return array
     */
    function manual_assign_fibre_order($post_data, $service)
    {

        $order_type = $service;
        $fibre_type = $service;
        //if ($service == 'fibre-line')
        //    $post_data['status'] = '';


        // get fibre type for inserting and prepare data
        if ($service != 'lte-a') {
            $fibre_type = str_replace('fibre-', '', $fibre_type);
        }

        $insert_array = array(

            'user_id'             => $post_data['user_id'],
            'username'            => $post_data['username'],

            'product_name'        => $post_data['product_name_fd'],
            'fibre_type'          => $fibre_type,

        );

        if ($service == 'fibre-line')
            $insert_array['fibre_line_number'] = $post_data['number_fl'];

        if ($service == 'fibre-data') {

            $insert_array['fibre_data_username'] = $post_data['username_fd'];
            $insert_array['fibre_data_password'] = $post_data['password_fd'];
            $insert_array['fibre_data_provider'] = $post_data['provider_fd'];
        }

        if ($service = 'lte-a') {
            // print_r($post_data['lte_type']);exit();
            $insert_array['fibre_data_username'] = $post_data['username_la'];
            $insert_array['fibre_data_password'] = $post_data['password_la'];
            $insert_array['lte_type'] = $post_data['lte_type'];

            if ($post_data['lte_type'] == 'telkom' || $post_data['lte_type'] == 'mtn') {
                $insert_array['sim_serial_no'] = $post_data['sim_serial_number'];
            }
        }

        // check if this fibre order already exist
        $empty_result = $this->get_fibre_order_by_type($insert_array);
        if ($service != 'lte-a' && !empty($empty_result)) {

            // remove previous simple manual order (don't need because order not created yet)
            // $remove_result = $this->local_order_remove($simple_order_id, $post_data['user_id']);

            return array(
                'result' => false,
                'message' => 'This fibre order is already exist !',
                'dev_message' => 'This fibre order has already existed !'
            );
        }


        // assign simple order
        $simple_order_id = $this->manual_assign_simple_order($post_data, $order_type);


        // if simple order was not assigned
        if (!$simple_order_id)
            return array(
                'result' => false,
                'message' => 'The order has been assigned unsuccessfully !',
                'dev_message' => 'Error during insertion of simple order'
            );


        $insert_array['order_id'] = $simple_order_id;


        // insert fibre order
        $fibre_insert = $this->db->insert('fibre_orders', $insert_array);
        if (!$fibre_insert)
            return array(
                'result' => false,
                'message' => 'The order has been assigned unsuccessfully !',
                'dev_message' => 'Error during insertion of fibre order'
            );


        //$fibre_id = $this->db->insert_id();

        return array(
            'result' => true,
            'message' => 'The order has been assigned successfully !',
            'dev_message' => ' - '
        );
    }



    function assign_showmax_subscription($post_data, $service)
    {

        /*
         * string(10) "showmx-sub" -//-array(6)
         * { ["service"]=> string(10) "showmx-sub"
         * ["username"]=> string(8) "test-vvv"
         * ["showmax_subscription_type"]=> string(7) "premium"
         * ["price"]=> string(1) "0" ["proRata"]=> string(1) "0"
         * ["user_id"]=> string(4) "8901"
         * ["status"] => "str"
         * }
         *
         */

        $order_type = $service;

        //  create SM_manger
        if (empty($this->showmax_manager))
            $this->load->model("showmax_manager");

        // create SM subscription via API

        if ($post_data["status"] != "active")
            return array(
                'result' => false,
                'message' => 'Showmax subscription was not assigned',
                'dev_message' => "Only `Active` subscriptions can be assigned",
            );

        $showmax_subscription_result = $this->showmax_manager->activate_showmax_subscription(
            $post_data["user_id"],
            $post_data["showmax_subscription_type"]
        );

        // subscription was created unsuccesfully
        if (!$showmax_subscription_result["result"]) {
            return array(
                'result' => false,
                'message' => 'Showmax subscription was not assigned',
                'dev_message' => $showmax_subscription_result["message"]
            );
        }


        // assign simple order
        $simple_order_id = $this->manual_assign_simple_order($post_data, $order_type);


        // if simple order was not assigned
        // TODO : possible issues here
        if (!$simple_order_id)
            return array(
                'result' => false,
                'message' => 'The order has been assigned unsuccessfully !',
                'dev_message' => 'Error during insertion of simple order'
            );


        // set row id from showmax_subscription table
        $where_data = array("id" => $showmax_subscription_result["additional_data"]["subscription_db_log_id"]);
        $update_data['order_id'] = $simple_order_id;
        $showmax_subscription_update = $this->showmax_manager->update_subscription($where_data, $update_data);

        // update showmax subscriptions
        if (!$showmax_subscription_update)
            return array(
                'result' => false,
                'message' => 'The order has been assigned unsuccessfully !',
                'dev_message' => 'Error during update process'
            );


        //$fibre_id = $this->db->insert_id();

        return array(
            'result' => true,
            'message' => 'The order has been assigned successfully !',
            'dev_message' => ''
        );
    }

    /**
     * Get latest active Showmax subscription order from DB
     *
     * @param $user_id
     * @return array or bool
     */
    function get_showmax_subscription_order_from_db($user_id, $order_status = "active")
    {

        $this->db->select();
        $this->db->where('id_user', $user_id);
        $this->db->where('service_type', "showmx-sub");
        if ($order_status != null)
            $this->db->where("status", $order_status);
        $this->db->limit(1, 0);
        $this->db->order_by('date', 'desc');
        $query  = $this->db->get('orders');
        $result = (array)$query->first_row();

        return $result;
    }

    function validate_showmax_subscription_order($order_id, &$showmax_manager)
    {

        // get order from DB + get showmax subscription and valdiate it
        $order_data = $this->get_order_data($order_id);
        $showmax_subscription = $this->get_showmax_subscription($order_data["id_user"], $showmax_manager);
        $response = array(
            "order_data" => $order_data,
            "showmax_subscription" => $showmax_subscription,
            "validation_result" => false,
        );

        if ($order_data["id"] == $showmax_subscription["id"])
            $response["validation_result"] = true;


        return $response;
    }

    function get_showmax_subscription($user_id, &$showmax_manager = null)
    {

        $showmax_order = $this->get_showmax_subscription_order_from_db($user_id);
        if (empty($showmax_order))
            return $showmax_order;

        // check showmax model
        if (empty($showmax_manager)) {
            $this->load->model("showmax_manager");
            $showmax_manager = &$this->showmax_manager;
        }

        // get related info from 'showmax_subscription' table
        $search_params = array("order_id" => $showmax_order["id"], "user_id" => $user_id);
        $showmax_order["showmax_subscription"] = $showmax_manager->get_subscriptions($search_params);
        if (isset($showmax_order["showmax_subscription"][0]))
            $showmax_order["showmax_subscription"]  = $showmax_order["showmax_subscription"][0];

        return $showmax_order;
    }



    /**
     * @param array $data data for inserting
     * @param int $user_id (optional)
     * @return array
     */
    function get_fibre_order_by_type($data, $user_id = null)
    {

        $this->db->select();

        if (!empty($user_id))
            $this->db->where('user_id', $data['user_id']);

        if ($data['fibre_type'] == 'data' || $data['fibre_type'] == 'lte-a') {
            $this->db->where('fibre_data_username', $data['fibre_data_username']);
            $this->db->where('fibre_data_provider', $data['fibre_data_provider']);
        } else {
            $this->db->where('fibre_line_number', $data['fibre_line_number']);
        }

        $query = $this->db->get('fibre_orders');
        $row = $query->first_row('array');


        return $row;
    }

    /**
     * Assign ADSL order
     *
     * @param $post_data
     * @param $service
     * @return bool|int returns false result or inserted_id
     */
    function manual_assign_simple_order($post_data, $service)
    {


        $flag_key = '';
        if ($service != 'adsl') {
            $flag_key = '_fd';
        }

        // process form checkboxes
        $change_flag = 0;
        if (isset($post_data['change_flag' . $flag_key]) && ($post_data['change_flag' . $flag_key] != ""))
            $change_flag = $post_data['change_flag' . $flag_key];

        $display_usage = 0;
        if (isset($post_data['display_usage' . $flag_key]) && ($post_data['display_usage' . $flag_key] != "")) {
            $display_usage = $post_data['display_usage' . $flag_key];
        }

        $cancel_flag = 0;
        if (isset($post_data['cancel_flage']) && ($post_data['cancel_flage'] != ""))
            $cancel_flag = $post_data['cancel_flage'];


        $billing_cycle = '';
        if (isset($post_data['billing_cycle']))
            $billing_cycle = $post_data['billing_cycle'];


        /*
        echo "<pre>";
        print_r($post_data);
        echo "</pre>";
        echo "<hr/>";
        echo "<pre>";
        var_dump($service);
        echo "</pre>";
        die("inside manual simple order");
        */
        $realm = '';
        $username = '';
        if ($service == 'lte-a') {
            $realm = $post_data['realm'];
            $username = substr($post_data['username_la'], 0, strpos($post_data['username_la'], '@'));
        }

        // construct data for insert
        $insert_data = array(

            'user'             => $post_data['username'],
            'product'          => '',                     // empty for fibre
            'status'           => $post_data['status'],
            'account_username' => $username,                     // empty for fibre not for LTE-A
            'account_password' => '',                     // empty for fibre
            'realm'            => $realm,                     // empty for fibre not for LTE-A
            'price'            => $post_data['price'],
            'pro_rata_extra'   => $post_data['proRata'],
            'account_comment'  => '',                     // empty for fibre

            'change_flag'      => $change_flag,
            'display_usage'    => $display_usage,
            'type'             => 'manual',
            'cancel_flage'     => $cancel_flag,
            'id_user'          => $post_data['user_id'],
            'billing_cycle'    => $billing_cycle,                    // empty for fibre

            'service_type'     => $service,
            'avios_code'       => $post_data['avios_code']
        );

        // check if this order already exist ,
        // if exist     -> return order_id
        // if not exist -> return false;
        $simple_order_exist = $this->check_db_for_simple_order($insert_data); // TRUE or FALSE
        // if order exist - returns order_id
        $result = $simple_order_exist;
        // if simple order doesn't exist - assign new order
        if (!$simple_order_exist)
            $result = $this->assign_order($insert_data);

        return $result;
    }



    function check_db_for_simple_order($data)
    {

        // doesn't have unique fields yet / to identify order
        // ------------------------------

        //$this->db->select();
        //$this->db->where('user', $data['user']);
        return false;
    }




    /*
    function manual_assign_new_adsl_order($post_data){

        $change_flag = 0;
        if(isset($post_data['change_flag']))
            $change_flag = $post_data['change_flag'];


        $display_usage = 0;
        if(isset($post_data['display_usage']))
            $display_usage = $post_data['display_usage'];

        $cancel_flage = 0;
        if(isset($post_data['cancel_flage']))
            $cancel_flage = $post_data['cancel_flage'];

        // -------------------------------------------

        if (isset($_POST['email_sms'])){
            $email_sms = $_POST['email_sms'];
        } else {
            $email_sms = 0;

        }


        if (isset($_POST['write_to_log'])){
            $write_to_log = $_POST['write_to_log'];
        } else {
            $write_to_log = 0;
        }
        $order_data = array (
            'user' => $post_data['username'],
            'product' => $post_data['$product_id'],
            'status' => $post_data['order_status'],
                /*
            'account_username' => $_POST['account_username'],
            'account_password' => $_POST['account_password'],
            'realm'  => $_POST['realm'],
            'price' => $_POST['price'],
            'pro_rata_extra' => $_POST['proRata'],
            'account_comment' => $comment,
            'change_flag' => $change_flag,
            'display_usage' => $display_usage,
            'type' => 'manual',
            'cancel_flage' =>$cancel_flage,
            'id_user' => $id_user,
            'billing_cycle' => $billing_cycle,
        );

        $insert_result = $this->assign_order($order_data);
        return $insert_result;


    }
    */


    /**
     *  Get fibre data from DB
     *
     * @param int $order_id
     * @param int $user_id (optional)
     * @param int $username (optional)
     * @return bool or array
     */
    function get_fibre_data_by_order($order_id, $user_id = null)
    {

        if (empty($order_id))
            return false;

        $this->db->where('order_id', $order_id);
        if (!empty($user_id))
            $this->db->where('user_id', $user_id);


        $query = $this->db->get('fibre_orders');
        // $result = $query->result_array();
        $result = $query->first_row('array');

        return $result;
    }

    /**
     *  Validate service type and get fibre data
     *
     * @param string $service_type
     * @param string $username
     * @param int $user_id
     * @param int $order_id
     * @return bool or array
     */
    function check_fibre_data($service_type, $order_id,  $user_id)
    {

        if (($service_type == 'adsl'))
            return false;

        if (empty($user_id))
            return false;

        $fibre_data = $this->get_fibre_data_by_order($order_id, $user_id);
        return $fibre_data;
    }

    function update_fibre_data($service_type, $order_id, $user_id)
    {

        $fibre_data = $this->validation_model->handle_update_order_fields($service_type);
        if (empty($fibre_data))
            return array(
                'result' => false,
                'message' => "Fibre order can't be empty",
                'dev_message' => ''
            );


        // check
        if (!$this->check_fibre_access($order_id, $user_id, $fibre_data['fibre_id']))
            return array(
                'result' => false,
                'message' => "This fibre order does not belong to this user",
                'dev_message' => ''
            );

        $update_array = array();
        $update_array['product_name'] = $fibre_data['product_name_fd'];
        $update_array['fibre_type'] = $service_type;

        if ($service_type != 'lte-a')
            $update_array['fibre_type'] = str_ireplace('fibre-', '', $service_type);

        if ($update_array['fibre_type'] == 'data') {
            $update_array['fibre_data_username'] = $fibre_data['username_fd'];
            $update_array['fibre_data_password'] = $fibre_data['password_fd'];
            $update_array['fibre_data_provider'] = $fibre_data['provider_fd'];
        } else if ($update_array['fibre_type'] == 'lte-a') {
            $update_array['fibre_data_username'] = $fibre_data['username_fd'];
            $update_array['fibre_data_password'] = $fibre_data['password_fd'];
        } else { // for 'line' products
            $update_array['fibre_line_number']   = $fibre_data['number_fl'];
        }

        $search_fibre_order = $this->get_fibre_order_by_type($update_array);

        if (!empty($search_fibre_order) && ($search_fibre_order['order_id'] != $order_id))
            return array(
                'result' => false,
                'message' => 'This fibre order is already exist',
                'dev_message' => 'This fibre order has already exist'
            );


        unset($update_array['fibre_type']);
        $this->db->where('id', $fibre_data['fibre_id']);
        $result = $this->db->update('fibre_orders', $update_array);
        return array(
            'result' => $result,
            'message' => 'The fibre order was successfully updated',
            'dev_message' => 'The fibre order was successfully updated',
        );
    }


    function check_fibre_access($order_id, $user_id, $fibre_id)
    {

        $this->db->select();
        $this->db->where('id', $fibre_id);
        $this->db->where('order_id', $order_id);
        $this->db->where('user_id', $user_id);

        $query = $this->db->get('fibre_orders');
        $result = $query->result_array();

        // check if this order exist and unique
        if (!empty($result) && (count($result) === 1))
            return true;

        return false;
    }

    /*
     * Get active orders with avios billing code
     *
     */
    function getAviosOrders()
    {
    }

    function orderStatuses()
    {

        $query = $this->db->query('SELECT DISTINCT status FROM orders');
        $row = $query->result_array();
        $res = ['All'];
        foreach ($row as $status) {
            array_push($res, $status["status"]);
        }

        return $res;
    }

    function get_lte_without_type()
    {

        $q = "SELECT * FROM fibre_orders WHERE (lte_type <> 'rain' AND lte_type <> 'cell_c' OR lte_type IS null) AND fibre_type='lte-a'";
        $query = $this->db->query($q);

        $res = $query->result_array();

        return $res;
    }

    function update_lte_type($id, $type)
    {

        $data = ['lte_type' => $this->db->escape_str($type)];
        $this->db->where('id', $id);
        $q = $this->db->update('fibre_orders', $data);

        return $q;
    }

    function lte_form_data($orderData, $commonData)
    {
        $return = [
            'Product Name' => $orderData['product_name'],
            'Username' => $orderData['fibre_data_username'],
            'SIM Serial No' => $orderData['sim_serial_no'],
            'Status' => $commonData['status'],
            'Price' => $commonData['price'],
            'Pro Rata Extra' => $commonData['pro_rata_extra'],
            'display_usage' => $commonData['display_usage'],
            'change_flag' => $commonData['change_flag'],
            'cancel_flage' => $commonData['cancel_flage'],
        ];

        return $return;
    }
    function mobile_form_data($orderData, $commonData)
    {
        $return = [
            'Product Name' => $orderData['product_name'],
            'Username' => $orderData['fibre_data_username'],
            'SIM Serial No' => $orderData['sim_serial_no'],
            'Status' => $commonData['status'],
            'Price' => $commonData['price'],
            'Pro Rata Extra' => $commonData['pro_rata_extra'],
            'display_usage' => $commonData['display_usage'],
            'change_flag' => $commonData['change_flag'],
            'cancel_flage' => $commonData['cancel_flage'],
        ];

        return $return;
    }

    function update_lte_order($formData, $currentData)
    {

       


        // $formData['total_data'] = isset($formData['total_data']) ? $formData['total_data'] : $currentData['Total Data'];
        // $formData['percentage'] = isset($formData['percentage']) ? $formData['percentage'] : $currentData['Percentage'];

        $name = strstr($formData['username'], '@', true);
        if (empty($name)) {
            $name = $formData['username'];
        }
        // if (empty($formData['password'])) {
        //     $formData['password'] = null;
        // }
        // if (empty($formData['total_data'])) {
        //     $formData['total_data'] = null;
        // }
        // if (empty($formData['percentage'])) {
        //     $formData['percentage'] = null;
        // }
        // print_r($name);exit();           
        $data = [
            'product_name' => $formData['product_name'],
            'sim_serial_no' => $formData['sim_serial_no'],
            'fibre_data_username' => $name,

            // 'fibre_data_password' => $formData['password'],
            // 'total_data' => $formData['total_data'],
            // 'percentage' => $formData['percentage'],
        ];
     


        try {
            $this->db
                ->where('order_id', $formData['id'])
                ->update('fibre_orders', $data);
        } catch (Exception $e) {
            echo $e;
            die;
            // return false;
        }

        $data = [
            'price' => $formData['price'],
            'status' => $formData['status'],
            'billing_cycle' => $formData['cycle'],
            'account_username' => $name,
            'realm' => ltrim($formData['realm'], '@')
        ];

        try {
            $this->db
                ->where('id', $formData['id'])
                ->update('orders', $data);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    function update_mobile_order($formData, $currentData)
    {


        $name = strstr($formData['username'], '@', true);
        if (empty($name)) {
            $name = $formData['username'];
        }

        $data = [
            'product_name' => $formData['product_name'],
            'sim_serial_no' => $formData['sim_serial_no'],
            'fibre_data_username' => $name,
        ];



        try {
            $this->db
                ->where('order_id', $formData['id'])
                ->update('fibre_orders', $data);
        } catch (Exception $e) {
            return false;
        }

        if (isset($formData['display_usage']) && !empty($formData['display_usage'])) {
            $display_usage = 1;
        } else {
            $display_usage = 0;
        }
        if (isset($formData['cancel_flage']) && !empty($formData['cancel_flage'])) {
            $cancel_flage = 1;
        } else {
            $cancel_flage = 0;
        }

        $data = [
            'price' => $formData['price'],
            'pro_rata_extra' => $formData['pro_rata_extra'],
            'status' => $formData['status'],
            'display_usage' => $display_usage,
            'cancel_flage' => $cancel_flage,
            'account_username' => $name,
            // 'realm' => ltrim($formData['realm'], '@')
        ];

        try {
            $this->db
                ->where('id', $formData['id'])
                ->update('orders', $data);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function user_percentage_sum($stats_data, $order_id, $month_usage)
    {
        $data = $this->db
            ->select('total_data, percentage')
            ->where('order_id', $order_id)
            ->get('fibre_orders')
            ->result();

        foreach ($stats_data as &$stats) {

            if ($stats['Title'] != 'Main') {
                continue;
            }

            if ($data[0]->total_data != '' && $data[0]->total_data != 0) {
                $stats['Total Data'] = $data[0]->total_data;

                if ($stats['Data Units'] == 'MB') {
                    $stats['Total Data'] = $stats['Total Data'] * 1024;
                }
            }

            if ($stats['Remaining Data'] > 0) {
                $stats['Remaining Data'] = $stats['Total Data'] - $month_usage;
            }
        }

        return $stats_data;
    }
}
