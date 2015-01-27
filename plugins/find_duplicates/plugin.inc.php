<?php

/**
 * @param Item $object An object to check for duplicates
 * @return An HTML-formatted list of links to duplicates of $object in the database
 **/
function duplicateObjects($object)
{
	$html = "";
	if (strlen($hash = $object->getHash()) > 0)
	$duplicates = $object->getDatabase()->query("SELECT * FROM `" . ITEM_TABLE . "` WHERE `hash` = '{$hash}' AND `id` <> ' AND `container` = '" . $object->getContainer() . "'" . $object->getId() . "'");
	while ($duplicate = mysql_fetch_assoc($duplicates))
	{
		$html = addToList($html, "\t<li><a target=\"_blank\" href=\"" . buildGetUrl ("detail.php", new Container((int)$duplicate["container"], $GLOBALS["IDB"]), $duplicate["path"]) . "\">" . basename($duplicate["path"]) . "</a></li>", "\n");
	}
	return (strlen($html) > 0 ? "<h3>Duplicates</h3>\n<ol>\n{$html}\n</ol>" : $html);
}

?>