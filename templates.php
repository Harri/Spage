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
	<form action="search.php" method="get" id="search_form">
		<fieldset>
			<legend>Search</legend>
			<label for="terms">Search terms</label>
			<input name="terms" id="terms" required="required">
			<button type="submit">Search</button>
		</fieldset>
	</form>
	<header>
		<h1>{{title}}</h1>
	</header>
	{{{content_html}}}
	<footer>
	</footer>
</body>
</html>';

$default_template_with_comments = '<!doctype html>
<html lang="en">
	<head>
	<meta charset="utf-8">
	<meta name="dcterms.created" content="{{date}}">
	<title>{{title}}</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>
	<form action="search.php" method="get" id="search_form">
		<fieldset>
			<legend>Search</legend>
			<label for="terms">Search terms</label>
			<input name="terms" id="terms" required="required">
			<button type="submit">Search</button>
		</fieldset>
	</form>
	<header>
		<h1>{{title}}</h1>
	</header>
	{{{content_html}}}
	<ol>
		{{#comments}}
		<li id="{{uuid}}">
			<div class="comment_author">{{{author}}}</div>
			<div class="comment">{{{comment}}}</div>
			<div class="comment_time">{{date}} {{time}}</div>
		</li>
		{{/comments}}
	</ol>

	<form action="comments.php" method="post" id="comment_form">
		<fieldset>
			<legend>Write new comment</legend>
			<label for="author">Name and/or email address: </label>
			<input maxlength="200" name="author" id="author" required="required">
			<label for="message">Message: </label>
			<textarea maxlength="10000" id="message" name="message" rows="20" required="required"></textarea>
			<input type="hidden" name="page" value="{{url}}">
			<button type="submit">Comment</button>
		</fieldset>
	</form>

	<footer>
	</footer>
</body>
</html>';

$moderate_template_with_comments = '<!doctype html>
<html lang="en">
	<head>
	<meta charset="utf-8">
	<meta name="dcterms.created" content="{{date}}">
	<title>{{title}}</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>
	<form action="search.php" method="get" id="search_form">
		<fieldset>
			<legend>Search</legend>
			<label for="terms">Search terms</label>
			<input name="terms" id="terms" required="required">
			<button type="submit">Search</button>
		</fieldset>
	</form>
	<header>
		<h1>{{title}}</h1>
	</header>
	{{{content_html}}}
	<ol>
		{{#comments}}
		<li id="{{uuid}}">
			<div class="comment_author">{{{author}}}</div>
			<div class="comment">{{{comment}}}</div>
			<div class="comment_time">{{date}} {{time}}</div>
		</li>
		{{/comments}}
	</ol>

	<form action="comments.php" method="post" id="comment_form">
		<fieldset>
			<p>Comments on this page are reviewed before publishing.</p>
			<legend>Write new comment</legend>
			<label for="author">Name and/or email address: </label>
			<input maxlength="200" name="author" id="author" required="required">
			<label for="message">Message: </label>
			<textarea maxlength="10000" id="message" name="message" rows="20" required="required"></textarea>
			<input type="hidden" name="page" value="{{url}}">
			<button type="submit">Comment</button>
		</fieldset>
	</form>

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
		<p>Following search terms were not used since they are under {{min_term_lenght}} characters: {{terms}}</p>
	{{/dropped}}
	{{#sliced}}
		<p>Only first {{max_terms}} search terms were used. Follwing were not used: {{terms}}</p>
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
			<pubDate>{{pub_date}}</pubDate>
		</item>
		{{/pages}}
	</channel>
</rss>';

$sitemap_template = 'http://spage.example
{{#pages}}http://spage.example/{{url}}
{{/pages}}';
