<?php
class DB{
	
	public $db_host = ARFLOW_DB_HOST;
	public $db_name = ARFLOW_DB_NAME;
	public $db_user = ARFLOW_DB_USER;
	private $db_password = ARFLOW_DB_PASSWORD;
    protected $db;
	
	function __construct($db_host=ARFLOW_DB_HOST,$db_user=ARFLOW_DB_USER,$db_password=ARFLOW_DB_PASSWORD,$db_name=ARFLOW_DB_NAME){
		try{
			$dsn = 'mysql:dbname='.$db_name.';host='.$db_host;
			$pdo = new PDO($dsn, $db_user, $db_password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
			$this->db = $pdo;
			
		}catch( PDOExecption $e ) {
			$errorMsg = "Error!: " . $e->getMessage() . "</br>";			
		}
	}	
	/* insertQuery()
	* Function for insert data in database.
	* Query
	* Return insert_id.
	*/
	public function insertQuery($query,$param){
		
		if(!$query || !$param){
			return false;
		}
		try{
			$pdo = $this->db;
			$sql = $pdo->prepare($query);	
			try{		
				$sql->execute($param);				
				$pdo->beginTransaction();
				$pdo->commit();
				//get last inserted id;
				$stmt = $pdo->query("SELECT LAST_INSERT_ID()");
				$lastId = $stmt->fetch(PDO::FETCH_NUM);
				return $lastId = $lastId[0];
				
			}catch(PDOExecption $e){
				
				$pdo->rollback();
				$error = "Insert Query Error: ".$e->getMessage()."\n";
				
				return $error;
			}
		}catch( PDOExecption $e ) {
	
			$pdo->rollback();
			$error = "Insert Query Error: ".$e->getMessage()."\n";
			
			return $error;
		}
	}
	/* selectQuery()
	* function for select data from database. 
	* Query,parameter.
	* Return row data.
	*/
	public function selectQuery($query,$param = null){
		//echo $query;
		if(!$query){
			return false;
		}
		try{
			$pdo = $this->db;
			$sql = $pdo->prepare($query);
			try{
				
				$pdo->beginTransaction();
				if($param){
					$sql->execute($param);
				}else{
					$sql->execute();
				}
				$pdo->commit();
				return $sql->fetchall(PDO::FETCH_ASSOC);
				
			}catch(PDOExecption $e){
				
				$pdo->rollback();
				$error = "Select Query Error: ".$e->getMessage()."\n";
				
				return $error;
			}
		}catch( PDOExecption $e ) {
			
			$error = "Select Query Error: ".$e->getMessage()."\n";
			
			return $error;
		}
	} 
	/* updateQuery()
	* function for update data in database.
	* Query
	* Return true.
	*/
	public function updateQuery($query,$param){
		
		if(!$query || !$param){
			return false;
		}
		try{
			$pdo = $this->db;
			$sql = $pdo->prepare($query);
			try{	
				$sql->execute($param);				
				$status  = $sql->rowCount();
				return $status;
			}catch(PDOExecption $e){
				$pdo->rollback();
				$error = "Update Query Error: ".$e->getMessage()."\n";
			
				return $error;	
			}
		}catch( PDOExecption $e ) {
			
			$error = "Update Query Error: ".$e->getMessage()."\n";
		
			return $error;
		}
	}


	/* deleteQuery()
	* function for delete record in database.
	* Query
	* Return true.
	*/
	public function deleteQuery($query,$param){
		if(!$query || !$param){
			// echo "data new";
			return false;
		}else{
			// echo "data";
			$status = self::updateQuery($query,$param);
			return $status;
		}
	}	
	
	/* 
	* get user meta
	*/
	
	public function get_user_meta($userID,$metaName){
		if(!$metaName || !$userID  || empty($metaName) || empty($userID)){
			return false;
		}else{
			$query = "SELECT * FROM user_meta WHERE user_id=:user_id AND user_meta_key=:user_meta_key LIMIT 1";
			$param =array('user_id'=>$userID,'user_meta_key'=>$metaName);
			$res = self::selectQuery($query,$param);
			return $res;
		}
	}
	
	public function get_user_meta_valuse($userID,$metaName){
		if(!$metaName || !$userID  || empty($metaName) || empty($userID)){
			return false;
		}else{
			$query = "SELECT * FROM user_meta WHERE user_id=:user_id AND user_meta_key=:user_meta_key LIMIT 1";
			$param =array('user_id'=>$userID,'user_meta_key'=>$metaName);
			$res = self::selectQuery($query,$param);
			if(!empty($res[0]['user_meta_value'])){
				return $res[0]['user_meta_value'];				
			}else{				
				return false;
			}
		}
	}
	
	
	/* update_user_meta()
	* update option in databse and if not available record then add new record.
	* option_name , option_value
	* Return true.
	*/
	public function update_user_meta($userID,$metaName,$metaValue){
		if(!$metaName || !$userID || empty($metaName) || empty($userID) ){
			return false;
		}else{
			$query = "SELECT * FROM user_meta WHERE user_id=:user_id AND user_meta_key=:user_meta_key LIMIT 1";
			$param =array('user_id'=>$userID,'user_meta_key'=>$metaName);
			$res = self::selectQuery($query,$param);
		
			if(!empty($res)){
				$query = "UPDATE user_meta SET user_meta_value =:user_meta_value WHERE user_id=:user_id AND user_meta_key=:user_meta_key";
				$param =array('user_meta_value'=>$metaValue,'user_id'=>$userID,'user_meta_key'=>$metaName);
				$res = self::updateQuery($query,$param); 
				return $res;
			}else{
				$query = "INSERT INTO user_meta (user_id,user_meta_key,user_meta_value) VALUES (:user_id,:user_meta_key,:user_meta_value)";
				$param = array('user_id'=>$userID,'user_meta_key'=>$metaName,'user_meta_value'=>$metaValue);
				$res = self::insertQuery($query,$param);
				return $res;
			}
		}
	}
	
	/* send_email()
	* Mailing function for sending mail
	* templatename , sender email address , receiver email address , data to send
	* Return true.
	*/
	public function send_email($template_name, $to, $subject, $template_data)
	{
		// Get template data from provided template
		$template_file_full_path = KMT_EMAIL_TEMPLATE_PATH.$template_name;		
		if (file_exists($template_file_full_path)) 
		{
			 $html_content = file_get_contents($template_file_full_path);			 
		}
		else
		{
			return false; // Template Not available
		}
		
		foreach($template_data as $key => $data){
			$html_content = str_replace("{#$". $key ."$#}", $data, $html_content);
		}
		
		// To send HTML mail, the Content-type header must be set
		$headers  = "From: ".ARFLOW_EMAIL_TEMPLATE_PATH. " \r\n";
		$headers .= 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

		$messages = $html_content;		

		// Ready to send email
		if(mail($to,$subject,$messages,$headers)){			
			return true;
		}else{			
			return false;
		}
	}
	
	//function for country list
	public function countryList(){
		$countries = array(
			'1'=>'Alabama',
			'2'=>'Alaska',
			'3'=>'Arizona',
			'4'=>'Arkansas',
			'5'=>'California',
			'6'=>'Colorado',
			'7'=>'Connecticut',
			'8'=>'Delaware',
			'9'=>'Florida',
			'10'=>'Georgia',
			'11'=>'Hawaii',
			'12'=>'Idaho',
			'13'=>'Illinois',
			'14'=>'Indiana',
			'15'=>'Iowa',
			'16'=>'Kansas',
			'17'=>'Kentucky',
			'18'=>'Louisiana',
			'19'=>'Maine',
			'20'=>'Maryland',
			'21'=>'Massachusetts',
			'22'=>'Michigan',
			'23'=>'Minnesota',
			'24'=>'Mississippi',
			'25'=>'Missouri',
			'26'=>'Montana',
			'27'=>'Nebraska',
			'28'=>'Nevada',
			'29'=>'New Hampshire',
			'30'=>'New Jersey',
			'31'=>'New Mexico',
			'32'=>'New York',
			'33'=>'North Carolina',
			'34'=>'North Dakota',
			'35'=>'Ohio',
			'36'=>'Oklahoma',
			'37'=>'Oregon',
			'38'=>'Pennsylvania',
			'39'=>'Rhode Island',
			'40'=>'South Carolina',
			'41'=>'South Dakota',
			'42'=>'Tennessee',
			'43'=>'Texas',
			'44'=>'Utah',
			'45'=>'Vermont',
			'46'=>'Virginia',
			'47'=>'Washington',
			'48'=>'West Virginia',
			'49'=>'Wisconsin',
			'50'=>'Wyoming'
		);
		return $countries;
	}
	
	
	///{#035}
	//function for Ethencity list
	public function ethnicities(){
		$ethnicities = array(
			'Caucasian'=>'Caucasian',
			'African_American'=>'African American',
			'Latino/Hispanic'=>'Latino/Hispanic',
			'Asian'=>'Asian',
			'South_Asian'=>'South Asian',
			'Native_American'=>'Native American',
			'Middle_Eastern'=>'Middle Eastern',
			'Southeast_Asian_Pacific_Islander'=>'Southeast Asian / Pacific Islander',
			'Ethnically_Ambiguous_Mixed Race'=>'Ethnically Ambiguous / Mixed Race',
			'African_Descent'=>'African Descent'
		);
		return $ethnicities;
	}
	
	//function for Body_type list
	public function bodytype(){
		$bodytype = array(
			'Average'=>'Average',
			'Slim'=>'Slim',
			'Athletic_Toned'=>'Athletic / Toned',
			'Muscular'=>'Muscular',
			'Curvy'=>'Curvy',
			'Heavyset_Stocky'=>'Heavyset / Stocky',
			'Plus-Sized_Full-Figured'=>'Plus-Sized / Full-Figured'
		);
		return $bodytype;
	}
	
	//function for hair list
	public function hair(){
		$hair = array(
			'Black'=>'Black',
			'Brown'=>'Brown',
			'Blond'=>'Blond',
			'Auburn'=>'Auburn',
			'Chestnut'=>'Chestnut',
			'Red'=>'Red',
			'Gray'=>'Gray',
			'White'=>'White',
			'Bald'=>'Bald'
		);
		return $hair;
	}
	
	//function for eyes list
	public function eyes(){
		$eyes = array(
			'Amber'=>'Amber',
			'Blue'=>'Blue',
			'Brown'=>'Brown',
			'Gray'=>'Gray',
			'Green'=>'Green',
			'Hazel'=>'Hazel',
			'Red'=>'Red',
			'Violet'=>'Violet'
		);
		return $eyes;
	}
	
	public function get_option($optionName){
		if(!$optionName  ||empty($optionName)){
			return false;
		}else{
			$query = "SELECT * FROM options WHERE option_name=:option_name LIMIT 1";
			$param =array('option_name'=>$optionName);
			$res = self::selectQuery($query,$param);
			return $res;
		}
	}
	///{#035}
	
	public function update_option($optionName,$optionValue){
		if(!$optionName || !$optionName ||empty($optionName)){
			return false;
		}else{
			$query = "SELECT * FROM options WHERE option_name=:option_name LIMIT 1";
			$param =array('option_name'=>$optionName);
			$res = self::selectQuery($query,$param);
		
			if(!empty($res)){
				$query = "UPDATE options SET option_value =:option_value where option_name =:option_name";
				$param =array('option_value'=>$optionValue,'option_name'=>$optionName);
				$res = self::updateQuery($query,$param); 
				return $res;
			}else{
				$query = "INSERT INTO options (option_name,option_value) VALUES (:option_name,:option_value)";
				$param =array('option_name'=>$optionName,'option_value'=>$optionValue);
				$res = self::insertQuery($query,$param);
				return $res;
			}
		}
	} 
	function __destruct(){
		$this->db = null;
	} 
	public  function MakeThumb($src, $dest, $desired_width,$desired_height){
	
		
		/* read the source image */
		$source_image = imagecreatefromjpeg($src);
		
		$width = imagesx($source_image);
		$height = imagesy($source_image);
		
		/* find the "desired height" of this thumbnail, relative to the desired width  */
		//$desired_height = floor($height * ($desired_width / $width));
		
		/* create a new, "virtual" image */
		$virtual_image = imagecreatetruecolor($desired_width, $desired_height);
		
		/* copy source image at a resized size */
		imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
		
		/* create the physical thumbnail image to its destination */
		$imagejpg = imagejpeg($virtual_image, $dest);
		return $imagejpg;
		
		
    }
	
/* public	function randomPassword($length,$count, $characters) {
 
// $length - the length of the generated password
// $count - number of passwords to be generated
// $characters - types of characters to be used in the password
 
// define variables used within the function    
    $symbols = array();
    $passwords = array();
    $used_symbols = '';
    $pass = '';
 
// an array of different character types    
    $symbols["lower_case"] = 'abcdefghijklmnopqrstuvwxyz';
    $symbols["upper_case"] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $symbols["numbers"] = '1234567890';
    $symbols["special_symbols"] = '!?~@#-_+<>[]{}';
 
    $characters = split(",",$characters); // get characters types to be used for the passsword
    foreach ($characters as $key=>$value) {
        $used_symbols .= $symbols[$value]; // build a string with all characters
    }
    $symbols_length = strlen($used_symbols) - 1; //strlen starts from 0 so to get number of characters deduct 1
     
    for ($p = 0; $p < $count; $p++) {
        $pass = '';
        for ($i = 0; $i < $length; $i++) {
            $n = rand(0, $symbols_length); // get a random character from the string with all characters
            $pass .= $used_symbols[$n]; // add the character to the password string
        }
        $passwords[] = $pass;
    }
     
    return $passwords; // return the generated password
} */
	
   public	function randomPassword($length,$count, $characters) {
 
// $length - the length of the generated password
// $count - number of passwords to be generated
// $characters - types of characters to be used in the password
 
// define variables used within the function    
    $symbols = array();
    $passwords = array();
    $used_symbols = '';
    $pass = '';
 
// an array of different character types    
    $symbols["lower_case"] = 'abcdefghijklmnopqrstuvwxyz';
    $symbols["upper_case"] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $symbols["numbers"] = '1234567890';
    $symbols["special_symbols"] = '!?~@#-_+<>[]{}';
 
    $characters = split(",",$characters); // get characters types to be used for the passsword
    foreach ($characters as $key=>$value) {
        $used_symbols .= $symbols[$value]; // build a string with all characters
    }
    $symbols_length = strlen($used_symbols) - 1; //strlen starts from 0 so to get number of characters deduct 1
     
    for ($p = 0; $p < $count; $p++) {
        $pass = '';
        for ($i = 0; $i < $length; $i++) {
            $n = rand(0, $symbols_length); // get a random character from the string with all characters
            $pass .= $used_symbols[$n]; // add the character to the password string
        }
        $passwords[] = $pass;
    }
     
    return $passwords; // return the generated password
}

	
}	



?>