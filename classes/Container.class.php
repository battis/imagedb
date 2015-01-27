<?php

class Container extends Item
{
	/*************************************************************************
	 * Properties
	 *************************************************************************/

	/**
	 * The human-readable name of the container
	 * @var str
	 */
	protected $name;
	
	/**
	 * The options tree associated with this container's container
	 * @var ArrayObject
	 */
	protected $options;
	
	/**
	 * Any containers contained by this container
	 * @var Container
	 */
	protected $children;


	/*************************************************************************
	 * Constructors
	 *************************************************************************/

	/**
	 * Override the built-in constructor to provide multiple constructor methods identified by signature
	 * @throws ExceptionConstructor If constructor signature does not match Container(Database), Container(int, Container), or Container (int, Container, boolean)
	 * @see #defaultConstructor()
	 * @see #lookupConstructor()
	 **/
	public function __construct()
	{
		$args = func_get_args();
		$numArgs = func_num_args();
		switch ($numArgs)
		{
			case 1:
				if (is_a($args[0], "Database"))
				{
					$this->defaultConstructor($args[0]);
					break;
				}
			case 2:
				if (is_int($args[0]) && (is_a($args[1], "Container")))
				{
					$this->lookupConstructor($args[0], $args[1]);
					break;
				}
			case 3:
				if (is_int($args[0]) && is_a($args[1], "Container") && is_bool($args[2]))
				{
					$this->lookupConstructor($args[0], $args[1], $args[2]);
					break;
				}
			default:
				throw new ExceptionConstructor ("Unknown constructor: ImageDB(" . print_r($args, true) . ")");
		}
	}

	/**
	 * @param Database $database An initialized Database object referring to a MySQL database containing ImageDB data
	 **/
	protected function defaultConstructor(Database $database)
	{
		$this->id = getOption("system:id");
		$this->database = $database;
		$this->container = NULL;
		$this->path = getOption("system:path:root");
		$this->name = getOption("system:name");

		$this->loadOptions();
	}

	/**
	 * @param int $id The ID number of the container in the ImageDB database
	 * @param Container $container An initialized container object representing the parent of this container
	 * @param bool $shouldLoadOptions Whether nor not options should be loaded (defaults to false)
	 **/
	protected function lookupConstructor($id, Container $container, $shouldLoadOptions = false)
	{
		parent::lookupConstructor($id, $container, CONTAINER_TABLE);
		if ($shouldLoadOptions)
		{
			$this->loadOptions();
		}
	}



	/*************************************************************************
	 * Methods
	 *************************************************************************/

	/**
	 * Initialize the children of this container from the database
	 **/
	protected function loadChildren()
	{
		$children = $this->database->query ("SELECT `id` FROM `" . CONTAINER_TABLE . "` WHERE `container` = '{$this->id}'");
		if ($children)
		{
			while ($child = mysql_fetch_assoc ($children))
			{
				$childObject = new Container((int) $child["id"], $this);
				$this->children[$childObject->getName()] = $childObject;
			}
		}
	}

	/**
	 * Initialize the options tree of this container from the database
	 **/
	protected function loadOptions()
	{
		$options = $this->database->query ("SELECT * FROM `" . OPTION_TABLE . "` WHERE `container` = '{$this->id}'");
		if ($options)
		{
			while ($option = mysql_fetch_assoc($options))
			{
				$value = unserialize($option["value"]);
				switch ($option["option"])
				{
					case "field":
					{
						/* TODO honor the order of the fields */
						$fieldType = "Field" . ucfirst($value["type"]);
						$this->options["fields"][$value["name"]] = new $fieldType ((int) $option["id"], $value);
						break;
					}
					default:
					{
						$this->options[$option["option"]] = $value;
					}
				}
			}
		}
		if (getOption("path:variable:containerid") == false)
		{
			$this->options["path"]["variable"]["containerid"] = $this->getId();
		}
	}
	

	/*************************************************************************
	 * Accessor methods
	 *************************************************************************/

	/**
	 * @return This container's name
	 **/
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return This container's options
	 **/
	public function getOptions()
	{
		if (isset($this->options) == false)
		{
			$this->loadOptions();
		}
		return $this->options;
	}

	/**
	 * @return The array of container Objects that are children of this container
	 **/
	public function getChildren()
	{
		if (isset($this->children) == false)
		{
			$this->loadChildren();
		}
		return $this->children;
	}


	/*************************************************************************
	 * Conversion methods
	 *************************************************************************/

	/**
	 * @param str $subclassProperties Any additional properties defined by subclasses
	 * @param bool $showDatabaseObject Show the expanded database object (defaults to false)
	 * @param bool $showExpandedContainerObject Show the expanded container of this object (defaults to false: show only ID number and name)
	 * @return An HTML-formatted string version of this object
	 **/
	public function toHTML($subclassProperties = "", $showDatabaseObject = false, $showExpandedContainerObject = false)
	{
		return parent::toHtml(
			"<li class=\"Container-toHTML\">`name`: " . toHTML ($this->name) . "</li>" .
			"<li class=\"Container-toHTML\">`options`: " . toHTML ($this->options) . "</li>" .
			"<li class=\"Container-toHTML\">`children`: " . toHTML ($this->children) . "</li>" .
			$subclassProperties,
		$showDatabaseObject,
		$showExpandedContainerObject
		);
	}
}

?>