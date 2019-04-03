<?php

require 'dbmodel.php';
class TaskModel extends dbModel {

	static $_table = 'tasks';
	static $pageCount = 0;
	protected $maxPage   = 0;

	protected $page_size = 0;


	public function __construct(){

		parent::__construct();

		$conf = new Config();		
		$this->page_size = $conf->get('app')['page_size'];		
	}

	public function getByPage( $page, $orderBy=''){
		self::$pageCount = (int)$this->count();

		$this->maxPage =  ceil( self::$pageCount / $this->page_size );

		if ((int)$page > $this->maxPage) $page = $this->maxPage;
		$top = $this->page_size * ($page - 1);
		return $this->getList($top, $this->page_size, $orderBy);
	}

	public function getPageList() {
		for($i=1; $i <= $this->maxPage; $i++){
			$pages[$i] = $i;
		}
		return $pages;
	}

public function update(array $data) {
			$this->init();

			if (!$this->db) {
				die('PDO object is not created');
			}

		$sql  = 'UPDATE ' . static::$_table . ' SET ';	
		$sql .= " description=:description,";
		$sql .= " status=:status WHERE";
		$sql .= " id=:id";


		$stm = $this->db->prepare($sql);
		$stm->execute($data);
	}

public function checkIn(array $data) {
	$ret = [];
	if (empty( $_POST['username'])) $ret[] = 'username';
	if (empty( $_POST['description'])) $ret[] = 'description';	
	if (strpos($_POST['email'],'@' ) === false ) $ret[] = 'email';

	if (count($ret) == 0) return false;

	$_SESSION['username'] = $_POST['username'];
	$_SESSION['email'] = $_POST['email'];
	$_SESSION['description'] = $_POST['description'];
	return $ret;
}	

public function insert(array $data) {
			$this->init();

			if (!$this->db) {
				die('PDO object is not created');
			}

		$data['status']=0;	
		$sql  = 'INSERT INTO ' . static::$_table ;
		$sql .= ' (username,description,status,email) ';	
		$sql .= 'VALUES(:username, :description,:status,:email)';
		
		$stm = $this->db->prepare($sql);
		$stm->execute($data);
	}

}