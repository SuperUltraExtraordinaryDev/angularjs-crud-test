<?php

/**
 * System Users
 * Model
 *
 * @package     SBASE
 * @author      Marcel Soliman
 * @copyright   Copyright (c) 2011 Marcel Soliman
 * @link
 * @since
 * @filesource
 * @version     26122011 
 *
 */
class Sys_users_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    /**
     * Register new user
     *
     */
    public function register() {
        
        $error = 0;

        // Validate
        $validate = 'username,password';
        $validate_fields = explode(",", $validate);

        foreach ($_GET as $var => $value) {
            if ($var != "validate" and $var != "mode") {
                if (in_array($var, $validate_fields)) {
                    if ($value) {
                        $vars[$var] = $value;
                    } else {
                        $error++;
                    }
                } else {
                    $vars[$var] = $value;
                }
            }
        }

        if ($error == 0) {
            
            // MD5 Password
            if($vars['password']){
                $vars['password'] = md5($vars['password']);
            }

            if($this->db->insert('sys_users', $vars)){
                $ret_msg['result'] = 'OK'; 
            } else {
                $ret_msg['result'] = 'NOK';
            }                
            
        } else {
            $ret_msg['result'] = 'NOK';
        }

        return $ret_msg;

    }

    /**
     * Validate login
     * 
     */
    public function validate() {

        foreach (json_decode(file_get_contents("php://input")) as $var => $value) {
            $vars[$var] = $value;
        }

        $this->db->where('username', $vars['username']);
        $this->db->where('password', md5($vars['password']));

        $query = $this->db->get('sys_users');
		
        return $query->result_array();
        
    }

    public function validate_access() {

        foreach (json_decode(file_get_contents("php://input")) as $var => $value) {
            $vars[$var] = $value;
        }

        $this->load->driver('session');

        $is_logged_in = $this->session->userdata('is_logged_in');
        
        if (!isset($is_logged_in) || $is_logged_in != true) {

            $ret_msg['result'] = 'NOK_NO_SESSION';
            $this->session->sess_destroy();
            
        } else {

            echo $this->input->post('token');
            
            if($vars['token'] == $this->sys_users_model->get_token($this->session->userdata('userid'))[0]['token']){
                $ret_msg['result'] = 'OK';
            } else {
                $ret_msg['result'] = 'NOK_INVALID_TOKEN';
                $this->session->sess_destroy();
            }

        }

        return $ret_msg;

    }


    /**
    * Set Token
    */
    public function set_token($userid, $token){
        $this->db->where('userid', $userid);
        $this->db->update('sys_users', array('token' => $token));
    }

    /**
    * Get Toke
    */
    public function get_token($userid){
        $this->db->select('token');
        $this->db->from('sys_users');
        $this->db->where('userid', $userid);
        $this->db->limit(0, 1);
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get records
     * @return Array
     */
    public function get_records() {
        $query = $this->db->get('sys_users');
        return $query->result_array();
    }

    /**
     * Get records
     * @return Array
     */
    public function get_records_jqGrid($limit = 30, $page = 0, $order_by = 'userid', $dir = 'asc', $searchField = "", $searchString = "") {

        $this->db->select('*');
        $this->db->from('sys_users');


        if ($searchField != "" and $searchString != "") {
            $this->db->like($searchField, $searchString);
        }

        $this->db->order_by($order_by, $dir);
        $this->db->limit($limit, $page);

        $query = $this->db->get();

        

        return $query->result_array();
    }

    /**
     * Get record
     * @param String $id
     * @return Array
     */
    public function get_record($id) {
        $query = $this->db->get_where('sys_users', array('userid' => $id));
        return $query->row_array();
    }

    /**
     * Save
     * @return String updated/inserted
     */
    public function save() {
               
        // $this->output->enable_profiler(true);
        // print_r($_GET);
        
        $error = 0;

        // Validate
        $validate = $_GET['validate'];
        $validate_fields = explode(",", $validate);

        foreach ($_GET as $var => $value) {
            if ($var != "validate" and $var != "mode") {
                if (in_array($var, $validate_fields)) {
                    if ($value) {
                        $vars[$var] = $value;
                    } else {
                        $error++;
                    }
                } else {
                    $vars[$var] = $value;
                }
            }
        }

        if ($error == 0) {

            // Modified update/insert version!
            
            // MD5 Password
            if($vars['password']){
                $vars['password'] = md5($vars['password']);
            }

            // $this->db->where('userid', $vars['userid']);
            // $query = $this->db->get('sys_users');

            if ($_GET['mode'] == "update") {
                $this->db->where('userid', $vars['userid']);
                $this->db->update('sys_users', $vars);
            } else {
                $this->db->insert('sys_users', $vars);
            }
            
            if(!$this->db->_error_message()){
                return "<div class=\"alert alert-success\">Record saved</div>";
            } else {
                return "<div class=\"alert alert-error\">" . $this->db->_error_message() . " <br/>QUERY: " . $this->db->last_query() . "</div>";
            }                        
            
            
        } else {
            return "error";
        }
    }
    
    /**
     * Change password
     * @return String updated/inserted
     */
    public function change_password($id) {              
        
        $error = 0;
        
        // id must be
        if(!$id){
            $error++;
            $error_msg = "System error.";
        }

        // Validate
        $validate = $_GET['validate'];
        $validate_fields = explode(",", $validate);

        foreach ($_GET as $var => $value) {
            if ($var != "validate" and $var != "mode") {
                if (in_array($var, $validate_fields)) {
                    if ($value) {
                        $vars[$var] = $value;
                        $var_count++;
                    } else {
                        $error++;
                        $error_msg = "System error.";
                    }
                } else {
                    $vars[$var] = $value;
                }
            }
        }
        
        // Must be 2 variables
        if($var_count <> 2){
            $error++;
            $error_msg = "System error.";
        }
        
        // Check passwords
        if($vars['password'] != $vars['password_repeat']){
            $error++;
            $error_msg = "Password mismatch!";
        } else {
            unset($vars['password_repeat']);
        }

        if ($error == 0) {            
            
            // MD5 Password            
            $vars['password'] = md5($vars['password']);

            $this->db->where('userid', $id);
            $this->db->update('sys_users', $vars);

            
            if(!$this->db->_error_message()){
                return "<div class=\"alert alert-success\">Password changed!</div>";
            } else {
                return "<div class=\"alert alert-error\">System error! " . $this->db->_error_message() . " <br/>QUERY: " . $this->db->last_query() . "</div>";
            }                        
            
            
        } else {
                return "<div class=\"alert alert-error\">Error ($error) $error_msg</div>";
        }
        
    }    

    /**
     * Delete
     * @param string $id 
     * 
     */
    public function delete($id) {

        $this->db->delete('sys_users', array('userid' => $id));
    }

}

?>