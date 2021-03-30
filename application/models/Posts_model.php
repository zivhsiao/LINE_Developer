<?php


class Posts_model extends CI_Model {


    public function __construct()
    {
        // parent::__construct();
    }


    public function get_all_data(){

    
        $data = $this->db->from('posts')->get();
        
        foreach($data->result() as $row){
            $Parsedown = new \Parsedown();

            $body = $Parsedown->text($row->body);

            $row->body = $body;

        }

        $data = $data->result_array();

        return $data;
    }

}