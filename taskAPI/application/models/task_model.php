<?php
/**
 * @package     
 * @author      
 * @copyright   Copyright (c)
 * @link
 * @since
 * @version     GENERATOR_DATE
 */
class task_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function getTasks($taskId) {

        $this->db->select('*');
        $this->db->from('tasks');
        
        if($taskId > 0){
            $this->db->where('taskId', $taskId);
        }

        $this->db->order_by('taskId', 'desc');
        #$this->db->limit($limit, $page);

        $query = $this->db->get();

        return $query->result_array();
    }

    public function add(){

        // Init
        $error = 0;
        $err_msg = ""; 

        // Fields to be validated
        $validate = "task,status";
        $validate_fields = explode(",", $validate);

        // Foreach var in json stream
        foreach (json_decode(file_get_contents("php://input")) as $var => $value) {

            // If var not .. and  ..
            if ($var != "validate" and $var != "mode") {

                // If var in array validate_fields
                if (in_array($var, $validate_fields)) {

                    // If value, then add to $vars, else error
                    if ($value) {
                        $vars[$var] = $value;    

                    // value == null, error...
                    } else { 

                        // Raise errorlevel and add to err_msg
                        $error++;
                        $err_msg .= $var . " missing  ";
                    }

                // Not to be validated
                } else {

                    // Add to vars
                    $vars[$var] = $value;
                }

            }

        }

        // If no errors
        if($error == 0){

            // Insert into table
            $this->db->insert('tasks', $vars);  

            // Get ID
            $taskId = $this->db->insert_id();

            // Select record from table
            $this->db->select('taskId,task,status,created_at');
            $this->db->from('tasks');
            $this->db->where('taskId', $taskId);
            $query = $this->db->get();
            
            // Return json
            return json_encode($query->result_array());

        // Errors.. echo err_msg's
        } else {
            echo $err_msg;
        }

    }

    public function update(){

        // Init
        $error = 0;
        $err_msg = ""; 

        // Fields to be validated
        $validate = "task,status";
        $validate_fields = explode(",", $validate);

        // Foreach var in json stream
        foreach (json_decode(file_get_contents("php://input")) as $var => $value) {

            // If var not .. and  ..
            if ($var != "validate" and $var != "mode") {

                // If var in array validate_fields
                if (in_array($var, $validate_fields)) {

                    // If value, then add to $vars, else error
                    if ($value) {
                        $vars[$var] = $value;    

                    // value == null, error...
                    } else { 

                        // Raise errorlevel and add to err_msg
                        $error++;
                        $err_msg .= $var . " missing  ";
                    }

                // Not to be validated
                } else {

                    // Add to vars
                    $vars[$var] = $value;
                }

            }

        }

        // If no errors
        if($error == 0){

            // Update record
            $this->db->where('taskId', $vars['taskId']);
            $this->db->update('tasks', $vars);

        // Errors.. echo err_msg's
        } else {

            return $err_msg;
        
        }

    }

    public function delete() {
        
        $postdata = json_decode(file_get_contents("php://input"));

        $this->db->delete('tasks', array('taskId' => $postdata->taskId));
        
        $data = 'Deleted.';

        return $data;

    }

}

?>