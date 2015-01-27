<?php

function findOnFlickr($image = NULL)
{
	if (isset ($image) == false)
	{
		$image = $GLOBALS["ITEM"];
	}
	if (isset ($image) == false)
	{
		return "";
	}
	$filename = basename($image->getPath());
	if (strlen ($filename))
	{
		preg_match ("/([a-f0-9]+)(_[a-f0-9]+_.(\..{3,4})?)/", $filename, $matches);
		if (isset($matches[1]) && strlen ($matches[1]) > 0)
		{
			return "<div id=\"find-on-flickr\">" .
				"<h3>" .
					"<img class=\"icon\" src=\"" . concatenatePath(getOption("system:url:plugins"),
																   "find_on_flickr/flickr_icon.png") . "\" /> " .
					"Flickr URL" .
				"</h3>" .
				"<a target=\"_blank\" href=\"http://flickr.com/photo.gne?id={$matches[1]}\">Show on Flickr</a>" .
				"</div>";
		}
	}
	return "";
}

?>