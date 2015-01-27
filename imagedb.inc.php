<?php

/* TODO it would be hot to pass stored session information in the database to limit extraneous object generation */

/* figure out where we are in the world */
// FIXME -- this breaks when included from within subdirectories! (e.g. imagedb.js.php)
//define ("IMAGEDB_PATH", dirname(__FILE__));
//define ("IMAGEDB_URL", dirname($_SERVER["PHP_SELF"]));
define("IMAGEDB_PATH", "/Volumes/Users Volume/Users/seth/Sites/sandbox/imagedb");
define("IMAGEDB_URL", "/sandbox/imagedb");

require_once (IMAGEDB_PATH . "/config.inc.php");
require_once (IMAGEDB_PATH . "/includes.inc.php");
include_once (IMAGEDB_PATH . "/plugins.inc.php");

/* initialize the objects used to build pages */
$IDB = new Container (new Database (MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE));
if (isset($IDB) == false)
{
	exit;
}

if (isset($_REQUEST["container"]))
{
	try
	{
		$CONTAINER = new Container((int) $_REQUEST["container"], $IDB);
	}
	catch (ExceptionContainer $e)
	{
		unset ($CONTAINER);
	}
}

if (isset($_REQUEST["image"]))
{
	try
	{
		if (isset($CONTAINER))
			$ITEM = new Image((int) $_REQUEST["image"], $CONTAINER);
		else
			$ITEM = new Image((int) $_REQUEST["image"], $IDB);
	}
	catch (ExceptionContainer $e)
	{
		unset ($ITEM);
	}
	
	if (isset($ITEM) && isset($CONTAINER) == false)
		$CONTAINER = $ITEM->getContainer();
}

if (isset($_REQUEST["path"]))
{
	if (isset($CONTAINER))
	{
		$PATH = urldecodePath ($_REQUEST["path"], $CONTAINER);
		if (isImagePath($_REQUEST["path"]) && isset($ITEM) == false)
		{
			try
			{
				$ITEM = new Image ($PATH, $CONTAINER);
			}
			catch (ExceptionPath $e)
			{
				unset ($PATH);
				unset ($ITEM);
			}
		}
	}
}

?>