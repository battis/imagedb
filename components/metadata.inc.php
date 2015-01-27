<?php

function metadata()
{
	$html = "";
	
	$fields = getOption("fields");
	if(is_array($fields))
	{
		foreach ($fields as $field)
		{
			$html = addToList($html, $field->toHtml(), "\n");
		}
	}
	
	return $html;
}

?>