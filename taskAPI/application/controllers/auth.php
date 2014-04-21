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

    /*
     * Register new user 
     * 
     * To be implemented
     *
    public function register(){
        $this->load->model('sys_users_model');
        $data['response'] = json_encode($this->sys_users_model->register());
        $this->load->view('auth', $data);
    }
    */

    /**
    * Validate credentials on login
    *
    */
    public function validate_credentials(){

        // Models, helpers, drivers
        $this->load->model('sys_users_model');
        $this->load->driver('session');

        // Validate
        $validated = $this->sys_users_model->validate();

        // If validated   :-)
        if($validated){
            
            // Compile data for session
            $data = array (
               'username' => $this->input->post('username'),
               'is_logged_in' => true,
               'userid' => $validated[0]['userid']
            );
           
            // Set session data
            $this->session->set_userdata($data);

            // Generate token
            $token = md5(uniqid() . microtime() . rand());

            // Update token in user table
            $this->sys_users_model->set_token($validated[0]['userid'], $token);
            
            // Return OK with token
            $ret_msg['result'] = 'OK';
            $ret_msg['token'] = $token;
            
        // Not validated  :-(
        } else {
						
            // Return NOK, destroy session
            $ret_msg['result'] = 'NOK';
            $this->session->sess_destroy();

        }

        // json_encode response
        $data['response'] = json_encode($ret_msg);

        // load view for output
        $this->load->view('auth', $data);
        
    }

    /*
    * Get token status
    * (check provided token against database)
    *
    */
    public function get_token_status() {
        $this->load->model('sys_users_model');
        $ret_msg = $this->sys_users_model->validate_access();
        $data['response'] = json_encode($ret_msg);
        $this->load->view('auth', $data);
    }


}

?>