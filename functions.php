<?php
	/* Error Reporting */

	define("ELH_DIR_URL", 'http://'. $_SERVER['SERVER_NAME'] . (!stristr($_SERVER['REQUEST_URI'], '.') ? $_SERVER['REQUEST_URI'] : dirname($_SERVER['REQUEST_URI'])));

	/**
	 * Report Error
	 * @param String $str The error message
	 * @return bool
	 */
	function elh_error($str){
		if(ELH_DEBUG) trigger_error($str, E_USER_ERROR);
		else return false;
	}

	/**
	 * Report Warning
	 * @param String $str The error message
	 * @return bool
	 */
	function elh_warning($str){
		if(ELH_DEBUG) trigger_error($str, E_USER_WARNING);
		return false;
	}

	/**
	 * Report Notice
	 * @param String $str The error message
	 * @return bool
	 */
	function elh_notice($str){
		if(ELH_DEBUG) trigger_error($str, E_USER_NOTICE);
		return false;
	}

	/* Teacher Functions */

	/**
	 * Get Teacher Fields
	 * @return array
	 */
	function teacher_fields(){
		return [
			'first_name'	=> 'First Name',
			'last_name' 	=> 'Last Name',
			'username' 		=> 'Username',
			'password' 		=> 'Password',
			'email' 		=> 'Email',
			'skype'			=> 'Skype',
			'description'	=> 'Description',
			'paypal'		=> 'Paypal',
			'method'		=> 'Method',
			'photo'			=> 'Photo',
			'country'		=> 'Country',
			'price'			=> 'Price',
			'duration'		=> 'Duration',
			'freeclass'		=> 'Allow Free Lesson'

		];
	}

	/**
	 * Get Student Fields
	 * @return array
	 */
	function student_fields(){
		return [
			'first_name'	=> 'First Name',
			'last_name' 	=> 'Last Name',
			'username' 		=> 'Username',
			'password' 		=> 'Password',
			'email' 		=> 'Email',
			'skype'			=> 'Skype'
		];
	}


	/**
	 * Check Teacher Field
	 * @param String $field_name The name of the field
	 * @return bool
	 */
	function check_teacher_field($field_name){
		$acceptable = teacher_fields();
		if(array_key_exists($field_name, $acceptable)) return true;
		else return elh_notice("Bad Teacher Field: {$field_name}");
	}

	/**
	 * Check Student Field
	 * @param String $field_name The name of the field
	 * @return bool
	 */
	function check_student_field($field_name){
		$acceptable = student_fields();
		if(array_key_exists($field_name, $acceptable)) return true;
		else return elh_notice("Bad Student Field: {$field_name}");
	}

	/**
	 * Update Teacher
	 * @param int $id The ID for the teacher
	 * @param array $info the data to update
	 * @return bool
	 */
	function update_teacher($id, $info = []){
		global $db;
		if(!$id) return elh_error("Can't update a teacher without ID");
		$fields = [];


		foreach($info as $k => $v){
			if(check_teacher_field($k)) $fields[$k] = $v;
		}
		foreach($fields as $field => $data) {
			if($field == 'password') $data = md5($data);
			$stmt = $db->prepare("UPDATE teacher SET `{$field}` = ? WHERE id = ?");

			$stmt->bindParam(1, $d);
			$stmt->bindParam(2, $i);
			$f = $field;
			$d = $data;
			$i = $id;
			if(!$stmt->execute()) {
				$err = $stmt->errorInfo();
				elh_notice("Failed to update teacher({$id}) field {$field} with data \"{$data}\".  ".$err[2]);
			}
		}
		return true;
	}

	/**
	 * Update Student
	 * @param int $id The ID for the student
	 * @param array $info the data to update
	 * @return bool
	 */
	function update_student($id, $info = []){
		global $db;
		if(!$id) return elh_error("Can't update a student without ID");
		$fields = [];

		foreach($info as $k => $v){
			if(check_student_field($k)) $fields[$k] = $v;
		}
		foreach($fields as $field => $data) {
			if($field == 'password') $data = md5($data);
			$stmt = $db->prepare("UPDATE student SET `{$field}` = ? WHERE id = ?");

			$stmt->bindParam(1, $d);
			$stmt->bindParam(2, $i);


			$d = $data;
			$i = $id;

			if(!$stmt->execute()) {
				$err = $stmt->errorInfo();
				elh_notice("Failed to update student({$id}) field {$field} with data \"{$data}\".  ".$err[2]);
			}
		}
		return true;
	}



	/**
	 * Add Teacher
	 * @param array $info The information to add
	 * @return mixed
	 */
	function add_teacher($info){
		global $db;
		$stmt = $db->prepare("INSERT INTO teacher () VALUES ()");
		if(!$stmt->execute()) elh_error("Failed to create new teacher record");
		$teacher_id = $db->lastInsertId();
		update_teacher($teacher_id, $info);
		return $teacher_id;
	}

	/**
	 * Add Student
	 * @param array $info The information to add
	 * @return mixed
	 */
	function add_student($info){
		global $db;
		$stmt = $db->prepare("INSERT INTO student () VALUES ()");
		if(!$stmt->execute()) elh_error("Failed to create new teacher record");
		$student_id = $db->lastInsertId();
		update_student($student_id, $info);
		return $student_id;
	}


	/**
	 * Get Rating Data
	 * @param int $id Teacher Id
	 * @return array
	 */
	function get_rating_data($id){
		global $db;
		return $db->query("SELECT COUNT(0) as num, ROUND(AVG(rating)) as final FROM rate WHERE teacher_id = {$id}")->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * Get Rating
	 * @param int $id Teacher Id
	 * @return array
	 */
	function get_rating($id){
		$data = get_rating_data($id);
		return $data['final'];
	}

	/**
	 * Get Rating Count
	 * @param int $id Teacher Id
	 * @return array
	 */
	function get_rating_count($id){
		$data = get_rating_data($id);
		return $data['num'];
	}

	/**
	 * Get Stars
	 * @param int $id Teacher Id
	 * @return array
	 */
	function get_stars($id){
		$r = "";
		$rating = get_rating($id);
		for($i=1;$i <= 5; $i++){
			if($i > $rating) $r .= "<span class='glyphicon glyphicon-star-empty' style='color:#ccc;'></span>";
			else $r .= "<span class='glyphicon glyphicon-star' style='color:gold;'></span>";
		}
		return "{$r} (".get_rating_count($id).")";
	}

	/**
	 * Echo stars
	 * @param $id
	 */
	function stars($id){ echo get_stars($id); }

	/**
	 * Get a user class
	 * @param $id
	 * @return teacher
	 */
	function get_teacher($id){
		return new teacher($id);
	}

	/**
	 * Get a user class
	 * @param $id
	 * @return teacher
	 */
	function get_student($id){
		return new student($id);
	}

	function get_teacher_name($id){
		global $db;
		$sql = "SELECT * FROM teacher WHERE id = ?";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(1, $i);
		$i = $id;
		$stmt->execute();
		$row = $stmt->fetchObject();
		return $row->first_name . " " . $row->last_name;
	}

	function get_student_name($id){
		global $db;
		$sql = "SELECT * FROM student WHERE id = ?";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(1, $i);
		$i = $id;
		$stmt->execute();
		$row = $stmt->fetchObject();
		return $row->first_name . " " . $row->last_name;
	}

	function get_teacher_id_by_email($email){
		global $db;
		$sql = "SELECT * FROM teacher WHERE email = ?";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(1, $e);
		$e = $email;
		$stmt->execute();
		if($stmt->rowCount() == 0) return false;
		else if($sth->rowCount() == 1) {
			$row = $stmt->fetchObject();
			return $row->id;
		}
		else return false;
	}

function auth($user, $pass, $remember){
		global $db, $admin_info;
		$pass = md5($pass);

		if($user == $admin_info['username'] && $pass = $admin_info['password']){
			$_SESSION['logged_in'] = true;
			$_SESSION['type'] = 'admin';
			setcookie('user', base64_encode(serialize($_SESSION)), time() + (10 * 365 * 24 * 60 * 60));
			return true;
		}

		$sql = "SELECT id FROM teacher WHERE username = ? AND password = ?";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(1, $user);
		$stmt->bindParam(2, $pass);
		$stmt->execute();
		if($stmt->rowCount() != 0) {
			$obj = $stmt->fetchObject();
			$_SESSION['logged_in'] = true;
			$_SESSION['uid'] = $obj->id;
			$_SESSION['type'] = 'teacher';
			if($remember) setcookie('user', base64_encode(serialize($_SESSION)), time() + (10 * 365 * 24 * 60 * 60));
			$teacher = new teacher($obj->id);
			if(!$teacher->has_ip(get_ip())){
				$sth = $db->prepare("INSERT INTO ip (address, teacher_id) VALUES (?,?)");
				$sth->execute(array(get_ip(), $teacher->id));
			}
			return true;
		}

		$sql = "SELECT id FROM student WHERE username = ? AND password = ?";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(1, $user);
		$stmt->bindParam(2, $pass);
		$stmt->execute();
		if($stmt->rowCount() != 0) {
			$obj = $stmt->fetchObject();
			$_SESSION['logged_in'] = true;
			$_SESSION['uid'] = $obj->id;
			$_SESSION['type'] = 'student';
			if($remember) setcookie('user', base64_encode(serialize($_SESSION)), time() + (10 * 365 * 24 * 60 * 60));
			$student = new student($obj->id);
			if(!$student->has_ip(get_ip())){
				$sth = $db->prepare("INSERT INTO ip ( address, student_id ) VALUES (?,?)");
				$sth->execute(array(get_ip(), $student->id));
			}
			return true;
		}

		return false;
	}

	function listen_for_cookie(){
		if(!empty($_COOKIE['user']) && empty($_SESSION['type'])){
			$tmp = unserialize(base64_decode($_COOKIE['user']));
			foreach((array)$tmp as $k => $v){
				$_SESSION[$k] = $v;
			}
		}
	}

	function logged_in(){
		if(!empty($_SESSION['logged_in'])) return true;
		return false;
	}

	function login_id(){
		if(!logged_in()) return false;
		return $_SESSION['uid'];
	}

	function login_type(){
		if(!logged_in()) return false;
		return $_SESSION['type'];
	}

	function timezone_dropdown($offset){

		$return = "
		<select>
			<option timeZoneId='1' gmtAdjustment='GMT-12:00' useDaylightTime='0' value='-12'>(GMT-12:00) International Date Line West</option>
			<option timeZoneId='2' gmtAdjustment='GMT-11:00' useDaylightTime='0' value='-11'>(GMT-11:00) Midway Island, Samoa</option>
			<option timeZoneId='3' gmtAdjustment='GMT-10:00' useDaylightTime='0' value='-10'>(GMT-10:00) Hawaii</option>
			<option timeZoneId='4' gmtAdjustment='GMT-09:00' useDaylightTime='1' value='-9'>(GMT-09:00) Alaska</option>
			<option timeZoneId='5' gmtAdjustment='GMT-08:00' useDaylightTime='1' value='-8'>(GMT-08:00) Pacific Time (US & Canada)</option>
			<option timeZoneId='6' gmtAdjustment='GMT-08:00' useDaylightTime='1' value='-8'>(GMT-08:00) Tijuana, Baja California</option>
			<option timeZoneId='7' gmtAdjustment='GMT-07:00' useDaylightTime='0' value='-7'>(GMT-07:00) Arizona</option>
			<option timeZoneId='8' gmtAdjustment='GMT-07:00' useDaylightTime='1' value='-7'>(GMT-07:00) Chihuahua, La Paz, Mazatlan</option>
			<option timeZoneId='9' gmtAdjustment='GMT-07:00' useDaylightTime='1' value='-7'>(GMT-07:00) Mountain Time (US & Canada)</option>
			<option timeZoneId='10' gmtAdjustment='GMT-06:00' useDaylightTime='0' value='-6'>(GMT-06:00) Central America</option>
			<option timeZoneId='11' gmtAdjustment='GMT-06:00' useDaylightTime='1' value='-6'>(GMT-06:00) Central Time (US & Canada)</option>
			<option timeZoneId='12' gmtAdjustment='GMT-06:00' useDaylightTime='1' value='-6'>(GMT-06:00) Guadalajara, Mexico City, Monterrey</option>
			<option timeZoneId='13' gmtAdjustment='GMT-06:00' useDaylightTime='0' value='-6'>(GMT-06:00) Saskatchewan</option>
			<option timeZoneId='14' gmtAdjustment='GMT-05:00' useDaylightTime='0' value='-5'>(GMT-05:00) Bogota, Lima, Quito, Rio Branco</option>
			<option timeZoneId='15' gmtAdjustment='GMT-05:00' useDaylightTime='1' value='-5'>(GMT-05:00) Eastern Time (US & Canada)</option>
			<option timeZoneId='16' gmtAdjustment='GMT-05:00' useDaylightTime='1' value='-5'>(GMT-05:00) Indiana (East)</option>
			<option timeZoneId='17' gmtAdjustment='GMT-04:00' useDaylightTime='1' value='-4'>(GMT-04:00) Atlantic Time (Canada)</option>
			<option timeZoneId='18' gmtAdjustment='GMT-04:00' useDaylightTime='0' value='-4'>(GMT-04:00) Caracas, La Paz</option>
			<option timeZoneId='19' gmtAdjustment='GMT-04:00' useDaylightTime='0' value='-4'>(GMT-04:00) Manaus</option>
			<option timeZoneId='20' gmtAdjustment='GMT-04:00' useDaylightTime='1' value='-4'>(GMT-04:00) Santiago</option>
			<option timeZoneId='21' gmtAdjustment='GMT-03:30' useDaylightTime='1' value='-3.5'>(GMT-03:30) Newfoundland</option>
			<option timeZoneId='22' gmtAdjustment='GMT-03:00' useDaylightTime='1' value='-3'>(GMT-03:00) Brasilia</option>
			<option timeZoneId='23' gmtAdjustment='GMT-03:00' useDaylightTime='0' value='-3'>(GMT-03:00) Buenos Aires, Georgetown</option>
			<option timeZoneId='24' gmtAdjustment='GMT-03:00' useDaylightTime='1' value='-3'>(GMT-03:00) Greenland</option>
			<option timeZoneId='25' gmtAdjustment='GMT-03:00' useDaylightTime='1' value='-3'>(GMT-03:00) Montevideo</option>
			<option timeZoneId='26' gmtAdjustment='GMT-02:00' useDaylightTime='1' value='-2'>(GMT-02:00) Mid-Atlantic</option>
			<option timeZoneId='27' gmtAdjustment='GMT-01:00' useDaylightTime='0' value='-1'>(GMT-01:00) Cape Verde Is.</option>
			<option timeZoneId='28' gmtAdjustment='GMT-01:00' useDaylightTime='1' value='-1'>(GMT-01:00) Azores</option>
			<option timeZoneId='29' gmtAdjustment='GMT+00:00' useDaylightTime='0' value='0'>(GMT+00:00) Casablanca, Monrovia, Reykjavik</option>
			<option timeZoneId='30' gmtAdjustment='GMT+00:00' useDaylightTime='1' value='0'>(GMT+00:00) Greenwich Mean Time : Dublin, Edinburgh, Lisbon, London</option>
			<option timeZoneId='31' gmtAdjustment='GMT+01:00' useDaylightTime='1' value='1'>(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna</option>
			<option timeZoneId='32' gmtAdjustment='GMT+01:00' useDaylightTime='1' value='1'>(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague</option>
			<option timeZoneId='33' gmtAdjustment='GMT+01:00' useDaylightTime='1' value='1'>(GMT+01:00) Brussels, Copenhagen, Madrid, Paris</option>
			<option timeZoneId='34' gmtAdjustment='GMT+01:00' useDaylightTime='1' value='1'>(GMT+01:00) Sarajevo, Skopje, Warsaw, Zagreb</option>
			<option timeZoneId='35' gmtAdjustment='GMT+01:00' useDaylightTime='1' value='1'>(GMT+01:00) West Central Africa</option>
			<option timeZoneId='36' gmtAdjustment='GMT+02:00' useDaylightTime='1' value='2'>(GMT+02:00) Amman</option>
			<option timeZoneId='37' gmtAdjustment='GMT+02:00' useDaylightTime='1' value='2'>(GMT+02:00) Athens, Bucharest, Istanbul</option>
			<option timeZoneId='38' gmtAdjustment='GMT+02:00' useDaylightTime='1' value='2'>(GMT+02:00) Beirut</option>
			<option timeZoneId='39' gmtAdjustment='GMT+02:00' useDaylightTime='1' value='2'>(GMT+02:00) Cairo</option>
			<option timeZoneId='40' gmtAdjustment='GMT+02:00' useDaylightTime='0' value='2'>(GMT+02:00) Harare, Pretoria</option>
			<option timeZoneId='41' gmtAdjustment='GMT+02:00' useDaylightTime='1' value='2'>(GMT+02:00) Helsinki, Kyiv, Riga, Sofia, Tallinn, Vilnius</option>
			<option timeZoneId='42' gmtAdjustment='GMT+02:00' useDaylightTime='1' value='2'>(GMT+02:00) Jerusalem</option>
			<option timeZoneId='43' gmtAdjustment='GMT+02:00' useDaylightTime='1' value='2'>(GMT+02:00) Minsk</option>
			<option timeZoneId='44' gmtAdjustment='GMT+02:00' useDaylightTime='1' value='2'>(GMT+02:00) Windhoek</option>
			<option timeZoneId='45' gmtAdjustment='GMT+03:00' useDaylightTime='0' value='3'>(GMT+03:00) Kuwait, Riyadh, Baghdad</option>
			<option timeZoneId='46' gmtAdjustment='GMT+03:00' useDaylightTime='1' value='3'>(GMT+03:00) Moscow, St. Petersburg, Volgograd</option>
			<option timeZoneId='47' gmtAdjustment='GMT+03:00' useDaylightTime='0' value='3'>(GMT+03:00) Nairobi</option>
			<option timeZoneId='48' gmtAdjustment='GMT+03:00' useDaylightTime='0' value='3'>(GMT+03:00) Tbilisi</option>
			<option timeZoneId='49' gmtAdjustment='GMT+03:30' useDaylightTime='1' value='3.5'>(GMT+03:30) Tehran</option>
			<option timeZoneId='50' gmtAdjustment='GMT+04:00' useDaylightTime='0' value='4'>(GMT+04:00) Abu Dhabi, Muscat</option>
			<option timeZoneId='51' gmtAdjustment='GMT+04:00' useDaylightTime='1' value='4'>(GMT+04:00) Baku</option>
			<option timeZoneId='52' gmtAdjustment='GMT+04:00' useDaylightTime='1' value='4'>(GMT+04:00) Yerevan</option>
			<option timeZoneId='53' gmtAdjustment='GMT+04:30' useDaylightTime='0' value='4.5'>(GMT+04:30) Kabul</option>
			<option timeZoneId='54' gmtAdjustment='GMT+05:00' useDaylightTime='1' value='5'>(GMT+05:00) Yekaterinburg</option>
			<option timeZoneId='55' gmtAdjustment='GMT+05:00' useDaylightTime='0' value='5'>(GMT+05:00) Islamabad, Karachi, Tashkent</option>
			<option timeZoneId='56' gmtAdjustment='GMT+05:30' useDaylightTime='0' value='5.5'>(GMT+05:30) Sri Jayawardenapura</option>
			<option timeZoneId='57' gmtAdjustment='GMT+05:30' useDaylightTime='0' value='5.5'>(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi</option>
			<option timeZoneId='58' gmtAdjustment='GMT+05:45' useDaylightTime='0' value='5.75'>(GMT+05:45) Kathmandu</option>
			<option timeZoneId='59' gmtAdjustment='GMT+06:00' useDaylightTime='1' value='6'>(GMT+06:00) Almaty, Novosibirsk</option>
			<option timeZoneId='60' gmtAdjustment='GMT+06:00' useDaylightTime='0' value='6'>(GMT+06:00) Astana, Dhaka</option>
			<option timeZoneId='61' gmtAdjustment='GMT+06:30' useDaylightTime='0' value='6.5'>(GMT+06:30) Yangon (Rangoon)</option>
			<option timeZoneId='62' gmtAdjustment='GMT+07:00' useDaylightTime='0' value='7'>(GMT+07:00) Bangkok, Hanoi, Jakarta</option>
			<option timeZoneId='63' gmtAdjustment='GMT+07:00' useDaylightTime='1' value='7'>(GMT+07:00) Krasnoyarsk</option>
			<option timeZoneId='64' gmtAdjustment='GMT+08:00' useDaylightTime='0' value='8'>(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi</option>
			<option timeZoneId='65' gmtAdjustment='GMT+08:00' useDaylightTime='0' value='8'>(GMT+08:00) Kuala Lumpur, Singapore</option>
			<option timeZoneId='66' gmtAdjustment='GMT+08:00' useDaylightTime='0' value='8'>(GMT+08:00) Irkutsk, Ulaan Bataar</option>
			<option timeZoneId='67' gmtAdjustment='GMT+08:00' useDaylightTime='0' value='8'>(GMT+08:00) Perth</option>
			<option timeZoneId='68' gmtAdjustment='GMT+08:00' useDaylightTime='0' value='8'>(GMT+08:00) Taipei</option>
			<option timeZoneId='69' gmtAdjustment='GMT+09:00' useDaylightTime='0' value='9'>(GMT+09:00) Osaka, Sapporo, Tokyo</option>
			<option timeZoneId='70' gmtAdjustment='GMT+09:00' useDaylightTime='0' value='9'>(GMT+09:00) Seoul</option>
			<option timeZoneId='71' gmtAdjustment='GMT+09:00' useDaylightTime='1' value='9'>(GMT+09:00) Yakutsk</option>
			<option timeZoneId='72' gmtAdjustment='GMT+09:30' useDaylightTime='0' value='9.5'>(GMT+09:30) Adelaide</option>
			<option timeZoneId='73' gmtAdjustment='GMT+09:30' useDaylightTime='0' value='9.5'>(GMT+09:30) Darwin</option>
			<option timeZoneId='74' gmtAdjustment='GMT+10:00' useDaylightTime='0' value='10'>(GMT+10:00) Brisbane</option>
			<option timeZoneId='75' gmtAdjustment='GMT+10:00' useDaylightTime='1' value='10'>(GMT+10:00) Canberra, Melbourne, Sydney</option>
			<option timeZoneId='76' gmtAdjustment='GMT+10:00' useDaylightTime='1' value='10'>(GMT+10:00) Hobart</option>
			<option timeZoneId='77' gmtAdjustment='GMT+10:00' useDaylightTime='0' value='10'>(GMT+10:00) Guam, Port Moresby</option>
			<option timeZoneId='78' gmtAdjustment='GMT+10:00' useDaylightTime='1' value='10'>(GMT+10:00) Vladivostok</option>
			<option timeZoneId='79' gmtAdjustment='GMT+11:00' useDaylightTime='1' value='11'>(GMT+11:00) Magadan, Solomon Is., New Caledonia</option>
			<option timeZoneId='80' gmtAdjustment='GMT+12:00' useDaylightTime='1' value='12'>(GMT+12:00) Auckland, Wellington</option>
			<option timeZoneId='81' gmtAdjustment='GMT+12:00' useDaylightTime='0' value='12'>(GMT+12:00) Fiji, Kamchatka, Marshall Is.</option>
			<option timeZoneId='82' gmtAdjustment='GMT+13:00' useDaylightTime='0' value='13'>(GMT+13:00) Nuku'alofa</option>
		</select>";


	}

function array_sort($array, $on, $order=SORT_ASC)
{
	$new_array = array();
	$sortable_array = array();

	if (count($array) > 0) {
		foreach ($array as $k => $v) {
			if (is_array($v)) {
				foreach ($v as $k2 => $v2) {
					if ($k2 == $on) {
						$sortable_array[$k] = $v2;
					}
				}
			} else {
				$sortable_array[$k] = $v;
			}
		}

		switch ($order) {
			case SORT_ASC:
				asort($sortable_array);
				break;
			case SORT_DESC:
				arsort($sortable_array);
				break;
		}

		foreach ($sortable_array as $k => $v) {
			$new_array[$k] = $array[$k];
		}
	}

	return $new_array;
}

/**
 * Helper method for getting an APIContext for all calls
 * @param string $clientId Client ID
 * @param string $clientSecret Client Secret
 * @param bool $sandbox Use Sandbox?
 * @return PayPal\Rest\ApiContext
 */
function getApiContext($clientId, $clientSecret, $sandbox = true)
{
	$apiContext = new \PayPal\Rest\ApiContext(
		new \PayPal\Auth\OAuthTokenCredential(
			$clientId,
			$clientSecret
		)
	);
	$arr = array(
		'log.LogEnabled' => true,
		'log.FileName' => 'PayPal.log',
		'log.LogLevel' => 'DEBUG',
		'cache.enabled' => true,
	);
	if($sandbox) $arr['mode'] = 'sandbox';
	$apiContext->setConfig( $arr );
	return $apiContext;
}

function check_ip(){
	global $db;
	foreach($db->query("SELECT * FROM ip WHERE teacher_id IN (SELECT id FROM teacher WHERE ban = 1) OR student_id IN (SELECT id FROM student WHERE ban = 1)") as $ip){
		if($ip['address'] == get_ip()) {
			echo "You have been banned.";
			die();
		}
	}
}

function get_ip(){
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}



function send_mail($to, $subject, $body, $attachments = []){

	global $mailConfig;

	$mail = new PHPMailer;

	//$mail->SMTPDebug = 3;                               // Enable verbose debug output

	$mail->isSMTP();                                      // Set mailer to use SMTP
	$mail->Host 		= $mailConfig['server'];  // Specify main and backup SMTP servers
	$mail->SMTPAuth 	= true;                               // Enable SMTP authentication
	$mail->Username 	= $mailConfig['username'];                 // SMTP username
	$mail->Password 	= $mailConfig['password'];                           // SMTP password
	$mail->SMTPSecure 	= $mailConfig['securityType'];                            // Enable TLS encryption, `ssl` also accepted
	$mail->Port 		= $mailConfig['port'];                                    // TCP port to connect to

	$mail->setFrom($mailConfig['from'], 'Mailer');
	$mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient

	foreach((array)$attachments as $attachment) $mail->addAttachment($attachment);

	$mail->isHTML(true);                                  // Set email format to HTML

	$mail->Subject = $subject;
	$mail->Body    = $body;
	$mail->AltBody = strip_tags($body);

	if(!$mail->send()) return false;
	else return true;


}
