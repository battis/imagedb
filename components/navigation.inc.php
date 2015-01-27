<?php

/**
 * @return The breadcrumb trail to the current page as HTML
 **/
function breadcrumbs()
{
	if (isset($GLOBALS["PATH"]))
	{
		$files = explode ("/", $GLOBALS["PATH"]);
	}
	elseif (isset($GLOBALS["ITEM"]))
	{
		$files = explode ("/", $GLOBALS["ITEM"]->getPath());
	}
	else
	{
		$files = array();
	}
	
	$breadcrumbs = "<a href=\"index.php\"><img class=\"icon\" src=\"" . buildURL(getOption("system:icon")) . "\" /></a> :: " .
		"<a href=\"" . buildGetURL("browse.php", $GLOBALS["CONTAINER"]) . "\"><img class=\"icon\" src=\"" . buildURL(getOption("container:icon")) . "\" /> " . $GLOBALS["CONTAINER"]->getName() . "</a>";
		
	$incrementalURL = "/";
	foreach ($files as $file)
	{
		/* no need to include the empty string before the initial / in the breadcrumb trail */
		if (strlen($file) > 0)
		{
			$incrementalURL = concatenateURL($incrementalURL, $file);
			if (isImageURL($incrementalURL))
			{
				if (isset($GLOBALS["ITEM"]))
				{
					$breadcrumbs = addToList ($breadcrumbs, "<a href=\"" . buildGetURL("detail.php", $GLOBALS["CONTAINER"], array("image" => $GLOBALS["ITEM"]->getId())) . "\">{$file}</a>", " / ");
				}
				else
				{
					$breadcrumbs = addToList ($breadcrumbs, "<a href=\"" . buildGetURL("detail.php", $GLOBALS["CONTAINER"], $incrementalURL) . "\">{$file}</a>", " / ");
				}
			}
			else
			{
				$breadcrumbs = addToList ($breadcrumbs, "<a href=\"" . buildGetURL("browse.php", $GLOBALS["CONTAINER"], $incrementalURL) . "\">{$file}</a>", " / ");
			}
		}
	}
	
	return $breadcrumbs;
}

/**
 * @return The permalink to the current page
 **/
function permalink()
{
	// FIXME actually build the permalink	
	return "permalink goes here";
}

?>