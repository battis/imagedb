<?php

class Database
{
	/*************************************************************************
	 * Properties
	 *************************************************************************/

	/**
	 * The URL of the MySQL server
	 * @var str
	 **/
	protected $server;
	
	/**
	 * The user name to access the MySQL server
	 * @var str
	 **/
	protected $user;
	
	/**
	 * The password to access the MySQL server
	 * @var str
	 **/
	protected $password;
	
	/**
	 * The name of the database containing the ImageDB data on the MySQL server
	 * @var str
	 **/
	protected $database;
	
	/**
	 * The persistent link to the MySQL database resource
	 **/
	protected $link;


	/*************************************************************************
	 * Constructors
	 *************************************************************************/

	/**
	 * @throws ExceptionConstructor If constructor signature does not match available options
	 * @see #completeConstructor()
	 **/
	public function __construct()
	{
		$args = func_get_args();
		$numArgs = func_num_args();
		switch($numArgs)
		{
			case 4:
				if (is_string($args[0]) && is_string($args[1]) && is_string ($args[2]) && is_string ($args[3]))
				{
					$this->completeConstructor ($args[0], $args[1], $args[2], $args[3]);
					break;
				}
			default:
				throw new ExceptionConstructor ("Unknown constructor: Database(" . print_r($args, true) . ")");
		}
	}

	/**
	 * @param str $server The MySQL server address
	 * @param str $user The user name for access to the MySQL server
	 * @param str $password The password for access to the MySQL server
	 * @param str $database The database to access on the MySQL server
	 * @throws ExceptionMySQL If connection cannot be made or database cannot be selected
	 **/
	protected function completeConstructor ($server, $user, $password, $database)
	{
		$this->server = $server;
		$this->user = $user;
		$this->password = $password;
		$this->database = $database;

		if (!($this->link = mysql_pconnect($this->server, $this->user, $this->password)))
		{
			throw new ExceptionMySQL ("Could not connect to server: " . $this->server);
		}

		if (!mysql_select_db($this->database, $this->link))
		{
			throw new ExceptionMySQL ("Could not select database: ". $this->database);
		}
	}


	/*************************************************************************
	 * Methods
	 *************************************************************************/

	/**
	 * @return The ID number of the most recently inserted row in the database
	 **/
	public function insertId()
	{
		return mysql_insert_id($this->link);
	}

	/**
	 * @param str $query The MySQL query to be run
	 * @return the result (or lack thereof) of the query
	 **/
	public function query ($query)
	{
		// TODO escape query to avoid sql-injection errors
		// TODO wrap the result in an object
		return mysql_query ($query, $this->link);
	}


	/*************************************************************************
	 * Conversion methods
	 *************************************************************************/

	/**
	 * @param bool $showPasswordPlainText Whether or not to show the password in plaintext (defaults to false)
	 * @return An HTML-formatted string version of this Database object
	 **/
	public function toHTML($showPasswordPlainText = false)
	{
		return "<ul class=\"Database-toHTML\">" .
			"<li class=\"Database-toHTML\">`server`: " . toHTML ($this->server) . "</li>" .
			"<li class=\"Database-toHTML\">`user`: " . toHTML ($this->user) . "</li>" .
			"<li class=\"Database-toHTML\">`password`: " . ($showPasswordPlainText ? toHTML ($this->password) : "&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;") . "</li>" .
			"<li class=\"Database-toHTML\">`database`: " . toHTML ($this->database) . "</li>" .
			"<li class=\"Database-toHTML\">`link`: " . toHTML ($this->link) . "</li>" .
			"</ul>";
	}
}

?>