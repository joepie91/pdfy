<?php
/*
* pdfhost is more free software. It is licensed under the WTFPL, which
* allows you to do pretty much anything with it, without having to
* ask permission. Commercial use is allowed, and no attribution is
* required. We do politely request that you share your modifications
* to benefit other developers, but you are under no enforced
* obligation to do so :)
*
* Please read the accompanying LICENSE document for the full WTFPL
* licensing text.
*/

if(!isset($_APP)) { die("Unauthorized."); }

$page_number = empty($router->uParameters[1]) ? 1 : (int) $router->uParameters[1];
$max = 6;
$start = ($page_number - 1) * $max;

$res = $database->CachedQuery("SELECT COUNT(`Id`) FROM documents WHERE `Public` = 1");
$total_documents = $res->data[0]["COUNT(`Id`)"];
$total_pages = ceil($total_documents / $max);

$sDocuments = array();

try
{
	foreach (Document::CreateFromQuery("SELECT * FROM documents WHERE `Public` = 1 ORDER BY `Uploaded` DESC, `Id` DESC LIMIT {$start},{$max}") as $document)
	{
		$sDocuments[] = array(
			"name" => $document->sOriginalFilename,
			"views" => $document->sViews,
			"date" => local_from_unix($document->sUploadDate, $locale->datetime_long),
			"slug" => $document->sSlugId
		);
	}
}
catch (NotFoundException $e)
{
	throw new RouterException("No such gallery page exists.");
}

$sPageTitle = "Gallery";
$sPageContents = NewTemplater::Render("gallery", $locale->strings, array(
	"documents" => $sDocuments,
	"next" => ($total_pages > $page_number) ? $page_number + 1 : 0,
	"previous" => ($page_number > 1) ? $page_number - 1 : 0
));
