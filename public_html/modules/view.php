<?php
/*
 * PDFy is more free software. It is licensed under the WTFPL, which
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

try
{
	$document = Document::CreateFromQuery("SELECT * FROM documents WHERE `SlugId` = :SlugId", array("SlugId" => $router->uParameters[1]), 60, true);
}
catch (NotFoundException $e)
{
	http_status_code(404);
	$sPageTitle = "Document not found";
	$sPageContents = "No document exists at this URL. It may have been removed, or it may never have existed in the first place.";
	return;
}

/* We use a custom layout here, so we don't need the regular layout wrapper. Just kill off execution here. */
echo(NewTemplater::Render("view", $locale->strings, array(
	"slug" => $document->sSlugId,
	"filename" => $document->sOriginalFilename,
	"views" => $document->sViews
)));
die();
