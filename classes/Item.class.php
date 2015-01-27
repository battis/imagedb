<?php

class Item
{
	/*************************************************************************
	 * Properties
	 *************************************************************************/
	
	/**
	 * The ID number of this object in the ImageDB database
	 * @var int
	 **/
	protected $id;
	
	/**
	 * The Database object that refers to the database containing this object's data
	 * @var Database
	 **/
	protected $database;
	
	/**
	 * The container that holds this object
	 * @var Container
	 **/
	protected $container;
	
	/**
	 * The path to this object on disk (relative to its container)
	 * @var str
	 **/
	protected $path;
	
	/**
	 * The tags associated with this item
	 * @var array
	 **/
	private $tags;

	/*************************************************************************
	 * Constructors
	 *************************************************************************/

	/**
	 * Override the built-in constructor to provide multiple constructor methods identified by signature
	 * @throws ExceptionConstructor If constructor signature does not match available options
	 * @see #lookupConstructor()
	 * @see #pathConstructor()
	 **/
	function __construct()
	{
		$args = func_get_args();
		$numArgs = func_num_args();
		switch($numArgs)
		{
			case 2:
				if (is_int($args[0]) && is_a($args[1], "Container"))
				{
					$this->lookupConstructor ($args[0], $args[1]);
					break;
				}
				elseif (is_string($args[0]) && is_a ($args[1], "Container"))
				{
					$this->pathConstructor ($args[0], $args[1]);
					break;
				}
			case 3:
				if (is_string($args[0]) && is_a ($args[1], "Container") && is_string($args[2]))
				{
					$this->pathConstructor ($args[0], $args[1], $args[2]);
					break;
				}
			default:
				throw new ExceptionConstructor ("Unknown constructor: Item(" . print_r($args, true) . ")");
		}
	}

	/**
	 * @param int $id The ID number of the object in the ImageDB database
	 * @param Container $container An initialized container object that "holds" this object
	 * @param str $table The database table in which to search for this object
	 * @throws ExceptionLookup If ID number cannot be found int the ImageDB database
	 * @throws ExceptionContainer If container cannot be traced back to the main system
	 **/
	protected function lookupConstructor($id, Container $container, $table = ITEM_TABLE)
	{
		$this->id = $id;
		$this->database = $container->getDatabase();

		$objectData = $this->database->query("SELECT * FROM `{$table}` WHERE `container` = '" . $container->getId() . "' AND `id` = '{$id}' LIMIT 1");
		if ($objectData == false)
		{
			throw new ExceptionLookup("Invalid Item: ID = " . $id);
		}
		
		$object = mysql_fetch_assoc($objectData);
		
		/* build a chain of parents back to the container we were given */
		if ($object["container"] == $container->getId())
		{
			$this->container = $container;
		}
		elseif ($object["container"] != getOption("system:id"))
		{
			$this->container = new Container((int) $object["container"], $container);
		}
		else
		{
			throw new ExceptionContainer ("Not contained by this system");
		}
		
		/* load whatever values are stored in the database that we haven't already computed */
		while (list ($field, $value) = each ($object))
		{
			if (isset($this->$field) == false)
			{
				$this->$field = $value;
			}
		}
	}

	/**
	 * @param str $path The path of the object relative to the container root
	 * @param Container $container An initialized container object that "holds" this Image
	 * @param str $table The database table in which to search for this object
	 * @throws ExceptionPath If there is no file at the path given (relative to the container's path)
	 **/
	protected function pathConstructor($path, Container $container, $table = ITEM_TABLE)
	{
		/* first, see if the image is already in the database */
		$objectData = $container->database->query("SELECT * FROM `" . ITEM_TABLE ."` WHERE `container` = '" . $container->getId() . "' AND `path` = '{$path}' LIMIT 1");
		
		if (mysql_num_rows($objectData) == 0)
		{
			/* it wasn't there -- we need to add this object to the database */

			/* make sure the file really exists, first */
			if (file_exists(concatenatePath($container->getPath(), $path)))
			{
				$this->database = $container->getDatabase();
				$this->database->query("INSERT INTO `" . ITEM_TABLE . "` (`container`, `path`) VALUES " .
					"('{$container->getId()}', '{$path}')");
				$this->id = $this->database->insertId();
				$this->path = $path;
				$this->container = $container;
			}
			else
			{
				throw new ExceptionPath ("Invalid path to object: $path");
			}
		}
		else
		{
			$object = mysql_fetch_assoc($objectData);
			$this->lookupConstructor((int) $object["id"], $container);
		}
	}


	/*************************************************************************
	 * Accessor methods
	 *************************************************************************/

	/**
	 * @return The ID number of this object in the ImageDB database
	 **/
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return The Database object that describes the MySQL database that contains the ImageDB data
	 **/
	public function getDatabase()
	{
		return $this->database;
	}

	/**
	 * @return The container that is the parent of this object
	 **/
	public function getContainer()
	{
		return $this->container;
	}

	/**
	 * @return The path to this object on disk (relative to its container)
	 */
	public function getPath()
	{
		return $this->path;
	}
	
	public function getTags()
	{
		if (isset($tags))
		{
			return $tags;
		}
		
		$tags = $this->database->query(
			"SELECT * FROM `" . TAG_TABLE . "` " .
			"WHERE " .
				"`item` = '{$this->id}'  AND " .
				"`container` = '" . $this->getContainer()->getId() . "' " .
			"ORDER BY " .
				"`tag` ASC"
		);
		
		while ($tag = mysql_fetch_assoc($tags))
		{
			$this->tags[] = new Tag($tag["tag"], $this);
		}
		
		return $this->tags;
	}
	
	/*************************************************************************
	 * Conversion methods
	 *************************************************************************/

	/**
	 * @param str $subclassProperties Any additional properties defined by subclasses
	 * @param bool $showDatabaseObject Show the expanded database object (defaults to false)
	 * @param bool $showExpandedContainerObject Show the expanded container of this object (defaults to false: show only ID number and name)
	 * @return An HTML-formatted string version of this Library object
	 **/
	public function toHTML($subclassProperties = "", $showDatabaseObject = false, $showExpandedContainerObject = false)
	{
		return "<ul class=\"Item-toHTML\">" .
			"<li class=\"Item-toHTML\">`id`: " . toHTML ($this->id) . "</li>" .
		($showDatabaseObject ? "<li>`database`: " . toHTML($this->database) . "</li>" : "") .
			"<li class=\"Item-toHTML\">`container`: "  .
		($showExpandedContainerObject ?
		toHTML ($this->container) : (isset($this->container) ?
				"<ul class=\"Container-toHTML\">" .
					"<li class=\"Container-toHTML\">`name`: " . toHTML ($this->container->getName()) . "</li>" .
					"<li class=\"Container-toHTML\">`id`: " . toHTML ($this->container->getId()) . "</li>" .
				"</ul>"
				: "")) . "</li>" .
				$subclassProperties .
			"</ul>";
	}
}

?>