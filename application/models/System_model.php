<?php

class System_model extends CI_Model {

    public $title;
    public $content;
    public $date;

    public function __construct()
    {
        //parent::__construct();
    }


    public function login_valid(){

        $input = $_POST;

        $data = $this->db->from('users')->where('email', $input['email'])->get();

        $data = $data->result_array();
        
        if(count($data) > 0){
            if($this->encryption->decrypt($data[0]['password']) == $input['password']){
                return $data;
            }
        }

        return '';
    }

}