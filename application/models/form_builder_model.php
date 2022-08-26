<?php

class Form_builder_model extends CI_Model
{

    function saveNewForm($fields) {

        foreach ($fields['fields'] as &$field) {

            if($field['val'] == 'drop') {
                $field['desc'] = explode(',', $field['desc']);

                foreach ($field['desc'] as &$one) {
                    $one = trim($one);
                }
            }
        }

        $query = $this->db->get_where('form_builder_fields', ['product_type' => $fields['type']]);
        $form = $query->result_array();

        $data = [
            'product_type' => $fields['type'],
            'product_id' => 0,
            'fields' => json_encode($fields['fields'])];

        if (empty($form)) {
            $this->db->insert('form_builder_fields', $data);
            $form_id = $this->db->insert_id();
            $this->updateFormID($form_id, $fields['type']);
            return true;
        } else {
            $this->db->where('product_type', $fields['type']);
            $this->db->update('form_builder_fields', $data);
            return true;
        }

    }

    function updateFormID($form_id, $product_id) {
        $this->db->where('id', $product_id);
        $this->db->update('form_builder_types', ['form_id' => $form_id]);
    }

    function getOrderTypes() {

        $query = $this->db->get('form_builder_types');
        $res = $query->result_array();

        $names = [];

        foreach ($res as $name) {
            $arr = [
                'name' => $name['name'],
                'id' => $name['id']
            ];
            array_push($names, $arr);
        }

        return $names;
    }

    function getAvailableProducts($type) {

        $this->db->where('type_id', $type);
        $query = $this->db->get('products_for_order');
        $res = $query->result_array();

        return $res;
    }

    function getManualOrderTypeData($id) {

        $this->db->where('id', $id);
        $query = $this->db->get('form_builder_types');
        $res = $query->result_array();

        return $res[0];
    }

    function getManualOrderProdData($id) {

        $this->db->where('id', $id);
        $query = $this->db->get('products_for_order');
        $res = $query->result_array();

        return $res[0];
    }

    function editOrderTypeData($data) {

        $this->db->where('id', $data['id']);
        unset($data['id']);
        $res = $this->db->update('form_builder_types', $data);
        return $res;
    }

    function editProdTypeData($data) {

        $this->db->where('id', $data['id']);
        unset($data['id']);
        $res = $this->db->update('products_for_order', $data);
        return $res;
    }

    function deleteSpecType($id) {
        $this->db->where('id', $id);
        $res = $this->db->delete('form_builder_types');
        return $res;
    }

    function deleteSpecProd($id) {
        $this->db->where('id', $id);
        $res = $this->db->delete('products_for_order');
        return $res;
    }

    function addType($data) {

        $res = $this->db->insert('form_builder_types', $data);
        if ($res) {
            $id = $this->db->insert_id();
            return ['status' => 'ok', 'id' => $id];
        }
    }

    function addProduct($data) {

        $res = $this->db->insert('products_for_order', $data);
        if ($res) {
            $id = $this->db->insert_id();
            return ['status' => 'ok', 'id' => $id];
        }
    }

    function getFormFields($id) {

        $res = $this->db->where('id', $id)
            ->get('form_builder_fields')
            ->result_array();

        return $res[0]['fields'];
    }

    function getFormId($id) {
        $res = $this->db->where('id', $id)
            ->get('form_builder_types')
            ->result_array();

        return $res[0]['form_id'];
    }

    function getTypesIds() {

        $types = $this->db->get('form_builder_types')->result_array();
        $res = [];
        foreach ($types as $type) {
            array_push($res, $type['id']);
        }

        return $res;
    }

    function getSpecProduct($id) {
        $res = $this->db->where('id', $id)
            ->get('products_for_order')
            ->result_array();

        return $res[0];
    }
}