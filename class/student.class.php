<?php
class student {

	public $id;
	public $first_name;
	public $last_name;
	public $name;
	public $username;
	public $password;
	public $email;
	public $skype;
	public $free_classes = 0;
	public $classes;
	public $ips = [];
	public $blocks = [];
	public $ban;

	function __construct($id, $new_teacher_info = array()){
		global $db, $date_format;
		if(!$id) $id = add_student($new_teacher_info);
		$info = $db->query("SELECT * FROM student WHERE id = {$id}")->fetch(PDO::FETCH_ASSOC);
		foreach($info as $k => $v) $this->$k = $v;
		$this->name = $this->first_name . " " . $this->last_name;

		foreach($db->query("SELECT * FROM class WHERE student_id = {$id}") as $c){
			$c['partner'] = get_teacher_name($c['teacher_id']);
			if($c['free'] == 1) $this->free_classes++;
			$c['formatted_date'] = date($date_format,$c['start_time'] / 1000);
			$this->classes[] = $c;
		}
		foreach($db->query("SELECT * FROM ip WHERE student_id = {$id}") as $ip){
			$this->ips[] = $ip['address'];
		}
		foreach($db->query("SELECT * FROM block WHERE student_id = {$id}") as $block){
			$this->blocks[] = $block['teacher_id'];
		}
	}

	public function has_ip($ip){
		foreach($this->ips as $i) {
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
