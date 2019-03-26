<?php
/**
 * класс управление конфигурациями проекта
 *
 * @author akalend
 * @package mvc
 */
/**
 * Упроавление конфигурацией
 *
 */
class Config {
		
	private $db;
	private $mc;
	private $app;

	private $name = null;
	private $data = null;

	/**
	 * 
	 * 
	 *
	 */
	function __construct( $name = null, $section = null ){
		require( 'conf/app.conf.php' );
		if ( is_null( $name ) ) {			
			$this->db  = $db_conf;
			$this->mc  = $mc_conf;			
			$this->app = $app_conf;
			return $this;
		} 
		
		require( 'conf/$name.conf.php'  );
		$this->name = $name;
		$section_name = $name.'_'.$section;
		$this->data = $$section_name;		
		return $this;
	}
	/**
	 * �������� �������� ����������
	 *
	 * @param  string $name - ��� ���������� ���������� 
	 * @return mixed - ���������� ������ ������ ���� �� ���������� 
	 */
	public function get( $name ) {	    
//	    var_dump( $this->name, $this->data );
		if ( !is_null( $this->name )  &&  !is_null( $this->data)) {
//		    echo '----------';
			if ( isset( $this->data['name'] ))
				return $this->data['name'];
		}
		
		if (!isset($this->{$name}) )
			throw new Exception('unknow section in config file'); 
		return $this->{$name};
	}
	/**
	 * �������� �� ������� ������ ���������� 
	 *
	 * @param string $name ��� ������
	 * @return array - ������ ����������
	 */
	public function getSection($name=NULL) {
		if ( $name) {
			$section_name = $this->name.'_'.$name;
			$this->data = $$section_name;			
		}
		return $this->data;
	}

	
}