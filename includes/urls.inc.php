<?php

/**
 * @param str $url The page URL
 * @param Container $CONTAINER The container object within which the URL is being constructed
 * @param var $pathOrParameters Either a path on the disk or an associative array of parameter names and values: array ("param1" => "value1", "param2" => "value2")
 * @return A URL to the page including all Get parameters, url-encoded
 **/
function buildGetURL ($url, Container $CONTAINER, $pathOrParameters = NULL)
{
	$url = buildURL($url);
	$url .= "?container=" . $CONTAINER->getId();
	if (is_array($pathOrParameters))
	{
		while (list($parameter, $value) = each ($pathOrParameters))
		{
			if ($value == NULL)
			{
				$url = addToList($url, urlencode($parameter), "&");
			}
			else
			{
				$url = addToList($url, urlencode($parameter) . "=" . urlencode($value), "&");
			}
		}
	}
	elseif (isset($pathOrParameters))
	{
		$url .= "&path=" . urlencode($pathOrParameters);
	}
	return $url;
}

/**
 * @param $url A url to a page within the system: "browse.php"
 * @return A fully-qualified URL to that page: "/sandbox/imagedb/browse.php"
 **/
function buildURL($url)
{
	if (preg_match("|^[^/\.].*|", $url))
	{
		return concatenateURL(getOption("system:url:root"), $url);
	}
	return $url;
}

/**
 * Generates a valid URL from two partial URLs
 * @param str $left The left portion of the URL (may or may not end with "/")
 * @param str $right The right portion of the URL (may or may not start with "/")
 * @return A correctly concatenated URL made of left and right partial URLs
 **/
function concatenateURL($left, $right)
{
	return concatenateSeam ($left, $right);
}

/**
 * @param str $url The URL to be tested (relative to the library)
 * @return True if the URL is to a specific image in the library
 **/
function isImageURL($url)
{
	/* FIXME false positive on folders with file extensions in the name... */
	return preg_match ("|^.*\.\w+$|", $url) == true;
}

?>