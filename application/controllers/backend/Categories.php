<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Categories extends CI_Controller {

    public function __construct(){
        parent::__construct();

    }

	public function index()
	{

        try{
			$crud = new grocery_CRUD();

			$crud->set_theme('tablestrap4');
			$crud->set_table('categories');
			$crud->set_subject('分類管理');
			$crud->required_fields('name', 'order');
            
            $crud->display_as('name','類別名稱');
			$crud->display_as('order','順序');
			$crud->display_as('slug','Slug');
			
            $crud->unset_clone();
            $crud->unset_fields('created_at', 'updated_at');
			
			$crud->columns('name', 'order');
            
			$output = $crud->render();

            $this->twig->output('backend/categories/browse', (array)$output);

		}catch(Exception $e){
			show_error($e->getMessage().' --- '.$e->getTraceAsString());
		}    

	}

	
	public function update($id){

		$input = $this->input->post();
				
		$data = [			
			'name'        => $input['name'],
			'order'       => $input['order'],
			'slug'         => $input['slug'],
		];

		$this->db->where('category_id', $id);
		$this->db->update('categories', $data);

		redirect('/backend/categories');

	}

	public function insert(){

		$input = $this->input->post();
				
		$data = [			
			'name'        => $input['name'],
			'order'       => $input['order'],
			'slug'         => $input['slug'],
		];


		$this->db->insert('categories', $data);

		redirect('/backend/categories');

	}
}
