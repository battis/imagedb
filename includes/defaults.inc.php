<?php

/* TODO which of these should really be global variables, rather than define statements? */
/* TODO is there any efficiency gain to using define statements? */

/* special characters */
define ("CANONICAL_OPTION_DELIMITER", ":");
define ("PATH_VARIABLE_DELIMITER", "%");

/* database table names */
define ("ITEM_TABLE", "items");
define ("CONTAINER_TABLE", "containers");
define ("OPTION_TABLE", "options");
define ("METADATA_TABLE", "metadata");
define ("TAG_TABLE", "tags");

/* PHP file directory paths (relative to ImageDB root) */
define ("DATA_DIRECTORY", "data");
define ("CLASSES_DIRECTORY", "classes");
define ("INCLUDES_DIRECTORY", "includes");
define ("COMPONENTS_DIRECTORY", "components");
define ("PLUGINS_DIRECTORY", "plugins");
define ("API_URL", "api.php");
define ("JAVASCRIPT_DIRECTORY", "js");
define ("STYLESHEET_DIRECTORY", "css");
define ("IMAGES_DIRECTORY", "images");

/*
 * Arbitrary default values
 *
 * All of these values can be over-ridden by per-library settings in the
 * options panel. These defaults are used when a library does not have its own
 * settings.
 */
$DEFAULTS = array (
	"system" => array(
		"id" => -1,
		"name" => "ImageDB",
		"icon" => "images/home.gif",
		"logo" => "images/home.gif",
		"path" => array(
			"root" => IMAGEDB_PATH,
			"data" => IMAGEDB_PATH . "/" . DATA_DIRECTORY,
			"classes" => IMAGEDB_PATH . "/" . CLASSES_DIRECTORY,
			"includes" => IMAGEDB_PATH . "/" . INCLUDES_DIRECTORY,
			"components" => IMAGEDB_PATH . "/" . COMPONENTS_DIRECTORY,
			"plugins" => IMAGEDB_PATH . "/" . PLUGINS_DIRECTORY,
		),
		"url" => array (
			"api" => IMAGEDB_URL . "/" . API_URL,
			"root" => IMAGEDB_URL,
			"data" => IMAGEDB_URL . "/" . DATA_DIRECTORY,
			"classes" => IMAGEDB_URL . "/" . CLASSES_DIRECTORY,
			"includes" => IMAGEDB_URL . "/" . INCLUDES_DIRECTORY,
			"components" => IMAGEDB_URL . "/" . COMPONENTS_DIRECTORY,
			"plugins" => IMAGEDB_URL . "/" . PLUGINS_DIRECTORY,
		),
		"loading" => array(
			"icon" => "images/loading.gif"
		)
	),
	"container" => array(
		"icon" => "images/container.gif",
		"logo" => "images/container.gif"
	),
	"cache" => array(
		"path" => PATH_VARIABLE_DELIMITER . "data" . PATH_VARIABLE_DELIMITER . "/" .
			PATH_VARIABLE_DELIMITER . "containerid" . PATH_VARIABLE_DELIMITER . "/Thumbnail Cache/"
	),
	"metadata" => array(
		"name" => "Metadata",
		"rows" => array(
			"min" => 1,
			"max" => 24
		),
		"columns" => array(
			"min" => 20,
			"max" => 80
		)
	),
	"tags" => array(
		"name" => "Tags"
	)
);
		/* setting separately so that we can use already set values in expressions */
$DEFAULTS["path"]["variable"] = array(
	"root" => $DEFAULTS["system"]["path"]["root"],
	"data" => $DEFAULTS["system"]["path"]["data"],
	"classes" => $DEFAULTS["system"]["path"]["classes"],
	"includes" => $DEFAULTS["system"]["path"]["includes"],
	"components" => $DEFAULTS["system"]["path"]["components"],
	"plugins" => $DEFAULTS["system"]["path"]["plugins"],
);

?>