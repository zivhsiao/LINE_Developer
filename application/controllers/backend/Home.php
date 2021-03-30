<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    public function __construct(){
        parent::__construct();

    }

	public function index()
	{


        $result = array(
            'admin_user' => $this->session->userdata('admin_login')
        );

		$this->twig->output('backend/home/index', $result);
	}

}
