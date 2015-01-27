<?php

function tags()
{
	$tagsHtml = "";
	$tags = $GLOBALS["ITEM"]->getTags();
	if(is_array($tags))
	{
		foreach($GLOBALS["ITEM"]->getTags() as $tag)
		{
			$tagsHtml = addToList($tagsHtml, "<div id=\"" . tokenize($tag, $GLOBALS["ITEM"]->getId()) . "\">$tag->getTag()</div>", "\n");
		}
	}
	return "<div id=\"" . tokenize("tags", $GLOBALS["ITEM"]->getId()) . "\">{$tagsHtml}<div id=\"addTag\">" . button("add", apiCall(AJAX_APPEND, API_APPEND_ACTION, API_TAG_OBJECT, tokenize("tags", $GLOBALS["ITEM"]->getId()), NULL, "' + \$F('form element')", $GLOBALS["ITEM"])) . "</div></div>";
}

?>