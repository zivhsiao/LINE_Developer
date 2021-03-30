<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {

    public function __construct(){
        parent::__construct();

    }

    /**
     * browse
     *
     * @return void
     */
    public function index(){

		try{
			$crud = new grocery_CRUD();

			$crud->set_theme('tablestrap4');
			$crud->set_table('users');
			$crud->set_subject('使用者管理');
			$crud->required_fields('name', 'account', 'password', 'email');
            $crud->display_as('name','使用者名稱');
            $crud->display_as('account','使用者帳號');
            $crud->display_as('password','密碼');
            $crud->display_as('email','E-mail');
            $crud->unset_clone();
            $crud->unset_fields('created_at', 'updated_at', 'phone');

			$crud->columns('name','account');

			$output = $crud->render();

            $this->twig->output('backend/users/browse', (array)$output);

		}catch(Exception $e){
			show_error($e->getMessage().' --- '.$e->getTraceAsString());
		}    
    }

    
    /**
     * delete
     *
     * @param  mixed $id
     * @return void
     */
    public function delete($id){

        $this->db->from('users')->delete($id);
    }
    
    /**
     * update
     *
     * @param  mixed $id
     * @return void
     */
    public function update($id){

        $input = $this->input->post;

    }

}
