<?php
/**
 * @package     
 * @author      
 * @copyright   Copyright (c) 2014
 * @link
 * @since
 * @version     
 */

class auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');  
    }


    /**
    * Index
    */
    public function index(){
        echo 'Please use login page.';
    }

    /**
    * ** to be implemented **
    * Register new user
    *
    * @param $_POST  
    * @return void
    */
    public function register(){
        $this->load->model('sys_users_model');
        $data['response'] = json_encode($this->sys_users_model->register());
        $this->load->view('auth', $data);
    }
    
    /**
    * Validate credentials on login
    *
    * @param $_POST  
    * @return void
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
               'is_logged_in' => true,
               'userid' => $validated[0]['userid'],
               'username' => $validated[0]['username'],
               'firstname' => $validated[0]['firstname'],
               'lastname' => $validated[0]['lastname']
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
            $ret_msg['username'] = $validated[0]['username'];
            
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


}

?>