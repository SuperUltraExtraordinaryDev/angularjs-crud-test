<?php
/**
 * @package     
 * @author      
 * @copyright   Copyright (c) 2014
 * @link
 * @since
 * @version     
 */

class task extends CI_Controller {

    /**
     * Constructor
     *
     */
    public function __construct() {

        parent::__construct();

    }

    /**
    * Validate current access
    *
    * @return Array
    *
    * result 'OK'
    * result 'NOK_INVALID_TOKEN'
    */
    public function validate_access() {

        $this->load->driver('session');

        // Get input
        foreach (json_decode(file_get_contents("php://input")) as $var => $value) {
            $vars[$var] = $value;
        }

        // Session?
        $is_logged_in = $this->session->userdata('is_logged_in');
        
        // If no session, return
        if (!isset($is_logged_in) || $is_logged_in != true) {

            $ret_msg['result'] = 'NOK_NO_SESSION';
            $this->session->sess_destroy();
        
        // Session exists, but do you have a valid token?...
        } else {
            
            // Compare provided token with token in user table
            // In steps because of old PHP version at my provider...
            $session_userid = $this->session->userdata('userid');
            $user_token_info = $this->sys_users_model->get_token($session_userid);
            if($vars['token'] == $user_token_info[0]['token']){
                // Return OK
                $ret_msg['result'] = 'OK';
            } else {
                // Invalid token, return NOK and destroy session
                $ret_msg['result'] = 'NOK_INVALID_TOKEN';
                $this->session->sess_destroy();
            }

        }

        return $ret_msg;

    }

    /**
    * Check access
    * Used in every database request (get, add, delete and update) 
    *
    * @return boolean
    */
    public function check_access() {
        $this->load->model('sys_users_model');
        //$ret_msg = $this->sys_users_model->validate_access();
        $ret_msg = $this->validate_access();
        if($ret_msg['result'] == 'OK'){
            return true;
        } else {
            return false;
        }
    }

    /**
    * Get token status
    * (check provided token against database)
    *
    * @return void
    */
    public function get_token_status() {
        $this->load->model('sys_users_model');
        $ret_msg = $this->validate_access();
        $data['response'] = json_encode($ret_msg);
        $this->load->view('auth', $data);
    }

    /**
    * Get tasks or task
    *
    * @param int taskId     
    * @return void
    */
    public function get($taskId=0){

        $this->load->model('sys_users_model');
        $this->load->model('task_model');

        if($this->check_access()){
            $data['response'] = json_encode($this->task_model->getTasks($taskId), JSON_NUMERIC_CHECK);
        } else {
            $data['response'] = json_encode(array('result' => 'NOK_TOKEN_ERROR'));
        }

        $this->load->view('task', $data);

    }

    /**
    * Add task
    *
    * @param $_POST  
    * @return void
    */
    public function add(){

        $this->load->model('sys_users_model');
        $this->load->model('task_model');
        
        if($this->check_access()){
            $data['response'] = $this->task_model->add();
        } else {
            $data['response'] = json_encode(array('result' => 'NOK_TOKEN_ERROR'));
        } 

        $this->load->view('task', $data);

    }

    /**
    * Delete task
    *
    * @param $_POST  
    * @return void
    */
    public function delete(){

        $this->load->model('sys_users_model');
        $this->load->model('task_model');

        if($this->check_access()){
            $data['response'] = $this->task_model->delete();
        } else {
            $data['response'] = json_encode(array('result' => 'NOK_TOKEN_ERROR'));
        }

        $this->load->view('task', $data);

    }

    /**
    * Update task
    *
    * @param $_POST  
    * @return void
    */
    public function update(){

        $this->load->model('sys_users_model');
        $this->load->model('task_model');

        if($this->check_access()){
            $data['response'] = $this->task_model->update();
        } else {
            $data['response'] = json_encode(array('result' => 'NOK_TOKEN_ERROR'));
        }

        $this->load->view('task', $data);
        
    }


}

?>