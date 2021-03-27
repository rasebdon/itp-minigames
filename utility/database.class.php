<?php
// this was crowd-sourced from https://www.php.net/manual/en/mysqli-stmt.bind-result.php 
//	and https://codeshack.io/super-fast-php-mysql-database-class/

class Database
{
    public static $instance;
	protected $connection;
	protected $query;
	protected $show_errors = TRUE;
	protected $query_closed = TRUE;
	public $query_count = 0;

	public function __construct($dbhost = 'localhost', $dbuser = 'root', $dbpass = '', $dbname = '', $charset = 'utf8')
	{
		$this->connection = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
		if ($this->connection->connect_error) {
			$this->error('Failed to connect to MySQL - ' . $this->connection->connect_error);
		}
		$this->connection->set_charset($charset);
	}

	public function query($query)
	{
		if (!$this->query_closed) {	// close previous query
			$this->query->close();
		}
		if ($this->query = $this->connection->prepare($query)) {
			if (func_num_args() > 1) {	// more than one arg (dynamic query)
				$x = func_get_args();	// get array of all args
				$args = array_slice($x, 1);	// cut off first arg (query)
				$types = '';	// string of types for prep statements
				$args_ref = array();	// bind_param takes in references as parameter
				foreach ($args as $k => &$arg) {
					if (is_array($args[$k])) {	// if parameters are passed in as array, instead of individual values
						foreach ($args[$k] as $j => &$a) {
							$types .= $this->_gettype($args[$k][$j]);
							$args_ref[] = &$a;
						}
					} else {
						$types .= $this->_gettype($args[$k]);
						$args_ref[] = &$arg;
					}
				}
				// prepends $types in front of $args -> looks like 'ssi', 'text', 'more text', '42'
				array_unshift($args_ref, $types);
				// calls bind_param function in $this->query class, passes in $args_ref as arguments
				call_user_func_array(array($this->query, 'bind_param'), $args_ref);
			}
			$this->query->execute();
			if ($this->query->errno) {
				$this->error('Unable to process MySQL query (check your params) - ' . $this->query->error);
			}
			$this->query_closed = FALSE;
			$this->query_count++;
		} else {
			$this->error('Unable to prepare MySQL statement (check your syntax) - ' . $this->connection->error);
		}
		return $this;
	}

	public function fetchAll($callback = NULL)
	{
		$params = array();
		$row = array();
		$meta = $this->query->result_metadata();
		while ($field = $meta->fetch_field()) {
			$params[] = &$row[$field->name];
		}
		call_user_func_array(array($this->query, 'bind_result'), $params);
		$result = array();
		while ($this->query->fetch()) {
			$r = array();
			foreach ($row as $key => $val) {
				$r[$key] = $val;
			}
			// do callback for each row
			if ($callback != NULL && is_callable($callback)) {
				$value = call_user_func($callback, $r);
				if ($value == 'break') break; // stop if return 'break' in callback
			} else {
				$result[] = $r;
			}
		}
		$this->query->close();
		$this->query_closed = TRUE;
		return $result;
	}

	public function fetchArray()
	{
		$params = array();
		$row = array();
		$meta = $this->query->result_metadata();
		while ($field = $meta->fetch_field()) {
			$params[] = &$row[$field->name];
		}
		// call bind_result in query obj, parameter is $params
		call_user_func_array(array($this->query, 'bind_result'), $params);
		$result = array();
		while ($this->query->fetch()) {
			foreach ($row as $key => $val) {
				$result[$key] = $val;
			}
		}
		$this->query->close();
		$this->query_closed = TRUE;
		return $result != null ? $result : false;
	}

	public function close()
	{
		return $this->connection->close();
	}

	public function numRows()
	{
		$this->query->store_result();
		return $this->query->num_rows;
	}

	public function affectedRows()
	{
		return $this->query->affected_rows;
	}

	public function lastInsertID()
	{
		return $this->connection->insert_id;
	}

	public function error($error)
	{
		if ($this->show_errors) {
			exit($error);
		}
	}

	private function _gettype($var)
	{
		if (is_string($var)) return 's';
		if (is_float($var)) return 'd';
		if (is_int($var)) return 'i';
		return 'b';
	}
}

// INIT DB
Database::$instance = new Database('localhost', 'itp_minigames_admin', 'pr0j3Ct_m1n1G4M3s', 'itp_minigames');