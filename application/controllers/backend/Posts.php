<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Posts extends CI_Controller {

    public function __construct(){
        parent::__construct();

    }

	public function index()
	{

        try{
			$crud = new grocery_CRUD();

			$crud->set_theme('tablestrap4');
			$crud->set_table('posts');
			$crud->set_subject('文章管理');
			$crud->required_fields('category_id', 'title');
            $crud->display_as('category_id', '類別');
            $crud->display_as('title','文章標題');
			$crud->display_as('body','文章內文');
			$crud->display_as('seo_title','SEO 內文');
			$crud->display_as('meta_descr','META 內文');
			$crud->display_as('meta_keyword','META 關鍵字');
			$crud->display_as('featured','精選');
			$crud->display_as('image','圖片');

            $crud->unset_clone();
            $crud->unset_fields('created_at', 'updated_at');
			
			$crud->columns('category_id','title');
            
			$crud->set_relation('category_id', 'categories', 'name');

			$crud->set_field_upload('image','assets/uploads/files');
	
			$output = $crud->render();

            $this->twig->output('backend/posts/browse', (array)$output);

		}catch(Exception $e){
			show_error($e->getMessage().' --- '.$e->getTraceAsString());
		}    

	}

	
	public function update($id){

		$input = $this->input->post();

		$files = $_FILES;

		$config['upload_path'] =  realpath(APPPATH . '../assets/uploads/files/');
		$config['allowed_types'] = 'gif|jpg|jpeg|png';
        $config['max_size'] = 20000;
		$config['file_name'] = $_FILES['s78805a22']['name'];
		$this->load->library('upload', $config);

		$data = [];
		$image_upload = '';
				
		$data = [
			'category_id'  => $input['category_id'],
			'title'        => $input['title'],
			'body'         => $input['body'],
			'intro'        => $input['intro'],
			'slug'         => $input['slug'],
			'meta_descr'   => $input['meta_descr'],
			'meta_keyword' => $input['meta_keyword'],
			'seo_title'    => $input['seo_title'],
			'featured'     => $input['featured']
		];

		if($this->upload->do_upload('s78805a22')){
			$type = $this->upload->data();	
			$image_upload = $type['file_name'];
			$data['image'] = $image_upload;
		}

		$this->db->where('id', $id);
		$this->db->update('posts', $data);

		redirect('/backend/posts');

	}

	public function insert(){

		$input = $this->input->post();

		$files = $_FILES;

		$config['upload_path'] =  realpath(APPPATH . '../assets/uploads/files/');
		$config['allowed_types'] = 'gif|jpg|jpeg|png';
        $config['max_size'] = 20000;
		$config['file_name'] = $_FILES['s78805a22']['name'];
		$this->load->library('upload', $config);

		$data = [];
		$image_upload = '';
				
		$data = [
			'category_id'  => $input['category_id'],
			'title'        => $input['title'],
			'body'         => $input['body'],
			'intro'        => $input['intro'],
			'slug'         => $input['slug'],
			'meta_descr'   => $input['meta_descr'],
			'meta_keyword' => $input['meta_keyword'],
			'seo_title'    => $input['seo_title'],
			'featured'     => $input['featured']
		];

		if($this->upload->do_upload('s78805a22')){
			$type = $this->upload->data();	
			$image_upload = $type['file_name'];
			$data['image'] = $image_upload;
		}

		$this->db->insert('posts', $data);

		redirect('/backend/posts');

	}
}
