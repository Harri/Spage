<?php
$default_template = '<!doctype html>
<html lang="en">
	<head>
	<meta charset="utf-8">
	<meta name="dcterms.created" content="{{date}}">
	<title>{{title}}</title>
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
	<link rel="stylesheet" href="style.css">
</head>
<body>
	<header>
		<h1>Spage</h1>
	</header>
	{{{content_html}}}

	{{#few_latests}}
		<h1><a href="{{url}}">{{title}}</a></h1>
		{{{content_html}}}
	{{/few_latests}}

	<ol>
	{{#page_list}}
		<li><a href="{{url}}">{{title}}</a> ({{date}})</li>
	{{/page_list}}
	</ol>
	<footer>
	</footer>
</body>
</html>';

$search_results_template = '<!doctype html>
<html lang="en">
	<head>
	<meta charset="utf-8">
	<title>Search results for "{{terms}}"</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>
	<header>
		<h1>Search results for "{{terms}}"</h1>
	</header>
	{{#dropped}}
		<p>Following search terms were not used since they are under 3 characters: {{terms}}</p>
	{{/dropped}}
	{{#sliced}}
		<p>Only first 10 search terms were used. Follwing were not used: {{terms}}</p>
	{{/sliced}}
	<ol>
	{{#results}}
		<li><a href="{{url}}.html">{{title}}</a> ({{date}})</li>
	{{/results}}
	</ol>
	<form action="search.php" method="get">
		<fieldset>
			<legend></legend>
			<label for="terms">Search terms</label>
			<input name="terms" id="terms" required="required" value="{{orig_terms}}">
		</fieldset>
		<fieldset>
			<button type="submit">Submit</button>
		</fieldset>
	</form>
	<footer>
	</footer>
</body>
</html>';

$search_template = '<!doctype html>
<html lang="en">
	<head>
	<meta charset="utf-8">
	<title>Search</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>
	<header>
		<h1>Search</h1>
	</header>
	<form action="search.php" method="get">
		<fieldset>
			<legend></legend>
			<label for="terms">Search terms</label>
			<input name="terms" id="terms" required="required">
		</fieldset>
		<fieldset>
			<button type="submit">Submit</button>
		</fieldset>
	</form>
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