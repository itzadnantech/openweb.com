<?php

class Flat_ui_model extends CI_Model {

    function check_ui_prefix(){


        $ui_prefix = '';
        $flat_ui_const = FLAT_UI;


        if (!empty($flat_ui_const) && ($flat_ui_const === true) )
            $ui_prefix = 'flat-ui/';

        return $ui_prefix;
    }



}