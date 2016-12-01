<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {
	private $key;
	public function __construct()
	{
		parent::__construct();
		$this->load->model('User_model');
		
	}
	private function checkPost(){
		if($this->input->post()){
			return true;
		}else{
			return false;
		}
	}
	public function index()
	{
		
	}
	public function register()
	{
		if($this->checkPost()){
			$this->key = $this->input->post("key");
			if($this->User_model->checkKey($this->key)->num_rows()>0){
				if(!$this->User_model->checkUser($this->input->post("mail",TRUE))){
					if($this->User_model->insertUser($this->input->post("mail",TRUE),$this->input->post("password",TRUE))){
						if($user = $this->User_model->login($this->input->post("mail",TRUE),$this->input->post("password",TRUE))){
							$output = array(
								"result" => true,
								"user" => $user[0]
								);
						}else{
							$output = array(
								"result" => false,
								"error" => array(
									"type" => "user_doesnt_exist",
									"message" => "Wrong mail and password"
									)
								);
						}
					}else{
						$output = array(
							"result" => false,
							"error" => array(
								"type" => "insert_fails",
								"message" => "Insert failed check your credentials"
								)
							);
					}
				}else{
					$output = array(
						"result"=>false,
						"error" => array(
							"type" => "user_exists",
							"message" => "We have another user with same email"
							)
						);
				}
			}else{
				$output = array(
					"result"=>false,
					"error" => array(
						"type" => "invalid_key",
						"message" => "Invalid API Key"
						)
					);
			}
		}else{
				$output = array(
					"result"=>false,
					"error" => array(
						"type" => "invalid_key",
						"message" => "Invalid API Key"
						)
					);
		}
		$this->output->set_content_type('application/json')->set_output(json_encode($output));

	}
	public function firstLogin()
	{
		if ($this->checkPost()) {
			$this->key = $this->input->post("key");
			if ($this->User_model->checkKey($this->key)->num_rows()>0) {
				if($user = $this->User_model->login($this->input->post("mail",TRUE),$this->input->post("password",TRUE))){
					$output = array(
						"result" => true,
						"user" => $user[0]
						);
				}else{
					$output = array(
						"result" => false,
						"error" => array(
							"type" => "invalid_user_cred",
							"message" => "Please enter a valid email and password"
							)
						);
				}
			} else {
				$output = array(
					"result"=>false,
					"error" => array(
						"type" => "invalid_key",
						"message" => "Invalid API Key"
						)
					);
			}
			
		} else {
			$output = array(
				"result"=>false,
				"error" => array(
					"type" => "invalid_key",
					"message" => "Invalid API Key"
					)
				);
		}
		$this->output->set_content_type('application/json')->set_output(json_encode($output));
		
	}
	public function loginCheck()
	{
		if ($this->checkPost()) {
			$this->key = $this->input->post("key");
			if($this->User_model->checkKey($this->key)){
				if ($user = $this->User_model->loginCheck($this->input->post("sessionKey",TRUE))) {
					$output = array(
						"result" => true,
						"user" => $user[0]
						);
				} else {
					$output = array(
						"result"=>false,
						"error" => array(
							"type" => "session_not_valid",
							"message" => "Invalid Session Key"
							)
						);
				}
			}else{
				$output = array(
					"result"=>false,
					"error" => array(
						"type" => "invalid_key",
						"message" => "Invalid API Key"
						)
					);				
			}

		} else {
			$output = array(
				"result"=>false,
				"error" => array(
					"type" => "invalid_key",
					"message" => "Invalid API Key"
					)
				);
		}
		$this->output->set_content_type('application/json')->set_output(json_encode($output));
	}
}

/* End of file Index.php */
/* Location: ./application/controllers/Index.php */ ?>