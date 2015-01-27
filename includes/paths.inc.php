<?php

/**
 * Replace all path variables in a path with the actual values of the variables
 * @param str $pathWithVariables The path containing path variables
 * @return The path with actual values instead of the variables
 **/
function replacePathVariables($pathWithVariables)
{
	$path = $pathWithVariables;
	preg_match_all("|" . PATH_VARIABLE_DELIMITER . "([a-z0-9 ]+)" . PATH_VARIABLE_DELIMITER . "|i",
		$pathWithVariables, $variables);
	foreach ($variables[1] as $variable)
	{
		$variable = strtolower($variable);
		$path = preg_replace("|" . PATH_VARIABLE_DELIMITER . $variable . PATH_VARIABLE_DELIMITER . "|i",
			getOption("path:variable:{$variable}"), $path);
	}
	
	return $path;
}

/**
 * @param str $path The path to be tested (relative to the library)
 * @return True if the path is to a specific image in the library
 **/
function isImagePath($path)
{
	/* FIXME This should verify that the path actually points to a file */
	/* FIXME This should verify that $path is actually a path */
	/* FIXME This should verify that the path is relative to the active library */
	return isImageURL($path);
}

/**
 * Generates a valid directory path from two partial paths
 * @param str $left The left portion of the path (may or may not end with "/")
 * @param str $right The right portion of the path (may or may not start with "/")
 * @return A correctly concatenated path made of left and right partial paths
 **/
function concatenatePath($left, $right)
{
	/* FIXME This should verify that $left and $right are actual paths */
	/* TODO Should this verify that the final path is points to an actual file? */
	return concatenateSeam(replacePathVariables($left), replacePathVariables($right));
}

/**
 * @param str $urlencodedPath A disk path that has been URL encoded
 * @param Container $CONTAINER The container object within which the URL exists
 * @return The urldecoded path, if valid, or false if the path contains relative operators (. or ..) or is empty
 * @throws ExceptionPath If path refers to a non-existent file (relative to the Library's path)
 **/
function urldecodePath($urlencodedPath, Container $CONTAINER)
{
	$path = stripslashes (urldecode($urlencodedPath));
	if (strlen($path) == 0 || preg_match("|\.{1,2}/|", $path))
	{
		return false;
	}
	if (!file_exists(concatenatePath($CONTAINER->getPath(), $path)))
	{
		throw new ExceptionPath ("File not found: " . concatenatePath($CONTAINER->getPath(), $path));
	}
	return $path;
}

?>