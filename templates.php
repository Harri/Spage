<?php
$default_template = '<!doctype html>
<html class="page active" lang="en">
	<head>
	<meta charset="utf-8">
	<meta name="dcterms.created" content="{{date}}">
	<title>{{title}}</title>
	<link rel="stylesheet" href="normalize.css">
	<link rel="stylesheet" href="style.css">
</head>
<body>
	<header>
		<h1>{{title}}</h1>
	</header>
	{{{content_html}}}
	<footer>
	</footer>
</body>
</html>';

$front_page_template = '<!doctype html>
<html lang="en">
	<head>
	<meta charset="utf-8">
	<title>Spage</title>
	<link rel="stylesheet" href="normalize.css">
	<link rel="stylesheet" href="style.css">
</head>
<body>
	<header>
		<h1>Spage</h1>
	</header>
	{{{content_html}}}
	<ol>
	{{#page_list}}
		<li><a href="{{url}}.html">{{title}}</a> ({{date}})</li>
	{{/page_list}}
	</ol>
	<footer>
	</footer>
</body>
</html>';

$rss_template = '<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0">
	<channel>
		<title>Spage</title>
		<description>Spage news feed</description>
		<link>http://spage.example</link>
		{{#pages}}
		<item>
			<title>{{title}}</title>
			<description>{{content_html}}</description>
			<link>http://spage.example/{{url}}</link>
		</item>
		{{/pages}}
	</channel>
</rss>';