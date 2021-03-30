<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

    public function __construct(){
        parent::__construct();

    }

    /**
     * admin 的登入
     * 一定要 session 是 login_user 才可以
     */
	public function index()
	{

	    if($this->session->userdata('admin_login') != ''){
	        redirect('backend/home');
        }

        $result = array(
        );

	    if(!empty($_POST['submit'])){
            $data = $_POST;

            $chk_valid = $this->system_model->login_valid($data['email'], $data['password']);

            if(!empty($chk_valid)){
                $this->session->set_userdata('admin_login', $chk_valid[0]['account']);
                $this->session->set_userdata('admin_id', $chk_valid[0]['id']);

                // $this->system_model->update_log('login');

                redirect('backend');
            } else {
                $result['errMsg'] = '登入發生錯誤，請再重新登入！';
            }
        }

        $this->twig->output('backend/login', $result);

	}

    /**
     * 登出
     */
	public function logout(){

        $this->session->sess_destroy();

        redirect('login');
    }
}
