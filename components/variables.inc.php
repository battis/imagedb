<?php

/**
 * @param var $value The variable to display
 * @param bool $showSpanTag Whether or not to include a span tag wrapping the output (defaults to false)
 * @return An HTML-formatted <span> containing the value of the variable
 **/
function toHTML ($value, $showSpanTag = false)
{
	$html = ($showSpanTag ? "" : "<span class=\"toHTML\">");
	if (is_array($value))
	{
		$html .= "<ul start=\"0\">";
		while (list($key, $element) = each ($value))
		{
			$html .= "<li>" . toHTML ($key, false) . " &rarr; " . toHTML($element, false) . "</li>";
		}
		$html .= "</ul>";
	}
	else if (is_object($value))
	{
		$html .= $value->toHTML();
	}
	elseif ($value === NULL)
	{
		$html .= "NULL";
	}
	elseif ($value === true)
	{
		$html .= "True";
	}
	elseif ($value === false)
	{
		$html .= "False";
	}
	elseif (is_string($value))
	{
		$html .= "\"{$value}\"";
	}
	else
	{
		$html .= $value;
	}
	return $html . "</span>";
}

?>