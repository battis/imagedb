<?php

/**
 * @param str $name The name of the button (which will be displayed if no image is present)
 * @param str $action The contents of the HREF for the button a URL or a Javascript
 * @param str $imagePath the path to an image to use for the button (relative to the root URL of ImageDB, defaults to NULL)
 * @return Formatted HTML to display the button
 **/
function button ($name, $action, $imagePath = NULL)
{
	return "<div class=\"button\"><a href=\"$action\">" . (isset($imagePath) && strlen($imagePath) > 0 ? "<img class=\"button\" src=\"{imagePath}\" alt=\"{$name}\" />" : $name) . "</a></div>";
}

?>