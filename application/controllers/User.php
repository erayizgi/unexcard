<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include(APPPATH."/third_party/phpqrcode/qrlib.php");
class User extends CI_Controller {
	/*
	$key API Key for devices
	*/
	private $qrLib;
	private $key;
	public function __construct()
	{
		parent::__construct();
		$this->qrLib = new QRcode();
		$this->load->model('User_model');
	}
	/*
	checks if the post exist
	*/
	private function checkPost(){
		if($this->input->post()){
			return true;
		}else{
			return false;
		}
	}
	/*
	function for user registiration purposes
	*/
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
						"message" => "Post edilmedi"
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
	public function getTitles()
	{
		if($this->checkPost()){
			$this->key = $this->input->post("key");
			if($this->User_model->checkKey($this->key)){
				$output = array(
					"result" => true,
					"titles" => $this->User_model->getTitles()
					);
			}else{
				$output = array(
					"result" => false,
					"error" => array(
						"type" => "Invalid_key",
						"message" => "Invalid API Key"
						)
					);				
			}
		}else{
			$output = array(
				"result" => false,
				"error" => array(
					"type" => "Invalid_key",
					"message" => "Invalid API Key"
					)
				);			
		}
		$this->output->set_content_type("application/json")->set_output(json_encode($output));
	}
	public function getSocialMedia()
	{
		if($this->checkPost()){
			$this->key = $this->input->post("key");
			if($this->User_model->checkKey($this->key)){
				$output = array(
					"result" => true,
					"socialMedia" => $this->User_model->getSocialMedia()
					);
			}else{
				$output = array(
					"result" => false,
					"error" => array(
						"type" => "Invalid_key",
						"message" => "Invalid API Key"
						)
					);				
			}
		}else{
			$output = array(
				"result" => false,
				"error" => array(
					"type" => "Invalid_key",
					"message" => "Invalid API Key"
					)
				);			
		}
		$this->output->set_content_type("application/json")->set_output(json_encode($output));
		
	}
	public function createCard()
	{
		if($this->checkPost()){
			$this->key = $this->input->post("key");
			if ($this->User_model->checkKey($this->key)) {
				if($user = $this->User_model->loginCheck($this->input->post("sessionKey",TRUE))){
					$user = $user[0];
					$userID = $user["ID"];
					if($this->input->post("is_address")){
						if($this->input->post("Country") && $this->input->post("Province") && $this->input->post("District") && $this->input->post("Address")){
							$country = $this->input->post("Country",TRUE);
							$province = $this->input->post("Province",TRUE);
							$district = $this->input->post("District",TRUE);
							$address = $this->input->post("Address");
						}else{
							$output = array(
								"result" => false,
								"error" => array(
									"type" => "address_info_null",
									"message" => "Address Information is empty."
									)
								);
						}

					}
					if($this->input->post("social_media")){
						$socMedia = $this->input->post("social_media");
					}
						$cardName = $this->input->post("cardName",TRUE);
						$fax = $this->input->post("fax",TRUE);
						$webSite = $this->input->post("webSite",TRUE);
						$phone1 = $this->input->post("phone1",TRUE);
						$phone2 = $this->input->post("phone2",TRUE);
						$phone3 = $this->input->post("phone3",TRUE);
						$email = $this->input->post("email",TRUE);
						$titleID = $this->input->post("title",TRUE);
						$companyName = $this->input->post("companyName",TRUE);
						$name = $this->input->post("name",TRUE);
						if($cardID = $this->User_model->createCard($userID,$country,$province,$district,$address,$cardName, $webSite, $phone1, $phone2, $phone3, $email, $titleID,$companyName, $name,$fax,$socMedia)){
							$this->qrLib->png(base_url()."index.php/user/getCard/".$cardID, "cardImages/".$cardID.".png", "L", "10", 2);
							$output = array(
								"result" => true,
								"cardID" => $cardID,
								"qrLink" =>"cardImages/".$cardID.".png"
								);
						}else{
							$output = array(
								"result" => false,
								"error" =>array(
									"type" => "card_save_error",
									"message" => "There's been an error while saving card"
									)
								);
						}
				}else{
					$output = array(
						"result" => false,
						"error" => array(
							"type" => "session_not_valid",
							"message" => "Invalid session key"
							)
						);					
				}
			} else {
				$output = array(
					"result" => false,
					"error" => array(
						"type" => "Invalid_key",
						"message" => "Invalid API Key"
						)
					);
			}
			

		}else{
			$output = array(
				"result" => false,
				"error" => array(
					"type" => "Invalid_key",
					"message" => "Invalid API Key"
					)
				);
		}
		$this->output->set_content_type("application/json")->set_output(json_encode($output));
	}
	public function getCard($cardID)
	{
		//if ($this->checkPost()) {
		//	$this->key = $this->input->post("key");
			// if ($this->User_model->checkKey($key)) {
				if($card = $this->User_model->getCard($cardID)){
					$output = array(
						"result" => true,
						"card" => $card
						);
				}else{
					$output = array(
						"result" => false,
						"error" => array(
							"type" => "invalid_card",
							"message" => "Invalid Card ID"
							)
						);
				}
			// } else {
			// 	$output = array(
			// 		"result" => false,
			// 		"error" => array(
			// 			"type" => "Invalid_key",
			// 			"message" => "Invalid API Key"
			// 			)
			// 		);
			// }
			
		//} else {
		// 	$output = array(
		// 		"result" => false,
		// 		"error" => array(
		// 			"type" => "Invalid_key",
		// 			"message" => "Invalid API Key"
		// 			)
		// 		);
		// }
		$this->output->set_content_type("application/json")->set_output(json_encode($output));
	}
	public function getMyCards()
	{
		if($this->checkPost()){
			$this->key = $this->input->post("key");
			if($this->User_model->checkKey($this->key)){
				$sessionKey = $this->input->post("sessionKey",TRUE);
				if($user = $this->User_model->loginCheck($sessionKey)){
					$user = $user[0];
					$userID = $user["ID"];
					if($cards = $this->User_model->getMyCards($userID)){
						$output = array(
							"result" => true,
							"cards" => $cards
							);
					}else{
						$output = array(
							"result" => false,
							"error" => array(
								"type" => "no_cards",
								"message" => "There is no card belongs to given user"
								)
							);
					}
				}else{
					$output = array(
						"result" => false,
						"error" => array(
							"type" => "session_not_valid",
							"message" => "Invalid Session Key"
							)
						);
				}
			}else{
				$output = array(
					"result" => false,
					"error" => array(
						"type" => "Invalid_key",
						"message" => "Invalid API Key"
						)
					);
			}
		}else{
			$output = array(
				"result" => false,
				"error" => array(
					"type" => "Invalid_key",
					"message" => "Invalid API Key"
					)
				);			
		}
		$this->output->set_content_type('application/json')->set_output(json_encode($output));
	}
	public function getMyReceivedCards()
	{
		if($this->checkPost()){
			$this->key = $this->input->post("key");
			if($this->User_model->checkKey($this->key)){
				$sessionKey = $this->input->post("sessionKey",TRUE);
				if($user = $this->User_model->loginCheck($sessionKey)){
					$user = $user[0];
					$userID = $user["ID"];
					if($cards = $this->User_model->getMyReceivedCards($userID)){
						$output = array(
							"result" => true,
							"cards" => $cards
							);
					}else{
						$output = array(
							"result" => false,
							"error" => array(
								"type" => "no_cards_received",
								"message" => "There is no card received from given user"
								)
							);
					}
				}else{
					$output = array(
						"result" => false,
						"error" => array(
							"type" => "session_not_valid",
							"message" => "Invalid Session Key"
							)
						);
				}
			}else{
				$output = array(
					"result" => false,
					"error" => array(
						"type" => "Invalid_key",
						"message" => "Invalid API Key"
						)
					);
			}
		}else{
			$output = array(
				"result" => false,
				"error" => array(
					"type" => "Invalid_key",
					"message" => "Invalid API Key"
					)
				);			
		}
		$this->output->set_content_type('application/json')->set_output(json_encode($output));
		
	}
	public function receiveACard()
	{
		if ($this->checkPost()) {
			$this->key = $this->input->post("sessionKey",TRUE);
			if ($user = $this->User_model->loginCheck($this->key)) {
				$user = $user[0];
				$userID = $user["ID"];
				if ($this->input->post("receiving_card_id",TRUE)) {
					$receivingCard = $this->input->post("receiving_card_id",TRUE);
					if ($this->User_model->checkCard($receivingCard)) {
						if($this->User_model->receiveACard($userID,$receivingCard)){
							$output = array(
								"result" => true,
								"receiving_user" => $userID,
								"received_card" => $receivingCard
								);
						}else{
							$output = array(
								"result" => true,
								"error" => array(
									"type" => "card_already_received",
									"message" => "This card received by this user already"
									)
								);
						}
					} else {
						$output = array(
							"result" => false,
							"error" => array(
								"type" => "receiving_card_id_not_valid",
								"message" => "Receiving card id is not valid"
								)
							);
					}
					
				} else {
					$output = array(
						"result" => false,
						"error" => array(
							"type" => "receiving_card_id_null",
							"message" => "Receiving card id can't be null"
							)
						);
				}
				
			} else {
				$output = array(
					"result" => false,
					"error" => array(
						"type" => "session_not_valid",
						"message" => "Invalid Session Key"
						)
					);					
			}
			
		} else {
			$output = array(
				"result" => false,
				"error" => array(
					"type" => "Invalid_key",
					"message" => "Invalid API Key"
					)
				);	
		}
		$this->output->set_content_type('application/json')->set_output(json_encode($output));
	}
}

/* End of file Index.php */
/* Location: ./application/controllers/Index.php */ ?>