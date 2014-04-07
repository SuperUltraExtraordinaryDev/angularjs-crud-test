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

    public function get($taskId=0){

        $this->load->model('task_model');

        $data['response'] = json_encode($this->task_model->getTasks($taskId));

        $this->load->view('task', $data);

    }

    public function add(){

        $this->load->model('task_model');
        
        $data['response'] = $this->task_model->add();

        $this->load->view('task', $data);

    }

    public function delete(){

        $this->load->model('task_model');

        $data['response'] = $this->task_model->delete();

        $this->load->view('task', $data);

    }

    public function update(){

        $this->load->model('task_model');

        $data['response'] = $this->task_model->update();

        $this->load->view('task', $data);
        

    }


}

?>