#!/bin/bash
sass --watch public_html/static/scss/:public_html/static/css/ > sasswatch.log 2> sasswatch.err &
echo $! > sasswatch.pid
