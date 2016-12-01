<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}
	public function checkKey($key)
	{
		$this->db->where('apiKey', $key);
		return $this->db->get("ApiKeys");
	}
	public function checkUser($mail)
	{
		$this->db->where('Email', $mail);
		return $this->db->get('Users')->num_rows();
	}
	public function insertUser($mail,$pass)
	{
		$data = array(
			"Email" => $mail,
			"Password" => md5($pass),
			"TypeID" => 1, // TODO This will change when the user types module active
			"Created" => date("Y-m-d H:i:s")
			);
		return $this->db->insert('Users', $data);
	}
	public function login($mail,$pass)
	{
		$this->db->where("Email",$mail);
		$this->db->where("Password",md5($pass));
		$user = $this->db->get('Users');
		if($user->num_rows()>0){
			$user = $user->row();
			$data = array(
				"SessionID" => uniqid(),
				"SessionID_Exp_Date" => date("Y-m-d H:i:s",strtotime("+7 days",strtotime(date("Y-m-d H:i:s"))))
				);
			if($this->db->update("Users",$data)){
				$this->db->where("Email",$mail);
				$this->db->where("Password",md5($pass));
				$user = $this->db->get('Users')->result_array();
				return $user;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	public function loginCheck($sessionKey)
	{
		$this->db->where('SessionID', $sessionKey);
		$user = $this->db->get('Users');
		if($user->num_rows()>0){
			return $user->result_array();
		}else{
			return false;
		}
	}
}

/* End of file User_model.php */
/* Location: ./application/models/User_model.php */ ?>