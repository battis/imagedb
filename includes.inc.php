<?php

require_once(IMAGEDB_PATH . "/includes/defaults.inc.php");
require_once(IMAGEDB_PATH . "/includes/functions.inc.php");
require_once(IMAGEDB_PATH . "/includes/options.inc.php");
require_once(IMAGEDB_PATH . "/includes/paths.inc.php");

// TODO any way to autoload methods as well as classes?

$includesPath = getOption("system:path:includes");
$includes = opendir($includesPath);
while (($file = readdir($includes)) !== false)
{
	if (!preg_match("|^\..*$|", $file))
	{
		$filePath = concatenatePath($includesPath, $file);
		if (is_dir($filePath))
		{
			// TODO does it even make sense to scan subdirectories of includes? I don't think so...
			include_once ("{$filePath}/include.inc.php");
		}
		else
		{
			include_once ($filePath);
		}
	}
}
closedir($includes);

?>