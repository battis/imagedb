<?php

include_once ("./imagedb.inc.php");
include_once ("./components.inc.php");

/* if we don't have a container, we have nothing to browse! */
if (isset($CONTAINER) == false)
{
	header("Location: index.php");
	exit;
}

/* redirect to the root of the library if none specified */
if (isset($PATH) == false)
{
	header("Location: " . buildGetURL("browse.php", $CONTAINER, "/"));
	exit;
}

/* generate a listing of the requested path */
$files = array();
$subdirectories = array();
$directoryPath = concatenatePath($CONTAINER->getPath(), $PATH); 
$directory = opendir($directoryPath);
while (($file = readdir($directory)) !== false)
{
	/* filter out invisible files */
	if (!preg_match("|^\..*$|", $file))
	{
		if (is_dir(concatenatePath($directoryPath, $file)))
		{
			/* filter out invisible directories */
			$subdirectories[$file] = concatenatePath($PATH, $file);
		}
		else
		{
			/* TODO filter out non-image files */
			$files[$file] = concatenatePath($PATH, $file);
		}
	}
}
closedir($directory);

?>
<!DOCTYPE html>
<html>
<head>
	<title>ImageDB::<?= $CONTAINER->getName() ?><?= $PATH ?></title>
	<link rel="stylesheet" media="screen" type="text/css" href="css/screen.css.php" />
</head>
<body>

<p><?= breadcrumbs() ?></p>

<?php

while (list($file, $filePath) = each ($files))
{
	echo "<a href=\"" . buildGetURL("detail.php", $CONTAINER, $filePath) .
		"\"><img src=\"" . buildGetURL("image.php", $CONTAINER,
		array("path" => $filePath, "thumbnail" => NULL)) .
		"\" alt=\"{$file}\" /></a>\n";
}

echo "<ul>\n";
while (list($directory, $filePath) = each ($subdirectories))
{
	echo "\t<li><a href=\"" . buildGetURL("browse.php", $CONTAINER,
		$filePath) . "\">{$directory}</a></li>\n";
}
echo "</ul>\n";

?>

</body>
</html>