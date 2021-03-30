<?php

class ManageAuth
{
    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    /**     * 权限认证     */
    public function auth()
    {
        $this->CI->load->helper('url');
        if (preg_match("/backend.*/i", uri_string())) {
            // 需要进行权限检查的URL
            $this->CI->load->library('session');
            if( !$this->CI->session->userdata('admin_login') ) {
            // 用户未登陆
                redirect('login');
                return;
            }
        }
    }
}