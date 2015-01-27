<?php

/* Actions */
define("API_GET_ACTION", sanitizeApiTerm("get"));
define("API_APPEND_ACTION", sanitizeApiTerm("append"));
define("API_EDIT_ACTION", sanitizeApiTerm("edit"));
define("API_SAVE_ACTION", sanitizeApiTerm("save"));
define("API_DELETE_ACTION", sanitizeApiTerm("delete"));

/* Objects */
define("API_METADATA_OBJECT", sanitizeApiTerm("metadata"));
define("API_TAG_OBJECT", sanitizeApiTerm("tag"));

/* AJAX methods */
define ("AJAX_REPLACE", "replaceWithApiResponse");
define ("AJAX_APPEND", "appendApiResponse");

/**
 * @param str $apiTerm The API term to be sanitized (an action, object, etc.)
 * @return str A sanitized, canonized version of the API term
 **/
function sanitizeApiTerm($apiTerm)
{
	/* TODO look for SQL injection, etc? */
	return trim(strtolower($apiTerm));
}

/**
 * @param str $apiParameter A parameter to an API call to be examined
 * @return True if the parameter contains a Prototype call, false otherwise
 **/
function isJavascriptLiteralValue($apiParameter)
{
	// this is just quickly testing to see if we're calling a Prototype JS element: $F(whatever)
	return $apiParameter[0] != "\$";
}

/**
 * @param str $apiParametr A parameter to wrap in quotes, if necessary
 * @return The quoted parameter if a literal, null if a NULL parameter, or the original Prototype Javascript call if such it was
 **/
function quoteJavascriptParameter($apiParameter)
{
	if (is_null($apiParameter))
	{
		return "null";
	}
	else if (isJavascriptLiteralValue($apiParameter))
	{
		return "'{$apiParameter}'";
	}
	return $apiParameter;
}

/**
 * @param str $method The name of the Javascript method to call
 * @param str $action The API action verb
 * @param str $object The API object noun
 * @param str $token The token for the DOM element impacted by the method call
 * @param str $key The name of the ImageDB field or tag referenced
 * @param str $data The user-updated value of the ImageDB field or tag
 * @param Item $item The ImageDB item which the field or tag references
 **/
function apiCall($method, $action, $object, $token, $key, $data, $item)
{
	$parameters = addToList("", quoteJavascriptParameter($action));
	$parameters = addToList($parameters, quoteJavascriptParameter($object));
	$parameters = addTolist($parameters, quoteJavascriptParameter($token));
	$parameters = addToList($parameters, quoteJavascriptParameter($key));
	$parameters = addToList($parameters, quoteJavascriptParameter($data));
	$parameters = addToList($parameters, quoteJavascriptParameter($item->getId()));
	$parameters = addToList($parameters, quoteJavascriptParameter($item->getContainer()->getId()));
	
	return "javascript:{$method}({$parameters})";
}

?>