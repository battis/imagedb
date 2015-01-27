<?php

include_once ("./imagedb.inc.php");
include_once ("./components.inc.php");

/* if we don't have an image, we have nothing to see the details of! */
if (isset($ITEM) == false)
{
	header("Location: index.php");
	exit;
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>ImageDB::<?= $CONTAINER->getName() ?><?= (isset($PATH) ? $PATH : "") ?></title>

	<!-- begin Lightbox 2.04 -->
	<link rel="stylesheet" href="css/lightbox.css" type="text/css" media="screen" />
	<script type="text/javascript" src="js/prototype.js"></script>
	<script type="text/javascript" src="js/scriptaculous.js?load=effects,builder"></script>
	<script type="text/javascript" src="js/lightbox.js"></script>
	<!-- end Lightbox 2.04 -->

	<script type="text/javascript" src="js/imagedb.js.php"></script>
	<link rel="stylesheet" media="screen" type="text/css" href="css/screen.css.php" />
</head>
<body>
	<h1><?= basename($ITEM->getPath()) ?></h1>
	<p><?= breadcrumbs() ?></p>
	<p><a rel="lightbox" class="preview" href="<?= buildGetURL("image.php", $CONTAINER, array("image" => $ITEM->getId()))?>"><img class="preview" src="<?= buildGetURL("image.php", $CONTAINER, array("image" => $ITEM->getId(), "size" => 650)) ?>" /></a></p>
	<div id="metadata">
		<h2><?= getOption("metadata:name") ?></h2>
		<?= metadata() ?>
	</div>
	<div id="tags">
		<h2><?= getOption("tags:name") ?></h2>
		<?= tags() ?>
	</div>
	<?= spotlight(array("kMDItemFinderComment", "kMDItemWhereFrom", "kMDItemWhereFroms")) ?>
	<?= findOnFlickr() ?>
	<?= duplicateObjects($ITEM) ?>
</body>
</html>