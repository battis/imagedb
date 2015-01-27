<?php

$componentsPath = getOption("system:path:components");
$components = opendir($componentsPath);
while (($file = readdir($components)) !== false)
{
	if (!preg_match("|^\..*$|", $file))
	{
		$filePath = concatenatePath($componentsPath, $file);
		if (is_dir($filePath))
		{
			include_once ("{$filePath}/component.inc.php");
		}
		else
		{
			include_once ($filePath);
		}
	}
}
closedir($components);

?>