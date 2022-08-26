<?php


class migration_model extends CI_Model {


    function fetch_all_users_from_user_tbl(){

        $this->db->select();
        $query = $this->db->get('user_tbl');
        $result = $query->result_array();

        return $result;

    }


    function fetch_some_users_from_user_tbl($limit,$offset){

        $this->db->select();
        $query = $this->db->get('user_tbl',$limit,$offset);
        $result = $query->result_array();

        return $result;

    }





    function handle_all_old_user_tbl_row($limit, $offset){

        $all_old_orders = $this->fetch_some_users_from_user_tbl($limit, $offset);


        foreach ( $all_old_orders as $row){

            $this->handle_old_user_tbl_row($row);
        }

    }




    function handle_old_user_tbl_row($row){

        /*
         *  [User_Id] => 54
            [User_Support_Id] => 2549
            [User_No] => 67F8573C6E9C92748BBA58E464DF09E4
            [User_Profile] => EL59
            [User_Date] => 1344465416
            [User_Type] => 1
            [User_Credit] => 0.00
            [User_Email] => eltonlottering%40googlemail%2Ecom
            [User_Email_In] => 1
            [User_Mobile] => 0813003839
            [User_Mobile_In] => 1
            [User_ADSL] => 0186322233
            [User_ADSL_Address] =>
            [User_ADSL_Owner] =>
            [User_Login] => OW25056
            [User_Pass] => 76DB369F1A62BF186FDA083BB2CAC0F7
            [User_Pass_Bkp] => 669658
            [User_Pass_Done] => 1
            [User_Name] => Elton
            [User_Surname] => Lottering
            [User_DOB_Year] => 0
            [User_DOB_Month] => 0
            [User_DOB_Day] => 0
            [User_RegNo] => 9109045228087
            [User_Org] =>
            [User_VAT] =>
            [User_Address] => 29+Labellum+st%2C+Blydeville
            [User_Town] => Lichtenburg
            [User_Code] => 2740
            [User_State] => North+West
            [User_Country] => ZA
            [User_Ref] =>
            [User_Billing_CC] => 0
            [User_Billing_Profile] =>
            [User_Billing_Name] =>
            [User_Billing_Expire_Month] => 0
            [User_Billing_Expire_Year] => 0
            [User_Billing_Used] => 0
            [User_Debit] => 1
            [User_COrderInfo] => 31A7AE87C18B1CC0A2F0DF0D13702AEA3E637262B4DBC78DE3E92C5BC0F64CC4B2EBF0C8B0FC93D125DF37AA41BB8E93AACC4E672FE97425BC8A2367B3D1DC14
            [User_Mailer_SMS] => 0
            [User_Mailer_Email] => 1
            [User_OD_Access] => 0
            [User_OD_Domain] =>
            [User_OD_Status] => 0
            [User_Status] => 0
         */

        $user_insert_row = array(

            'email_address'  =>  urldecode($row['User_Email']), // NOT EMPTY!
            'mobile_number'  =>  urldecode($row['User_Mobile']),
            'username'       =>  urldecode($row['User_Login']),
            'password'	     =>  urldecode($row['User_Pass_Bkp']),     // NOT EMPTY!
            'first_name'     =>  urldecode($row['User_Name']),         // NOT EMPTY!
            'last_name'      =>  urldecode($row['User_Surname']),      // NOT EMPTY!

             'role'		     =>  "client",
             'joined'	     =>  date("Y-m-d H:i:s"),  //2014-04-21 05:37:47
             'discount'      =>  0,
             'status' 		 =>  'active',
             'mobile_number' =>  urldecode($row['User_Mobile']),
             'reason' 		 =>  '',
             'ow' 			 =>  urldecode($row['User_Login']),        // NOT EMPTY!
             'subscribe' 	 =>  '1',
             'bulk_email' 	 =>  '1',
             'invoice_email' =>  '0',
             'imported_user' =>  '1',

        );

        if (empty($user_insert_row['ow']) || empty($user_insert_row['email_address'])
            || empty($user_insert_row['first_name']) ||   empty($user_insert_row['last_name']))
                return;

        if (empty($user_insert_row['password'])){

            $length = rand(7,10);
            $random_string = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
            $user_insert_row['password'] = $random_string;
        }


        // check if user already exist ?
        $user_exist = $this->does_user_already_exist($user_insert_row);


        if (!$user_exist){

            // insert user
            $this->db->insert('membership', $user_insert_row);

            // return inserted id ;
            $inserted_id = $this->db->insert_id();


            $ow_exist = $this->does_ow_already_exist($user_insert_row['ow'], $inserted_id);
           // echo "<br/>" . $ow_exist;
           // echo "<br/>" . $user_insert_row['ow'] . " |  " . $inserted_id;

            if ($ow_exist == '2'){
                $this->create_existed_OW($inserted_id,  $user_insert_row['ow']);
            } elseif ($ow_exist == '3'){

                $this->remove_ow($user_insert_row['ow']);
                $this->create_existed_OW($inserted_id,  $user_insert_row['ow']);

            }


            $billing_insert_row = array(

                'username'     =>  urldecode($row['User_Login']),              // NOT EMPTY !
                'address_1'    =>  urldecode($row['User_Address']),  // NOT EMPTY !
                'city'         =>  urldecode($row['User_Town']),    // NOT EMPTY !
                'province'     =>  urldecode($row['User_State']),   // NOT EMPTY !
                'postal_code'  =>  urldecode($row['User_Code']),               // NOT EMPTY !
                'country' 	   =>  urldecode($row['User_Country']), // NOT EMPTY !
                'email' 	   =>  urldecode($row['User_Email']),   // NOT EMPTY !
                'mobile'	   =>  urldecode($row['User_Mobile']),
                'id_user'      =>  urldecode($inserted_id),
                'billing_name' =>  urldecode($row['User_Name'] . ' ' . $row['User_Surname']),  // NOT EMPTY !
                'expires_year' =>  '',
            );

            if (empty($billing_insert_row['address_1']))
                $billing_insert_row['address_1'] = "";

            if (empty($billing_insert_row['city']))
                $billing_insert_row['city'] = "";

            if (empty($billing_insert_row['province']))
                $billing_insert_row['province'] = "";

            if (empty($billing_insert_row['postal_code']))
                $billing_insert_row['postal_code'] = "";

            if (empty($billing_insert_row['country']))
                $billing_insert_row['country'] = "";

            // check if billing exist
            $billing_exist = $this->does_billing_already_exist($billing_insert_row);

            if (!$billing_exist){

                // insert billing
                $this->db->insert('billing', $billing_insert_row);

            }
        }
    }

    function does_user_already_exist($row){

        $this->db->select();
        $this->db->where('email_address', $row['email_address']);
        $this->db->or_where('username', $row['username']);
        $this->db->or_where('ow', $row['ow']);

        $query = $this->db->get('membership');
        $result = $query->first_row('array');

        if (empty($result))
            return false;

        return true;

    }

    function does_billing_already_exist($row){

        $this->db->select();
        $this->db->where('email', $row['email']);
        $this->db->or_where('id_user', $row['id_user']);
        $this->db->or_where('username', $row['username']);

        $query = $this->db->get('billing');
        $result = $query->first_row('array');

        if (empty($result))
            return false;

        return true;
    }

    function create_existed_OW($id_user,$ow)
    {

        $ow = str_replace('OW','',$ow);
        $this->db->insert('ow', array('id_user' => $id_user, 'ow_id' => $ow));
        return $this->db->insert_id();
    }

    function does_ow_already_exist($ow, $id_user){

        $ow = str_replace('OW','',$ow);

        $this->db->select('id_user');
        $this->db->where('ow_id', $ow);
        $query = $this->db->get('ow');

        $result = $query->first_row('array');

        if (empty($result))
            return '2';

        if ($result['id_user'] == $id_user)
            return '1';

        if ($result['id_user'] != $id_user)
            return '3';


        return true;
    }

    function remove_ow($ow){

        $ow = str_replace('OW','',$ow);
        return  $this->db->delete('ow', array('ow_id' => $ow));
    }


// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// ~~~~~~~~~~~~~~~~ order handlers ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~


    function fetch_all_orders(){


        $this->db->select();
        $query =  $this->db->get('order_tbl');
        $result = $query->result_array();

        return $result;

    }


    function fetch_some_orders_from_order_tbl($limit,$offset){

        $this->db->select();
        $query = $this->db->get('order_tbl',$limit,$offset);
        $result = $query->result_array();

        return $result;

    }


    function handle_all_old_orders_tbl_row($limit, $offset){

        $all_old_orders = $this->fetch_some_orders_from_order_tbl($limit, $offset);



       // echo "here";
      // echo "<pre>";
      //  print_r($all_old_orders);
      //  echo "</pre>";

        foreach ($all_old_orders as $row){

            echo "<br/>" . $row['Order_Id'];
            $this->handle_old_order_tbl_row($row);
        }

    }

    function handle_old_order_tbl_row($row){

     /*
            [Order_Id] => 29
            [User_Id] => 50
            [Invoice_Id] => 0
            [API_Id] => 0
            [Order_No] => 229E69033DF5EF9CEC051206411FD206
            [Order_Date] => 1344374364
            [Order_Reference] =>
            [Order_Code] => 2048kGold
            [Order_Text] => 2048k Gold Uncapped
            [Order_Details_User] => zem@openweb.co.za
            [Order_Details_Password] => 1122
            [Order_Details_Info] => 2048k Gold Uncapped
            [Order_Once] => 0
            [Order_Activate] => 1
            [Order_Activate_Month] => 8
            [Order_Activate_Year] => 2012
            [Order_Type] => 3
            [Order_Price] => 398
            [Order_Cap] => 0
            [Order_Usage] => 0
            [Order_Stats] => 1
            [Order_PW] => 1
            [Order_DOrder] => 0
            [Order_COrder] => 0
            [Order_COrder_Run] => 0
            [Order_COrder_Run_Status] => 0
            [Order_COrder_Rerun] => 0
            [Order_COrder_Checker] => 0
            [Order_COrder_Error] => 0
            [Order_Status] => 3
            [Order_Inv_Run] => 0
        )

    */


        $old_user_id = urldecode($row['User_Id']);


        $order_active = $row['Order_Activate'];


        if ($order_active != 1)
            return false;

        // find new user ID  and username and  get user row by User Id
        $old_user_row = $this->get_old_user_row_by_id($old_user_id);
        $new_user_database = $this->search_old_user_in_new_database($old_user_row);
        if ($new_user_database == null)
            return false;

         $new_user_id = $new_user_database['id'];
         $new_user_name = $new_user_database['username'];



        // date
        $month = urldecode($row['Order_Activate_Month']);


        if (strlen($month) == 1)
            $month = '0' . $month;
        $new_date_format =  urldecode($row['Order_Activate_Year']) . "-" . $month . '-01 00:00:00';



        // username and realm
        $old_username = urldecode($row['Order_Details_User']);
        $username_explode_array =  explode("@", $old_username);
        $new_order_name = $username_explode_array[0];
        $new_order_realm = $username_explode_array[1];

        // billing_cycle
        //`Order_Once`
        $old_billing_cycle = urldecode($row['Order_Once']);
        $new_billing_cycle = 'Monthly';
        if ($old_billing_cycle == 1){
            $new_billing_cycle = 'Once-Off';
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        $order_check_data = array(

            'account_username'  => $new_order_name,
            'account_password'  => urldecode($row['Order_Details_Password']),
            'id_user'           => $new_user_id,

        );
        $order_exist = $this->check_if_order_alrady_exist($order_check_data);

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        $order_text =  $row['Order_Text'];
        $product_id = 0;
        if (!empty($order_exist) && isset($order_exist['product']) && ($order_exist['product'] != 0) ){
            $product_id = $order_exist['product'];

        } else {
             $product_id = $this->insert_legacy_for_orders($order_text);

        }

        //var_dump($new_date_format);

        echo "<br/>$new_date_format";
        $order_insert_row = array(

            'user'              => $new_user_name,
            'product'           => $product_id,
            'date'              => date("Y-m-d H:i:s",strtotime($new_date_format)), // try change format
            'status'            => 'active',
            'price'             => urldecode($row['Order_Price']),
            'pro_rata_extra'    => '',
            'account_username'  => $new_order_name,
            'account_password'  => urldecode($row['Order_Details_Password']),
            'account_comment'   => '',
            'realm'             => $new_order_realm,
            'change_flag'       => '1',                              // !
            'date_cancelled'    => null,
            'date_update'       => null,
            'date_revoke'       => null,
            'type'              => 'manual', // auto or manual
            'display_usage'     => '1', //  `Order_Stats`            // !
            'cancel_flage'      => '', // `Order_PW`             s   // ! checl
            'modify_service'    => null,                            //  check / find in app
            'id_user'           => $new_user_id,
            'payment_method'    => '',
            'billing_cycle'     => $new_billing_cycle,          // ~



        );


        // does the order already exist ?
        $order_exist = $this->check_if_order_alrady_exist($order_insert_row);
        //var_dump($order_exist );
        if (empty($order_exist) ){

            // insert row
            $this->db->insert('orders', $order_insert_row);
            echo "<br/>  id : " . $row['Order_Id'] .  "  inserted";
        } else {

            // update
            $this->db->where('id', $order_exist['id']);
            $this->db->update('orders', $order_insert_row);
            echo  "<br/>  id : " . $row['Order_Id'] .  "  updated";
        }



    }



        function check_if_order_alrady_exist($row){

            $this->db->select('');
            $this->db->where('account_username', $row['account_username']);
            $this->db->where('account_password', $row['account_password']);
            $this->db->where('id_user', $row['id_user']);

            $query =  $this->db->get('orders');
            $result = $query->first_row('array');

            return $result;



        }

        function get_old_user_row_by_id($id){

            $this->db->select('');
            $this->db->where('User_Id', $id);
            $query = $this->db->get('user_tbl');

            $result = $query->first_row('array');

            return $result;

        }


        function search_old_user_in_new_database($row){


            $old_user_login = urldecode($row['User_Login']);

            $this->db->select('username, id');
            $this->db->where('username', $old_user_login);

            $query = $this->db->get('membership');
            $result = $query->first_row('array');

            if (!empty($result))
                return  $result;

             return null;


        }

        function insert_legacy_for_orders($name){



            $product_data = array (
                'name'   =>   $name,
                'parent' =>  'legacy',
                'class'  =>  'nosvc',
                'status' =>  'active',
                'active' =>  '1',
                'imported_product'  => '1'
            );
            $this->db->insert('products', $product_data);
            $product_id = $this->db->insert_id();

            return $product_id;
        }





}