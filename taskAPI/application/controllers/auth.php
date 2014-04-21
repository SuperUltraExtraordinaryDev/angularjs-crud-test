<?php
/**
 * @package     
 * @author      
 * @copyright   Copyright (c) 2014
 * @link
 * @since
 * @version     1.0
 */

class auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');  
    }

    public function index() {
        $this->load->view('login');
    }

    public function register(){

        $this->load->model('sys_users_model');

        $data['response'] = json_encode($this->sys_users_model->register());

        $this->load->view('auth', $data);

    }

    public function validate_credentials(){

        $this->load->model('sys_users_model');
        $query = $this->sys_users_model->validate();
        $this->load->driver('session');

        if($query){
                        
            $data = array (
               'username' => $this->input->post('username'),
               'is_logged_in' => true,
               'userid' => $query[0]['userid']
            );
           
            $this->session->set_userdata($data);

            $token = md5(uniqid() . microtime() . rand());

            $this->sys_users_model->set_token($query[0]['userid'], $token);
            
            $ret_msg['result'] = 'OK';
            $ret_msg['token'] = $token;
            
        } else {
						
            $ret_msg['result'] = 'NOK';

        }

        $data['response'] = json_encode($ret_msg);

        $this->load->view('auth', $data);
        
    }

    public function get_token_status() {
        $this->load->model('sys_users_model');
        $ret_msg = $this->sys_users_model->validate_access();
        $data['response'] = json_encode($ret_msg);
        $this->load->view('auth', $data);
    }


}

?>