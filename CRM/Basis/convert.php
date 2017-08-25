<?php

class MijnmediweDecrypt 
{
	private $key;
	
	function __construct()
	{
		$this->key = '2016%sechour2016';
	}
	
	private function mysql_aes_key($key) 
	{
		$new_key = str_repeat(chr(0), 16);
		
		for ($i=0,$len=strlen($key);$i<$len;$i++) {
			$new_key[$i%16] = $new_key[$i%16] ^ $key[$i];
		}
		
		return $new_key;
	}
	
	private function aes_encrypt($val)
	{
		$key = $this->mysql_aes_key($this->key);
		$pad_value = 16-(strlen($val) % 16);
		$val = str_pad($val, (16*(floor(strlen($val) / 16)+1)), chr($pad_value));
		return "[e]" . base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $val, MCRYPT_MODE_ECB, mcrypt_create_iv( mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB), MCRYPT_DEV_URANDOM)));
	}
	
	private function aes_decrypt($val)
	{
		$val = base64_decode(substr($val, 3, 9999));
		$key = $this->mysql_aes_key($this->key);
		$val = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $val, MCRYPT_MODE_ECB, mcrypt_create_iv( mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB), MCRYPT_DEV_URANDOM));
	
		return $val; // rtrim($val, "..16");
		
	}
	
	public function _mysql_decrypt($fieldname) 
	{
		
		return " LEFT(CONVERT( AES_DECRYPT(FROM_BASE64(SUBSTRING($fieldname, 4, 9999)), '". $this->key."') USING latin1) , 
						INSTR( AES_DECRYPT(FROM_BASE64(SUBSTRING($fieldname, 4, 9999)), '". $this->key."')  , ',') -1 )";
	}
	
		
	public function _encrypt($post)
	{
		//return $post;
		
		if (isset($post['name']) && substr($post['name'], 0, 3) != "[e]" && strlen($post['name']) > 0) {
			$new_name = $post['name'] . "," . strlen($post['name']) . ",";
			$new_name =  $this->aes_encrypt($new_name);
			if (strlen($new_name) <= 200) {
				if (strlen($post['name']) > 0) {
					$post['name'] = $new_name;
				}
			}
		}
	
		
		if (isset($post['street']) && substr($post['street'], 0, 3) != "[e]" && strlen($post['street']) > 0) {
			$new_street = $post['street'] . "," . strlen($post['street']) . ",";
			$new_street =  $this->aes_encrypt($new_street);
			if (strlen($new_street) <= 200) {
				if (strlen($post['street']) > 0) {
					$post['street'] =  $new_street;
				}				
			}
		}
		
		
		if (isset($post['address']) && substr($post['address'], 0, 3) != "[e]" && strlen($post['address']) > 0) {
			$new_address = $post['address'] . "," . strlen($post['address']) . ",";
			$new_address =  $this->aes_encrypt($new_address);
			if (strlen($new_address) <= 200) {
				if (strlen($post['address']) > 0) {
					$post['address'] = $new_address;
				}				
			}
		}
		if (isset($post['zip']) && substr($post['zip'], 0, 3) != "[e]" && strlen($post['zip']) > 0) {
			$new_zip = $post['zip'] . "," . strlen($post['zip']) . ",";
			$new_zip = $this->aes_encrypt($new_zip);
			if (strlen($new_zip) <= 100) {
				if (strlen($post['zip']) > 0) {
					$post['zip'] = $new_zip;
				}				
			}
		}
		if (isset($post['city']) && substr($post['city'], 0, 3) != "[e]" && strlen($post['city']) > 0) {
			$new_city = $post['city'] . "," . strlen($post['city']) . ",";
			$new_city = $this->aes_encrypt($new_city);
			if (strlen($new_city) <= 100) {
				if (strlen($post['city']) > 0) {
					$post['city'] = $new_city;
				}
			}
		}
		if (isset($post['phone']) && substr($post['phone'], 0, 3) != "[e]" && strlen($post['phone']) > 0) {
			$new_phone = $post['phone'] . "," . strlen($post['phone']) . ",";
			$new_phone = $this->aes_encrypt($new_phone);
			if (strlen($new_phone) <= 100) {
				if (strlen($post['phone']) > 0) {
					$post['phone'] = $new_phone;
				}
			}
		}
		if (isset($post['mobile']) && substr($post['mobile'], 0, 3) != "[e]" && strlen($post['mobile']) > 0) {
			$new_mobile = $post['mobile'] . "," . strlen($post['mobile']) . ",";
			$new_mobile = $this->aes_encrypt($new_mobile);
			if (strlen($new_mobile) <= 100) {
				if (strlen($post['mobile']) > 0) {
					$post['mobile'] = $new_mobile;
				}
			}
		}
		if (isset($post['address_residence']) && substr($post['address_residence'], 0, 3) != "[e]" && strlen($post['address_residence']) > 0) {
			$new_address_residence = $post['address_residence'] . "," . strlen($post['address_residence']) . ",";
			$new_address_residence = $this->aes_encrypt($new_address_residence);
			if (strlen($new_address_residence) <= 200) {
				if (strlen($post['address_residence']) > 0) {
					$post['address_residence'] = $new_address_residence;
				}
			}
		}
		if (isset($post['street_residence']) && substr($post['street_residence'], 0, 3) != "[e]" && strlen($post['street_residence']) > 0) {
			$new_street_residence = $post['street_residence'] . "," . strlen($post['street_residence']) . ",";
			$new_street_residence = $this->aes_encrypt($new_street_residence);
			if (strlen($new_street_residence) <= 200) {
				if (strlen($post['street_residence']) > 0) {
					$post['street_residence'] = $new_street_residence;
				}
			}
		}
		if (isset($post['zip_residence']) && substr($post['zip_residence'], 0, 3) != "[e]" && strlen($post['zip_residence']) > 0) {
			$new_zip_residence = $post['zip_residence'] . "," . strlen($post['zip_residence']) . ",";
			$new_zip_residence = $this->aes_encrypt($new_zip_residence);
			if (strlen($new_zip_residence) <= 100) {
				if (strlen($post['zip_residence']) > 0) {
					$post['zip_residence'] = $new_zip_residence;
				}
			}
		}
		if (isset($post['city_residence']) && substr($post['city_residence'], 0, 3) != "[e]" && strlen($post['city_residence']) > 0) {
			$new_city_residence = $post['city_residence'] . "," . strlen($post['city_residence']) . ",";
			$new_city_residence = $this->aes_encrypt($new_city_residence);
			if (strlen($new_city_residence) <= 100) {
				$post['city_residence'] = $new_city_residence;
			}
		}
		
		if (isset($post['nbr_personnel']) && substr($post['nbr_personnel'], 0, 3) != "[e]" && strlen($post['nbr_personnel']) > 0) {
			$new_nbr_personnel = $post['nbr_personnel'] . "," . strlen($post['nbr_personnel']) . ",";
			$new_nbr_personnel = $this->aes_encrypt($new_nbr_personnel);
			if (strlen($new_nbr_personnel) <= 100) {
				if (strlen($post['nbr_personnel']) > 0) {
					$post['nbr_personnel'] = $new_nbr_personnel;
				}
			}
		}
		if (isset($post['rsz_nbr']) && substr($post['rsz_nbr'], 0, 3) != "[e]" && strlen($post['rsz_nbr']) > 0) {
			$new_rsz_nbr = $post['rsz_nbr'] . "," . strlen($post['rsz_nbr']) . ",";
			$new_rsz_nbr = $this->aes_encrypt($new_rsz_nbr);
			if (strlen($new_rsz_nbr) <= 100) {
				if (strlen($post['rsz_nbr']) > 0) {
					$post['rsz_nbr'] = $new_rsz_nbr;
				}
			}
		}		
		if (isset($post['RSZ_nummer']) && substr($post['RSZ_nummer'], 0, 3) != "[e]" && strlen($post['RSZ_nummer']) > 0) {
			$new_RSZ_nummer = $post['RSZ_nummer'] . "," . strlen($post['RSZ_nummer']) . ",";
			$new_RSZ_nummer = $this->aes_encrypt($new_RSZ_nummer);
			if (strlen($new_RSZ_nummer) <= 100) {
				if (strlen($post['RSZ_nummer']) > 0) {
					$post['RSZ_nummer'] = $new_RSZ_nummer;
				}
			}
		}
		
						
		return $post;
	}
	
	
	public function _decrypt($line)
    {
				
		if (isset($line->name) && $line->name !== NULL && substr($line->name, 0, 3) == "[e]") {
				$myarray = explode(",", $this->aes_decrypt($line->name));
				$line->name = substr($myarray[0], 0, 0 + $myarray[1]);				
		}
		
		if (isset($line->Achternaam) && $line->Achternaam !== NULL && substr($line->Achternaam, 0, 3) == "[e]") {
				$myarray = explode(",", $this->aes_decrypt($line->Achternaam)); 
				$line->Achternaam = substr($myarray[0], 0, 0 + $myarray[1]);				
		}
		if (isset($line->address) && $line->address !== NULL && substr($line->address, 0, 3) == "[e]") {
				$myarray = explode(",", $this->aes_decrypt($line->address));
				$line->address = substr($myarray[0], 0, 0 + $myarray[1]);			
		}
		if (isset($line->Straatnaam) && $line->Straatnaam !== NULL && substr($line->Straatnaam, 0, 3) == "[e]") {
				$myarray = explode(",", $this->aes_decrypt($line->Straatnaam));
				$line->Straatnaam = substr($myarray[0], 0, 0 + $myarray[1]);			
		}
		if (isset($line->street) && $line->street !== NULL && substr($line->street, 0, 3) == "[e]") {
				$myarray = explode(",", $this->aes_decrypt($line->street));
				$line->street = substr($myarray[0], 0, 0 + $myarray[1]);			
		}	
		if (isset($line->zip) && $line->zip !== NULL && substr($line->zip, 0, 3) == "[e]") {
				$myarray = explode(",", $this->aes_decrypt($line->zip));
				$line->zip = substr($myarray[0], 0, 0 + $myarray[1]);				
		}
		if (isset($line->Postcode) && $line->Postcode !== NULL && substr($line->Postcode, 0, 3) == "[e]") {
				$myarray = explode(",", $this->aes_decrypt($line->Postcode));
				$line->Postcode = substr($myarray[0], 0, 0 + $myarray[1]);				
		}
		if (isset($line->city) && $line->city !== NULL && substr($line->city, 0, 3) == "[e]") {
				$myarray = explode(",", $this->aes_decrypt($line->city));
				$line->city = substr($myarray[0], 0, 0 + $myarray[1]);				
		}
		if (isset($line->Plaats) && $line->Plaats !== NULL && substr($line->Plaats, 0, 3) == "[e]") {
				$myarray = explode(",", $this->aes_decrypt($line->Plaats));
				$line->Plaats = substr($myarray[0], 0, 0 + $myarray[1]);				
		}
		if (isset($line->phone) && $line->phone !== NULL && substr($line->phone, 0, 3) == "[e]") {
				$myarray = explode(",", $this->aes_decrypt($line->phone));
				$line->phone = substr($myarray[0], 0, 0 + $myarray[1]);				
		}
		if (isset($line->mobile) && $line->mobile !== NULL && substr($line->mobile, 0, 3) == "[e]") {
				$myarray = explode(",", $this->aes_decrypt($line->mobile));
				$line->mobile = substr($myarray[0], 0, 0 + $myarray[1]);				
		}
		if (isset($line->address_residence) && $line->address_residence !== NULL && substr($line->address_residence, 0, 3) == "[e]") {
				$myarray = explode(",", $this->aes_decrypt($line->address_residence));
				$line->address_residence = substr($myarray[0], 0, 0 + $myarray[1]);			
		}
		if (isset($line->street_residence) && $line->street_residence !== NULL && substr($line->street_residence, 0, 3) == "[e]") {
				$myarray = explode(",", $this->aes_decrypt($line->street_residence));
				$line->street_residence = substr($myarray[0], 0, 0 + $myarray[1]);			
		}
		if (isset($line->zip_residence) && $line->zip_residence !== NULL && substr($line->zip_residence, 0, 3) == "[e]") {
				$myarray = explode(",", $this->aes_decrypt($line->zip_residence));
				$line->zip_residence = substr($myarray[0], 0, 0 + $myarray[1]);				
		}
		if (isset($line->city_residence) && $line->city_residence !== NULL && substr($line->city_residence, 0, 3) == "[e]") {
				$myarray = explode(",", $this->aes_decrypt($line->city_residence));
				$line->city_residence = substr($myarray[0], 0, 0 + $myarray[1]);				
		}
		if (isset($line->RSZ_nummer) && $line->RSZ_nummer !== NULL && substr($line->RSZ_nummer, 0, 3) == "[e]") {
				$myarray = explode(",", $this->aes_decrypt($line->RSZ_nummer));
				$line->RSZ_nummer = substr($myarray[0], 0, 0 + $myarray[1]);				
		}
		if (isset($line->rsz_nbr) && $line->rsz_nbr !== NULL && substr($line->rsz_nbr, 0, 3) == "[e]") {
				$myarray = explode(",", $this->aes_decrypt($line->rsz_nbr));
				$line->rsz_nbr = substr($myarray[0], 0, 0 + $myarray[1]);				
		}
		if (isset($line->nbr_personnel) && $line->nbr_personnel !== NULL && substr($line->nbr_personnel, 0, 3) == "[e]") {
				$myarray = explode(",", $this->aes_decrypt($line->nbr_personnel));
				$line->nbr_personnel = substr($myarray[0], 0, 0 + $myarray[1]);				
		}
				
		// doctorlist
		if (isset($line->zip_region) && $line->zip_region !== NULL && substr($line->zip_region, 0, 3) == "[e]") {
				$myarray = explode(",", $this->aes_decrypt($line->zip_region));
				$line->zip_region = substr($myarray[0], 0, 0 + $myarray[1]);				
		}		
		if (isset($line->city_region) && $line->city_region !== NULL && substr($line->city_region, 0, 3) == "[e]") {
				$myarray = explode(",", $this->aes_decrypt($line->city_region));
				$line->city_region = substr($myarray[0], 0, 0 + $myarray[1]);				
		}
		// end doctorlist	
		
		return $line;
		
	}

	public function _equal($argument1, $argument2)
	{
		$where = " ( " 
					. $argument1 . " = '" . $argument2 . "' 
					
					OR " 
					
					. $this->_mysql_decrypt($argument1) . " = '" . $argument2 . "' ) ";

		
		return $where;
	}
	
	public function _like($argument1, $argument2)
	{
		$where = "( " . $argument1 . " LIKE '%" . $argument2 . "%' 
		 
					OR "
						. $this->_mysql_decrypt($argument1) . " LIKE '%" . $argument2 . "%' 
					) "; 
		
		return $where;
	}
	
	public function _equalNbr($argument1, $argument2) 
	{

		$where = "( CONVERT(" . $argument1 . ", UNSIGNED) = CONVERT('" . $argument2 . "', UNSIGNED) 
					OR 
					CONVERT(  " . $this->_mysql_decrypt($argument1) . "
							, UNSIGNED
							) = CONVERT('" . $argument2 . "', UNSIGNED) ) "; 
		
		return $where;		
	}

	public function _equalTrimmed($argument1, $argument2) 
	{

		$where = "( REPLACE(" . $argument1 . ", ' ', '') = REPLACE('" . $argument2 . "', ' ', '') 
						OR 
					REPLACE ( " . $this->_mysql_decrypt($argument1) . ",  ' ', '') = REPLACE('" . $argument2 . "' , ' ', '') ) "; 
		
		return $where;		
	}	

	public function _likeTrimmed($argument1, $argument2) 
	{

		$where = "( REPLACE(" . $argument1 . ", ' ', '') LIKE REPLACE('%" . $argument2 . "%', ' ', '') 
						OR 
					REPLACE ( " . $this->_mysql_decrypt($argument1) . ",  ' ', '') LIKE REPLACE('%" . $argument2 . "%' , ' ', '') ) "; 
		
		return $where;		
	}	
	
	public function _orderby($argument1) 
	{
		$where =  $this->_mysql_decrypt($argument1) ; 
		
		return $where;			
	}
			
}
