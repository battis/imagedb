<?php

// TODO enable disabling ;) of plugins in GUI

$pluginsPath = getOption("system:path:plugins");
$plugins = opendir($pluginsPath);
while (($file = readdir($plugins)) !== false)
{
	if (!preg_match("|^\..*$|", $file))
	{
		$filePath = concatenatePath($pluginsPath, $file);
		if (is_dir($filePath))
		{
			include_once ("{$filePath}/plugin.inc.php");
		}
		else
		{
			include_once ($filePath);
		}
	}
}
closedir($plugins);

?>