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
		"^/tos$" => "modules/tos.php",
		"^/upload$" => "modules/upload.php",
		"^/error(/[0-9]*)?$" => "modules/error.php",
		"^/d(?:ocument)?/([^/]+)$" => "modules/view.php",
		/*"^/d(?:ocument)?/([^/]+)/delete/([^/]+)$" => "modules/delete.php",*/
		"^/d(?:ocument)?/([^/]+)/download$" => "modules/download.php",
		"^/d(?:ocument)?/([^/]+)/embed$" => "modules/embed.php",
	)
);

try
{
	$router->RouteRequest();
}
catch (RouterException $e)
{
	http_status_code(404);
	$sPageTitle = "Uh oh!";
	$sPageContents = "<h2>Uh oh!</h2><p>We could not find the page you are looking for.</p>";
}

echo(NewTemplater::Render("layout", $locale->strings, array(
	"title" => $sPageTitle,
	"contents" => $sPageContents
)));
