<?php
	class course {
		public $id;
		public $teacher_id;
		public $student_id;
		public $status;
		public $paypal;
		public $free;
		public $time;
		public $start_time;
		public $end_time;
		public $formatted_date;
		public $formatted_time;
		public $teacher;
		public $student;
		function __construct($id, $standalone = true){
			global $db, $date_format;
			if (!$id) return false;
			$info = $db->query("SELECT * FROM class WHERE id = {$id}")->fetch(PDO::FETCH_ASSOC);
			foreach($info as $k => $v) $this->$k = $v;
			$this->formatted_date = date( $date_format, $this->start_time / 1000 );
			$this->formatted_time = date( "H:i T", $this->start_time / 1000 );
			if($standalone){
				$this->teacher = new teacher($this->teacher_id);
				$this->student = new student($this->student_id);
			}
		}


	}
