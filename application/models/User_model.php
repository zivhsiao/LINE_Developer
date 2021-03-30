<?php

class User_model extends CI_Model {


    public function __construct()
    {
        // parent::__construct();
    }

    
    /**
     * get_user
     *
     * @return void
     */
    public function get_user(){

        $input = $_POST;

        $data = $this->db->from('users')->get();

        $data = $data->result_array();
        
        return json_encode($data);
    }
    
    
}