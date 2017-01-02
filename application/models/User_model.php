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
			$this->db->where("ID",$user->ID);
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
	public function createCard($userID,$country = NULL,$province = NULL,$district = null,$address = null,$cardName = null, $website = null, $phone1 = null, $phone2 = null, $phone3 = null, $email = null, $titleID = null,$companyName = null, $name,$fax =null)
	{
		if($country != NULL && $province != NULL && $district != NULL && $address != NULL){
			$this->insertAddress($country,$province,$district,$address);
			$addressID = $this->db->insert_id();
		}
		$data = array(
			"Name" => $name,
			"AddressID" => $addressID,
			"CompanyName" => $companyName,
			"CardName" => $cardName,
			"Fax" => $fax,
			"Www" => $website,
			"Phone1" => $phone1,
			"Phone2" => $phone2,
			"Phone3" => $phone3,
			"Email" => $email,
			"TitleID" => $titleID
			);
		if($this->db->insert("Cards",$data)){
			return $this->db->insert_id();
		}else{
			return false;
		}
	}
	public function insertAddress($country,$province,$district,$address)
	{
		$data = array(
			"Country" => $country,
			"Province" => $province,
			"District" => $district,
			"Address" => $address
			);
		return $this->db->insert("Addresses",$data);
	}
	public function getTitles()
	{
		return $this->db->get("Titles")->result_array();
	}
	public function getSocialMedia()
	{
		return $this->db->get("SocialMedia")->result_array();
	}
	public function getCard($cardID)
	{
		//SELECT * FROM Cards LEFT JOIN Addresses ON Addresses.ID = Cards.AddressID
		$this->db->join("Addresses","Addresses.ID = Cards.AddressID");
		$this->db->where("Cards.ID",$cardID);
		return $this->db->get("Cards");
	}
}

/* End of file User_model.php */
/* Location: ./application/models/User_model.php */ ?>