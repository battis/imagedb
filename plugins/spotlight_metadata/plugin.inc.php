<?php

function spotlightField ($field)
{
	$spotlight = shell_exec("mdls -name {$field} \"" . concatenatePath($GLOBALS["CONTAINER"]->getPath(), $GLOBALS["ITEM"]->getPath()) . "\"");
	$data = trim(preg_replace("|{$field} = \"(.*)\"|", "$1", $spotlight));
	$html = "";
	if (strlen($data) > 0 && strpos($data, $field) === false)
	{
		return "<label>{$field}</label>\n<input size=\"" . (strlen($data) * 1.05 + 10) . "\" value=\"{$data}\" />";
	}
	$data = preg_replace("|{$field} =\s+\(\s*((.*\s+)+)\s*\)|m", "$1", $spotlight);
	if (strpos($data, $field) === false)
	{
		$data = explode(",", $data);
		foreach ($data as $d)
		{
			$d = trim($d);
			$html = addToList($html, "<input size=\"" . (strlen($d) * 1.05 + 8) . "\" value=$d />", "<br>");
		}
		return "<label>{$field}</label>\n{$html}";
	}
	return "";
}

function spotlight ($field = null)
{
	$spotlight = shell_exec("mdls" . (isset($field) ? " -name {$field}" : "") . " \"" . concatenatePath($GLOBALS["CONTAINER"]->getPath(), $GLOBALS["ITEM"]->getPath()) . "\"");
	$html = "";
	if (isset($field))
	{
		if (is_array($field))
		{
			foreach ($field as $f)
			{
				$html = addToList($html, spotlightField($f), "\n");
			}
		}
		else
		{
			$html = spotlightField($field);
		}
	}
	else
	{
		$html = "<pre>" .
			shell_exec("mdls \"" . concatenatePath($GLOBALS["CONTAINER"]->getPath(), $GLOBALS["ITEM"]->getPath()) . "\"") .
			"</pre>";
	}
	if (strlen($html) == 0)
	{
		return "";
	}
	return "<div id=\"spotlight-metadata\">" .
		"<h3>" .
			"<img class=\"icon\" src=\"" . concatenatePath(getOption("system:url:plugins"), "spotlight_metadata/spotlight.png"). "\" /> " .
			"Spotlight Metadata" .
		"</h3>" .
		$html .
		"</div>";
}

?>