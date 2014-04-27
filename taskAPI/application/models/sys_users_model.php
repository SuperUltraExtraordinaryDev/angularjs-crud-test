<?php
/**
 * @package     
 * @author      
 * @copyright   Copyright (c) 2014 
 * @link
 * @since
 * @version     
 *
 */
class sys_users_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    /**
    * Register user
    *
    * @param $_POST
    * @return string
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
    * Validate 
    *
    * @param $_POST
    * @return array
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

    /*
    * Validate current access
    *
    * --> moved to task controller
    *
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
    */

    /**
    * Set token
    *
    * @param string $userid
    * @param string $token
    * @return void
    */
    public function set_token($userid, $token){
        $this->db->where('userid', $userid);
        $this->db->update('sys_users', array('token' => $token));
    }

    /**
    * Get token
    *
    * @param string $userid
    * @return array
    */
    public function get_token($userid){
        $this->db->select('token');
        $this->db->from('sys_users');
        $this->db->where('userid', $userid);
        $this->db->limit(0, 1);
        $query = $this->db->get();
        return $query->result_array();
    }

    
    /*

    // to be implemented... 
    
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

    // to be implemented... 

    public function delete($id) {
        
        $this->db->delete('sys_users', array('userid' => $id));
    }

    */

}

?>