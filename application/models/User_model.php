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
	public function createCard($userID,$country = NULL,$province = NULL,$district = null,$address = null,$cardName = null, $website = null, $phone1 = null, $phone2 = null, $phone3 = null, $email = null, $titleID = null,$companyName = null, $name,$fax =null,$socialMedia=null)
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
			$cardID = $this->db->insert_id();
			$dt = array(
				"UserID" => $userID,
				"CardID" => $cardID,
				"My" => TRUE
				);
			if($this->db->insert("UserCard",$dt)){
				if(count($socialMedia)>0){
					foreach ($socialMedia as $key => $value) {
						if($socialID = $this->checkSocialMedia($userID,$socialMedia[$key]["SocialMediaID"],$socialMedia[$key]["URL"])){
							$dt = array(
								"CardID" => $cardID,
								"UserSocialID" => $socialID
								);
							if(!$this->db->insert("CardUserSocials",$dt)){
								break;
							}
						}else{
							if($socialID = $this->insertSocialMedia($userID,$socialMedia[$key]["SocialMediaID"],$socialMedia[$key]["URL"])){
								$dt = array(
									"CardID" => $cardID,
									"UserSocialID" => $socialID
									);
								if(!$this->db->insert("CardUserSocials",$dt)){
									break;
								}
							}else{
								break;
							}
						}
					}
					return $cardID;
				}
			}else{
				return false;
			}
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
		$card = $this->db->get("Cards");
		if($card->num_rows()>0){
			$card = $card->row_array();
			$card["Social"] = $this->getSocialMediaOfCard($card["ID"])->result_array();
			return $card;
		}
	}
	public function getSocialMediaOfCard($cardID)
	{
		/*
		SELECT * FROM `CardUserSocials` 
		LEFT JOIN UserSocial ON UserSocial.ID = CardUserSocials.UserSocialID
		LEFT JOIN SocialMedia ON SocialMedia.ID = UserSocial.SocialID
		WHERE CardID = 11
		*/
		$this->db->select("SocialMedia.Name,SocialMedia.ImgUrl,UserSocial.Url");
		$this->db->where("CardID",$cardID);
		$this->db->join('UserSocial', 'UserSocial.ID = CardUserSocials.UserSocialID', 'left');
		$this->db->join('SocialMedia', 'SocialMedia.ID = UserSocial.SocialID', 'left');
		return $this->db->get("CardUserSocials");
	}
	public function checkSocialMedia($user,$social_media,$link)
	{
		$this->db->where("UserID",$user);
		$this->db->where('SocialID', $social_media);
		$this->db->where('Url', $link);
		$check = $this->db->get("UserSocial");
		if($check->num_rows()>0){
			$check = $check->row_array();
			return $check["ID"];
		}else{
			return false;
		}
	}
	public function insertSocialMedia($user,$social,$link)
	{
		$data = array(
			"userID" => $user,
			"SocialID" => $social,
			"Url" => $link
			);
		if($this->db->insert("UserSocial",$data)){
			return $this->db->insert_id();
		}else{
			return false;
		}
	}
	public function getMyCards($userID)
	{
		$this->db->where('UserID', $userID);
		$this->db->where('My', TRUE);
		$cards = $this->db->get("UserCard");
		//echo $this->db->last_query();
		if($cards->num_rows()>0){
			$cards = $cards->result_array();
			foreach ($cards as $key => $value) {
				$card[$key] = $this->getCard($cards[$key]["CardID"]);
			}
			return $card;
		}else{
			return false;
		}
	}
	public function getMyReceivedCards($userID)
	{
		$this->db->where('UserID', $userID);
		$this->db->where('My', FALSE);
		$cards = $this->db->get("UserCard");
		//echo $this->db->last_query();
		if($cards->num_rows()>0){
			$cards = $cards->result_array();
			foreach ($cards as $key => $value) {
				$card[$key] = $this->getCard($cards[$key]["CardID"]);
			}
			return $card;
		}else{
			return false;
		}
	}
	public function receiveACard($userID,$cardID)
	{
		if(!$this->checkIsTheCardReceived($userID,$cardID)){
			$data = array(
				"CardID" =>$cardID,
				"UserID" => $userID,
				"My" => false
				);
			return $this->db->insert("UserCard",$data);
		}else{
			return false;
		}
	}
	public function checkIsTheCardReceived($userID,$cardID)
	{
		$this->db->where('cardID', $cardID);
		$this->db->where('userID', $userID);
		$check = $this->db->get("UserCard");
		if($check->num_rows()>0){
			return true;
		}else{
			return false;
		}
	}
	public function checkCard($cardID)
	{
		$this->db->where("ID",$cardID);
		$check = $this->db->get("Cards");
		if($check->num_rows()>0){
			return true;
		}else{
			return false;
		}
	}
}

/* End of file User_model.php */
/* Location: ./application/models/User_model.php */ ?>