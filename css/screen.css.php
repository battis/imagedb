<?php

header('Content-type: text/css');

include_once ("../imagedb.inc.php");

?>
body
{
	font-family: Helvetica, Arial, sans-serif;
	font-size: 10pt;
}

label
{
	display: block;
	font-size: 8pt;
	font-weight: bold;
	margin-top: 10px;
}

pre,
p
{
	margin: 0px;
	padding: 0px;
}

div.tag
{
	-webkit-border-radius: 2px;
	-moz-border-radius: 2px;
	border-radius: 2px;
	border: 1px solid #ddf;
	color: #aaf;
	margin: 2px;
	padding: 1px;
}

div.button
{
	display: inline-block;
	padding: 1px 6px;
	margin: 2px;
	font-size: 8pt;
	border: 1px #ccc solid;
	background: #ddd;
	-webkit-border-radius: 6px;
	-moz-border-radius: 6px;
	border-radius: 6px;
}

div.button a {
	text-decoration: none;
	color: #666;
	background: transparent;
}

img
{
	border: 1px black solid;
}

img.icon,
img.loading,
img.button
{
	width: 10pt;
	height: 10pt;
	border: 0px none;
}

img.preview
{
	position: absolute;
	top: 10px;
	right: 10px;
	z-index: -1;
}

<?= getOption("css:screen") ?>