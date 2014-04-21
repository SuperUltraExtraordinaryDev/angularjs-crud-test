<?php
/**
 * @package     
 * @author      
 * @copyright   Copyright (c) 2014
 * @link
 * @since
 * @version     1.0
 */

class task extends CI_Controller {

    /**
     * Constructor
     */
    public function __construct() {

        parent::__construct();

    }

    /**
     * Index (list)
     * 
     */
    public function index() {

        $this->load->model('task_model');

        $data['response'] = "";

        $this->load->view('task.php', $data);

    }

    public function check_access() {

        $this->load->model('sys_users_model');

        $ret_msg = $this->sys_users_model->validate_access();

        if($ret_msg['result'] == 'OK'){
            return true;
        } else {
            return false;
        }

    }

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