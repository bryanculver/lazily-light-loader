<?php

class l3_db_model {
	private $structure = array();
	protected $id;

	function __construct($id = NULL) {
		$this->get_structure();
		if($id != NULL)
		{
			global $db;

			$tmp = $db->select('*', substr(get_class($this), strlen(CLASS_PREFIX) + 1), 1, array('id' => $id));

			if($tmp != FALSE)
			{
				foreach($tmp as $key => $value)
				{
					$this->$key = $value;
				}	
			}
		}
	}

	function __get($name) {
		return $this->$name;
	}

	function __set($name, $value) {
		$this->$name = $value;
	}

	function set($in) {
		foreach($in as $k => $v)
		{
			if(in_array($k, $this->structure)) {
				$this->$k = $v;
			}
		}
	}

	function get_structure() {
		global $DBC;
		global $db;
		$tmp = $db->send_query("DESCRIBE `".$DBC['prefix'].substr(get_class($this), strlen(CLASS_PREFIX) + 1)."`");
		$this->structure = array();
		while($i = mysql_fetch_object($tmp))
		{
			$this->structure[] = $i->Field;
			if($i->Default == 'CURRENT_TIMESTAMP') {
				$this->{$i->Field} = date('Y-m-d H:i:s', time());
			} else {
				$this->{$i->Field} = $i->Default;
			}
		}
	}

	function create() {
		global $db;
		$out = array();
		foreach($this->structure as $k) {
			$out[$k] = $this->$k;
		}
		$this->id = $db->insert(substr(get_class($this), strlen(CLASS_PREFIX) + 1), $out);
		return $this->id;
	}

	function delete() {
		global $db;
		return $db->delete(substr(get_class($this), strlen(CLASS_PREFIX) + 1), array('id' => $this->id));
	}

	function update() {
		global $db;
		$out = array();
		foreach($this->structure as $k) {
			$out[$k] = $this->$k;
		}
		return $db->update(substr(get_class($this), strlen(CLASS_PREFIX) + 1), $out, array('id' => $this->id));
	}

?>