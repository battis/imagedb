<?php

require_once (IMAGEDB_PATH . "/components.inc.php");

/* TODO document this! */

class Field extends Option
{
	protected $type;
	// TODO implement field order
	//protected $order;
	
	/*************************************************************************
	 * Constructors
	 *************************************************************************/

	/**
	 * Override the built-in constructor to provide multiple constructor methods identified by signature
	 * @throws ExceptionConstructor If constructor signature does not match Field(int, array)
	 * @see #parameterConstructor()
	 **/
	public function __construct()
	{
		$args = func_get_args();
		$numArgs = func_num_args();
		switch ($numArgs)
		{
			case 2:
				if (is_int($args[0]) && (is_array($args[1])))
				{
					$this->parameterConstructor($args[0], $args[1]);
					break;
				}
			default:
				throw new ExceptionConstructor ("Unknown constructor: Field(" . print_r($args, true) . ")");
		}
	}
	
	/**
	 * @param int $id The ID number of this field in the ImageDB database
	 * @param array $parameters An array of parameters defining the field -- which must include "name" and "type"
	 **/
	protected function parameterConstructor ($id, $parameters)
	{
		$this->id = $id;
		$this->name = $parameters["name"];
		$this->type = $parameters["type"];
		//$this->order = $parameters["order"];
	}
	
	/*************************************************************************
	 * Accessors
	 *************************************************************************/

	/**
	 * @return str The type of the field
	 **/
	public function getType()
	{
		return $type;
	}
	
	/**
	 * @return int The order index of the field
	 **/
	/*public function getOrder()
	{
		return $order;
	}*/
	
	/*************************************************************************
	 * Database operations
	 *************************************************************************/

	/**
	 * @param Item $item The item whose field contents are to be retrieved from the database
	 * @return The contents of this field stored in the database for the given item (null if no record exists)
	 **/
	public function get($item)
	{
		if (is_a($item, "Item"))
		{
			$result = $item->getDatabase()->query(
				"SELECT * FROM `" . METADATA_TABLE . "` " .
				"WHERE " .
					"`item` = '" . $item->getId() . "' AND " .
					"`field` = '{$this->id}' AND " .
					"`container` = '" . $item->getContainer()->getId() . "'"
			);
			
			if ($data = mysql_fetch_assoc($result))
			{
				return stripslashes($data["data"]);
			}
		}
		return NULL;
	}
	
	/**
	 * @param Item $item The item whose field contents should be saved to the database
	 * @param str $data The new field contents to be saved to the database for the item
	 * @return int The ID number of the record in the database containing the updated field data
	 **/
	public function save($item, $data)
	{
		$id = NULL;
		
		if (is_a($item, "Item") && isset($data))
		{
			$result = $item->getDatabase()->query(
				"SELECT * FROM `" . METADATA_TABLE . "` " .
				"WHERE " .
					"`field` = '{$this->id}' AND " .
					"`item` = '" . $item->getId() . "' AND " .
					"`container` = '" . $item->getContainer()->getId() . "'"
			);
			
			if ($oldData = mysql_fetch_assoc($result))
			{
				$item->getDatabase()->query(
					"UPDATE `" . METADATA_TABLE . "` " .
					"SET `data` = '" . mysql_escape_string($data) . "' " .
					"WHERE " .
						"`id` = '{$oldData["id"]}' AND " .
						"`container` = '" . $item->getContainer()->getId() . "'"
				);
				$id = $oldData["id"];
			}
			else
			{
				$item->getDatabase()->query(
					"INSERT INTO `" . METADATA_TABLE . "` " .
					"(`container`, `item`, `field`, `data`) VALUES " .
					"('" . $item->getContainer()->getId() . "', '" . $item->getId() . "', '{$this->id}', '" . mysql_escape_string($data) . "')"
				);
				$id = mysql_insert_id();
			}
			error_log("'" . print_r($data, true) . "' saved to `" . METADATA_TABLE . "` id = '{$id}'");
		}
		return $id;
	}
	
	/**
	 * @param Item $item The item for which this field's contents should be deleted from the database
	 * @return str The contents of the field for this item in the database (null if no record existed)
	 **/
	public function delete ($item)
	{
		if (is_a($item, "Item"))
		{
			$oldData = $this->get($item);
			
			// FIXME error_log the deletion
			$result = $item->getDatabase()->query(
				"DELETE FROM `" . METADATA_TABLE . "` " .
				"WHERE " .
					"`field` = '{$this->id}' AND " .
					"`item` = '" . $item->getId() . "' AND " .
					"`container` = '" . $item->getContainer()->getId() . "'"
			);
			error_log("Item ID " . $item->getId() . ", field `" . $this->name . "` ('{$oldData}') deleted from database.");
			return $oldData;
		}
		return NULL;
	}
	
	/*************************************************************************
	 * HTML form generators
	 *************************************************************************/

	/**
	 * @param Item $item The item for which we should display a loading form for this field
	 * @return str The HTML describing this field as it loads data from the database
	 **/
	public function load($item)
	{
		if (is_a($item, "Item"))
		{
			return "<img " .
				"class=\"loading\" " .
				"onLoad=\"" . apiCall(
					AJAX_REPLACE,
					API_GET_ACTION,
					API_METADATA_OBJECT,
					tokenize($this->name),
					$this->name,
					NULL,
					$item
				) . ";\" " .
				"src=\"" . concatenatePath(getOption("system:url:root"), getOption("system:loading:icon")) . "\" />";
		}
		/* TODO should really return an error image? */
		return "<img class=\"loading\" src=\"" . concatenatePath(getOption("system:url:root"), getOption("system:loading:icon")) . "\" />";
	}

	/**
	 * @param Item $item The item for which to show this field's contents
	 * @param str $data The data to show as this field's contents (for subclass partial overriding)
	 * @return The HTML describing this field and its contents (if any) for this item
	 **/
	public function show($item, $data = NULL)
	{
		if (is_a($item, "Item"))
		{
			if (isset($data) == false)
			{
				$data = $this->get($item);
				if (isset($data) && strlen($data) > 0)
				{
					$data = wordwrap($data, getOption("metadata:columns:max"));
					$data = "<pre>{$data}</pre>";
				}
			}
			
			/* TODO make the form name unique (MD5 hash of ID?) */
			$html = "<label>{$this->name}</label>\n" .
				"{$data}\n";
			if (isset($data) && strlen($data) > 0)
			{
				$html .= button(
					"edit",
					apiCall(
						AJAX_REPLACE,
						API_EDIT_ACTION,
						API_METADATA_OBJECT,
						tokenize($this->name),
						$this->name, 
						NULL,
						$item
					)
				);
				$html .= button(
					"delete",
					apiCall(
						AJAX_REPLACE,
						API_DELETE_ACTION,
						API_METADATA_OBJECT,
						tokenize($this->name),
						$this->name,
						NULL,
						$item
					)
				);
			}
			else
			{
				$html .= button(
					"add",
					apiCall(
						AJAX_REPLACE,
						API_EDIT_ACTION,
						API_METADATA_OBJECT,
						tokenize($this->name),
						$this->name,
						NULL,
						$item
					)
				);
			}
			return $html;
		}
		return "";
	}
	
	/**
	 * @param Item $item The item for which to edit this field's contents
	 * @return str The HTML describing this field's editing form
	 **/
	public function edit($item)
	{
		if (is_a($item, "Item"))
		{
			$data = $this->get($item);
			
			$rows = max(getOption("metadata:rows:min"), min(preg_match_all("|(.*)\n|", $data, $matches) + 1, getOption("metadata:rows:max")));
			$cols = 0;
			$lines = explode("\n", $data);
			foreach ($lines as $line)
			{
				if (strlen($line) > $cols)
				{
					$cols = max(getOption("metadata:columns:min"), min(strlen($line), getOption("metadata:columns:max")));
				}
			}
			/* TODO make textarea automatically resize as text is entered */
			return "<form name=\"" . tokenize($this->name, "form") . "\">\n" .
				"<label " .
						"for=\"" . tokenize($this->name, "data") . "\">" .
					$this->name .
				"</label>\n" .
				"<textarea " .
						"id=\"" . tokenize($this->name, "data") . "\" " .
						"name=\"" . tokenize($this->name, "data") . "\" " .
						"rows=\"{$rows}\" " .
						"cols=\"{$cols}\">" .
					$data .
				"</textarea>\n" .
				button(
					"save",
					apiCall(
						AJAX_REPLACE,
						API_SAVE_ACTION,
						API_METADATA_OBJECT,
						tokenize($this->name),
						$this->name,
						"\$F('" . tokenize($this->name, "data"). "')",
						$item
					)
				) .
				button(
					"cancel",
					apiCall(
						AJAX_REPLACE,
						API_GET_ACTION,
						API_METADATA_OBJECT,
						tokenize($this->name),
						$this->name,
						NULL,
						$item
					)
				) .
				"</form>";
		}
		return "";
	}
	
	/*************************************************************************
	 * Conversion methods
	 *************************************************************************/

	/**
	 * @param Item $item The item for which to show this field's contents (defaults to NULL, will be loaded for global $ITEM if null)
	 * @param str The HTML describing the contents of this field for this item
	 **/
	public function toHTML($item = NULL)
	{
		return "<div id=\"" . tokenize($this->name) . "\">" . $this->load($GLOBALS["ITEM"]) . "</div>";
	}
}

?>