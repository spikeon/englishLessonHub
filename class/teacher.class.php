<?php
	class teacher {

		public $id;
		public $first_name;
		public $last_name;
		public $name;
		public $username;
		public $password;
		public $email;
		public $skype;
		public $paypal;
		public $freeclass;
		public $description;
		public $desc;
		public $method;
		public $country;
		public $photo;
		public $thumb;

		public $stars;

		public $price;
		public $price1;
		public $price2;

		public $payment;
		public $duration;

		public $rating;
		public $rating_count;

		public $availability;

		public $classes;

		public $ips = [];
		public $blocks = [];

		public $ban;

		function __construct($id, $new_teacher_info = array()){
			global $db, $billing_info;
			if(!$id && !empty($new_teacher_info)) $id = add_teacher($new_teacher_info);
			else if (!$id) return false;
			$info = $db->query("SELECT * FROM teacher WHERE id = {$id}")->fetch(PDO::FETCH_ASSOC);
			//var_dump($info);
			foreach($info as $k => $v) $this->$k = $v;

			$this->name = $this->first_name . " " . $this->last_name;

			$this->rating = get_rating($id);
			$this->rating_count = get_rating_count($id);

			$this->desc = nl2br(substr(strip_tags($this->description), 0, 250));

			$this->thumb = "phpThumb/".phpThumbURL("src=".ELH_DIR_URL."/uploads/{$this->photo}&w=150&h=190&zc=y");

			$this->stars = get_stars($this->id);
			$this->availability = [];
			$this->classes = [];
			$avails = [];
			foreach($db->query("SELECT * FROM availability WHERE teacher_id = {$id}") as $avail){
				$avails[] = $avail;
			}

			$this->availability = array_sort($avails, 'day');

			foreach($db->query("SELECT * FROM class WHERE teacher_id = {$id}") as $c){

				$c['partner'] = get_student_name($c['student_id']);

				$this->classes[] = $c;
			}

			$this->payment = $this->price;
			$this->price = round($this->payment + ($this->payment * ($billing_info['percent'] / 100)) + $billing_info['add'],2, PHP_ROUND_HALF_DOWN);
			list($this->price1, $this->price2) = explode('.', (string) $this->price);
			//$this->price1 = floor($this->price);
			//$this->price2 = floor(($this->price - $this->price1) * 100);

			foreach($db->query("SELECT * FROM ip WHERE teacher_id = {$id}") as $ip){
				$this->ips[] = $ip['address'];
			}

			foreach($db->query("SELECT * FROM block WHERE teacher_id = {$id}") as $block){
				$this->blocks[] = $block['student_id'];
			}


		}

		public function has_ip($ip){
			foreach((array)$this->ips as $i) {
				if($ip == $i) return true;
			}
			return false;
		}

		public function has_block($id){
			foreach($this->blocks as $i) {
				if($id == $i) return true;
			}
			return false;
		}


	}
