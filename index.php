<?php

include_once ("./imagedb.inc.php");
include_once ("./components.inc.php");

?>
<!DOCTYPE html>
<html>
<head>
	<title><?= $IDB->getName() ?></title>
	<link rel="stylesheet" media="screen" type="text/css" href="css/screen.css.php" />
</head>
<body>
	<ol><?php

		foreach ($IDB->getChildren() as $container)
		{
			echo "\n\t<li><a href=\"" . buildGetURL("browse.php", $container, "/"). "\">{$container->getName()}</a></li>";
		}
	
	?>
	</ol>
</body>
</html>