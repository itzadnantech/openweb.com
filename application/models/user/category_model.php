<?php

class Category_model extends CI_Model {


	function get_categories() {
		$cat_fields = $this->get_category_fields();
		$get_fields = array();
		foreach ($cat_fields as $f=>$n) {
			array_push($get_fields, $f);
		}
		$get_fields_c = implode(',', $get_fields);
		$this->db->select('id,' . $get_fields_c);
		$query = $this->db->get('product_categories');
		return $query->result_array();
	}
	
	function get_subcategory_fields() {
		$category_fields = array (
				'name' => 'Sub-Category Name',
				'slug' => 'URL Slug',
				'parent' => 'Parent Category',
				'desc' => 'Category Description',
                'visible' => 'Product visibility'
		);
		return $category_fields;
	}


	function get_subcategories_assoc(){
	 	$cat_fields = $this->get_subcategory_fields();
		$get_fields = array();
		foreach ($cat_fields as $f=>$n) {
			array_push($get_fields, $f);
		}
		$get_fields_c = implode(',', $get_fields);
		$this->db->select('id,' . $get_fields_c);
		$query = $this->db->get('product_subcategories');
		return $query->result_array();
	}
	

	function get_subcategory_by_parent(){
		$categories = $this->get_categories();
		$subcategories = $this->get_subcategories_assoc();

		foreach ($subcategories as $s){
			$category_name[] =array('sub_category_name' => $s['name'],
									'category_name'=>$this->get_category_name($s['parent']) );
		}
		return $category_name;
	}
	
	function get_category_name($category_id) {
		$this->db->select('name');
		$this->db->where('id', $category_id);
		$query = $this->db->get('product_categories');
		$result = $query->first_row('array');
		return $result['name'];
	}
	
	
	function get_subcategory_name($parent, $role){
        $table = 'product_subcategories';
	    if($role == 'reseller') {
	        $table = 'product_subcategories_reseller';
        }
		$this->db->select('name');
		//$this->db->where('parent', $parent);//2013-10-11
		$this->db->where('id', $parent);
		$query = $this->db->get($table);
		$result = $query->first_row('array');
		return $result['name'];
	}
	
	function get_sub_category_name($id){
		$this->db->select('name');
		$this->db->where('id', $id);
		$query = $this->db->get('product_subcategories');
		$result = $query->first_row('array');
		return $result['name'];
	}
	
	// Returns subcategories within categories
/* 	function get_subcategories_assoc() {
		$categories = $this->get_subcategory_fields();
		$subcategories = array();
		if (!empty($categories)) {
			foreach ($categories as $c) {
				$s = $c['slug'];
				$subs = $this->get_subcategories_from_cat($s);
				$subcategories[$s] = $subs;
			}
		}
		return $subcategories;
	} */

	function get_subcategories_from_cat($cat_slug) {
		$this->db->select('name, slug');
		$this->db->where('parent', $cat_slug);
		$query = $this->db->get('product_subcategories');
		return $query->result_array();
	}

	/* function get_category_name($cat_slug) {
		$this->db->select('name');
		$this->db->where('slug', $cat_slug);
		$query = $this->db->get('product_categories');
		$result = $query->first_row('array');
		return $result['name'];
	} */

	/* function get_subcategory_name($subcat_slug) {
		$this->db->select('name');
		$this->db->where('slug', $subcat_slug);
		$query = $this->db->get('product_subcategories');
		$result = $query->first_row('array');
		return $result['name'];
	} */

	function get_category_data ( $category_id ) {
		$cat_fields = $this->get_category_fields();
		$get_fields = array();
		foreach ($cat_fields as $f=>$n) {
			array_push($get_fields, $f);
		}
		$get_fields_c = implode(',', $get_fields);
		$this->db->select('id,' . $get_fields_c);
		$this->db->where('id', $category_id);
		$query = $this->db->get('product_categories');
		return $query->result_array();
	}

	function get_subcategory_data($subcategory_id){
		$cat_fields = $this->get_subcategory_fields();
		$get_fields = array();
		foreach ($cat_fields as $f=>$n) {
			array_push($get_fields, $f);
		}
		$get_fields_c = implode(',', $get_fields);
		$this->db->where('id', $subcategory_id);
		$query = $this->db->get('product_subcategories');
		return $query->result_array();
	}
	
	function get_category_fields() {
		$category_fields = array (
			'name' => 'Category Name',
			'slug' => 'URL Slug',
			'desc' => 'Category Description',
            'visible' => 'Product visibility'
		);
		return $category_fields;
	}

	function get_children( $category_id ) {


	}

	function get_category_from_product( $product_id ) {


	}
}

?>