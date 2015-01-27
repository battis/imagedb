<?php

/**
 * Attempt to automatically include class definitions as they are used -- yay for the __ magic functions!
 * @param str $className The name of the class to be loaded
 **/
function __autoload($className)
{
	include_once IMAGEDB_PATH . "/" . CLASSES_DIRECTORY . "/{$className}.class.php";
}

?>