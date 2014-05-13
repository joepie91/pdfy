PDFy
=====

The PDF hosting software powering http://pdf.yt/.

## Setup

If you wish to run PDFy yourself, this is how:

1. Clone this repository - we'll assume you'll be cloning it to a folder in `/var/sites`, so the resulting folder would be `/var/sites/pdfy`.
2. Clone the [CPHP repository](https://github.com/joepie91/cphp) to `/var/sites` - this will become `/var/sites/cphp`.
3. Switch the CPHP repository to the `feature/formhandler` branch - this is temporary until experimental CPHP features are merged into the master branch.
4. Copy `config.json.example` to `config.json` and configure the database.
5. Create a folder `/var/sites/pdfy/storage` and ensure that it is owned by the user and group that your PHP / HTTPd run under. This is where PDF files will be stored.
6. Assign ownership of `/var/sites/pdfy/public_html/static/thumbs` to that same user and group.
7. Add the relevant configuration to your HTTPd (see below).
8. Replace instances of "http://pdf.yt/" in the code (in particular in embed codes) with the host that your instance will be running at.
9. ????
10. PDFy!

## HTTPd configuration

CPHP, the PHP framework used for PDFy, will handle URL rewriting. To make this work, you will need to tell your HTTPd to forward any non-existent requests to `/var/sites/pdfy/public_html/rewrite.php`. This is how you do that for most common HTTPds. Make sure to place this in your virtual host configuration, *not* your global configuration!

### Apache >= 2.2.16

```
FallbackResource /rewrite.php
```

### Apache < 2.2.16 (mod_rewrite required)

```
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ rewrite.php [L]
```

### lighttpd

```
server.error-handler-404 = "/rewrite.php"
```

### nginx

```
location / {
		try_files $uri $uri/ /rewrite.php;
}
```

## Bugs

Please include the following information (where possible) in bug reports:

* What did you expect it to do?
* What did it do instead?
* Operating System / Distribution (including version)
* Browser (if client-side bug) or HTTPd (if server-side bug)
* PDFy version (identified by commit hash, run `git rev-parse HEAD` on the server)
* If a client-side bug, preferably a URL to the problematic page/document.

You may also file bugs related to the PDF viewer; if necessary, these will be forwarded upstream to the pdf.js project.