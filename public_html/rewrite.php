<?php

$_CPHP = true;
$_CPHP_CONFIG = "../config.json";
require("cphp/base.php");

$_APP = true;

$sPageTitle = "";
$sPageContents = "";

$router = new CPHPRouter();

$router->ignore_query = true;
$router->allow_slash = true;

$router->routes = array(
	0 => array(
		"^/$" => "modules/index.php",
		"^/upload$" => "modules/upload.php",
		"^/error(/[0-9]*)?$" => "modules/error.php",
		"^/document/([^/]+)$" => "modules/view.php",
		/*"^/document/([^/]+)/delete/([^/]+)$" => "modules/delete.php",*/
		"^/document/([^/]+)/download$" => "modules/download.php",
		"^/document/([^/]+)/embed$" => "modules/embed.php",
	)
);

$router->RouteRequest();

echo(NewTemplater::Render("layout", $locale->strings, array(
	"title" => $sPageTitle,
	"contents" => $sPageContents
)));
