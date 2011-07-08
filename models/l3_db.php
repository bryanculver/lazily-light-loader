<?php

class l3_db extends mysqli{
		private $messages = array();

		public function __construct()
		{
			parent::__construct(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
			if ($this->connect_error) {
			  $this->flag_message('connection', 'There was an issue connecting to the database.', 'ERRNO: '.$this->connection->connect_errno.'; ERROR: '.$this->connection->connect_error);
				return FALSE;
			}
		}

		function __destruct()
		{
		  $this->close();
		}

		function get_messages()
		{
			return $this->messages;
		}

		function send_query($sql)
		{
			$result = mysql_query($sql);
			if($result === FALSE)
			{

				return FALSE;
			}
			else if(strcasecmp(substr(trim($sql), 0, 6), 'insert') === 0)
			{
				return mysql_insert_id($this->connection);
			}
			else
			{
				return $result;
			}
		}

		/**
		 * RUN A SIMPLE SELECT FROM DATABASE
		 *
		 * @param array|string $what
		 */
		function select($what, $from, $limit = NULL, $where = NULL, $custom_where = NULL, $order = NULL, $join = NULL)
		{
      if($what == NULL) { $what_sql = " * "; }
      else if(is_array($what)) {
        if(!empty($what)) {
          $what_sql = "";
          foreach($what as $v) {
            $what_sql .= $this->real_escape_string("`".preg_replace("/[`]?\.[`]?/", "`.`", preg_replace("/[`]?/", "", $v))."`")." , ";
          }
          $what_sql = substr($what_sql, 0, -2);
        } else {
          $what_sql = " * ";
        }
      } else if(is_string($what)) {
        $what_sql = $this->real_escape_string("`".preg_replace("/[`]?\.[`]?/", "`.`", preg_replace("/[`]?/", "", $what))."`");
      } else {
        $this->flag_message('select', "There was an error in the `what` (1) argument of the function.", "INPUT ARGUMENT (1): ".serialize($what));
        return FALSE;
      }
      
      if(!empty($from) && is_string($from)) {
        $from_sql = "`".DB_PREFIX.$this->real_escape_string($from)."`";
      } else {
        $this->flag_message('select', "There was an error in the `from` (2) argument of the function.", "INPUT ARGUMENT (2): ".serialize($from));
        return FALSE;
      }
      
		  if($order != NULL) {
		    $order[0] = str_replace(".", "`.`", $order[0]);
		  }
		  
		  
		  $sql = "SELECT ".$what_sql." FROM ".$from_sql;

		  if($join != NULL && $on != NULL) {
		  	$sql .= " INNER JOIN `".$join."` ON (".$on.")";
		  }

		  if($where !=  NULL && is_array($where))
		  {
		    $sql .= " WHERE ";
		    foreach($where as $clause => $value)
		    {
		      if(empty($value))
		      {

		      }
		      else if($value == "NULL")
		      {
		        $sql.= "`".mysql_real_escape_string($clause)."` IS NULL AND ";
		      }
		      else if($value == "!NULL")
		      {
		      	$sql.= "`".mysql_real_escape_string($clause)."` IS NOT NULL AND ";
		      }
		      else if(preg_match("/[^\d]/", $value) === 0)
		      {
		        $sql.= "`".mysql_real_escape_string($clause)."` = ".mysql_real_escape_string($value)." AND ";
		      }
		      else
		      {
		        $sql.= "`".mysql_real_escape_string($clause)."` = '".mysql_real_escape_string($value)."' AND ";
		      }
		    }  
	      $sql = substr($sql, 0, -5);

		  }

		  if($custom_where != NULL) $sql .= " WHERE ".$custom_where;
		  if($order !=  NULL) $sql .= " ORDER BY `".$order[0]."` ".strtoupper($order[1]);
		  if($limit !=  NULL && is_int($limit)) $sql .= " LIMIT ".$limit;
		  if($results = $this->query($sql))
		  {
		    if($limit == 1)
		    {
		      $out = $results->fetch_object();
		    }
		    else
		    {
		      $out = array();
	    	  while($result = $results->fetch_object())
	    	  {
	    	    $out[count($out)] = $result;
	    	  }
		    }
	  	  	return $out;
		  }
		  else
		  {
		    $this->flag_message($sql, mysql_error($this->connection));
		    return FALSE;
		  }
		}

		function insert($into, $what)
		{
		  global $DBC;
		  $sql = "INSERT INTO `".$DBC['prefix'].mysql_real_escape_string($into)."`";

	    $sql .= " ( ";

	    $columns = "";
	    $values = "";

	    foreach($what as $column => $value)
	    {

	      if(empty($value))
	      {

	      }
	      else if($value == "NULL")
	      {
	        $columns .= "`".mysql_real_escape_string($column)."`, ";
	        $values .= "NULL, ";
	      }
	      else if(preg_match("/[^\d]/", $value) === 0)
	      {
	        $columns .= "`".mysql_real_escape_string($column)."`, ";
	        $values .= "".mysql_real_escape_string($value).", ";
	      }
	      else
	      {
	        $columns .= "`".mysql_real_escape_string($column)."`, ";
	        $values .= "'".mysql_real_escape_string($value)."', ";
	      }
	    }

	    $columns = substr($columns,0,-2);
	    $values = substr($values,0,-2);

	    $sql .= $columns.") VALUES ( ".$values." );";
		  $results = mysql_query($sql, $this->connection);


		  if($results == TRUE)
		  {
		    return mysql_insert_id($this->connection);
		  }
		  else
		  {
		    $this->flag_message($sql, mysql_error($this->connection));
		    return FALSE;
		  }
		}

	  function delete($from, $where, $limit = NULL)
	  {
	    global $DBC;
		  $sql = "DELETE FROM `".$DBC['prefix'].mysql_real_escape_string($from)."`";
		  $sql .= " WHERE ";
		  foreach($where as $clause => $value)
		  {
		      $sql.= "`".mysql_real_escape_string($clause)."` = '".mysql_real_escape_string($value)."' AND ";
		  }
		  $sql = substr($sql, 0, -5);

		  if($limit !=  NULL && is_int($limit)) $sql .= " LIMIT ".$limit;

		  $results = mysql_query($sql, $this->connection);

		  if($results == FALSE) $this->flag_message($sql, mysql_error($this->connection));

		  return $results;
	  }

	  function update($what, $set, $where = NULL)
	  {
	    global $DBC;
	    $sql = "UPDATE `".$DBC['prefix'].mysql_real_escape_string($what)."`";
	    $sql .= " SET ";
	    foreach($set as $column => $value)
	    {
	      if(strtoupper($value) == NULL) $value = 'NULL';
	      else if(preg_match("/[^\d]/", $value) === 0) $value = mysql_real_escape_string($value);
	      else $value = "'".mysql_real_escape_string($value)."'";
	      $sql .= "`".mysql_real_escape_string($column)."` = ".$value." , ";
	    }
	    $sql = substr($sql, 0 , -3);
	    if($where != NULL)
	    {
	      $sql .= " WHERE ";
	      foreach($where as $clause => $value)
	      {
	        $sql .= "`".mysql_real_escape_string($clause)."` = '".mysql_real_escape_string($value)."' AND ";
	      }
	      $sql = substr($sql, 0 , -5);
	    }
	    $results = mysql_query($sql, $this->connection);

		if($results == FALSE) $this->flag_message($sql, mysql_error($this->connection));

	    return $results;
	  }

	  function flag_message($function, $error, $details)
	  {
	  	$this->messages[] = array("FUNCTION" => $function, "ERROR" => $error, "DETAILS" => $details);
	  }
}

?>