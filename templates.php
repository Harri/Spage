<?php
$default_template = '<!doctype html>
<html class="page active" lang="en">
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

$page_list_template = '<!doctype html>
<html class="page active" lang="en">
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
	<footer>
	</footer>
</body>
</html>';

$admin_template = '<!doctype html>
<html class="page active" lang="en">
	<head>
	<meta charset="utf-8">
	<title>Create new page - Spage</title>
	<link rel="stylesheet" href="style.css">
	<script type="text/javascript" src="lib/showdown.js"></script>
	<script type="text/javascript" src="lib/showdown-gui.js"></script>
</head>
<body>
	<header>
		<h1>Spage</h1>
		<ul>
			<li><a href="spage.php">Admin front page</a></li>
			<li><a href="spage.php?rebuild_pages=1">Rebuild all pages</a></li>
			<li><a href="spage.php?create_page_list=1">Create/update index.html</a></li>
			<li><a href="spage.php?edit_page=1">Edit page</a></li>
		</ul>
		{{message}}
	</header>
	<form action="spage.php" method="post">
		<fieldset>
			<legend>Add new page</legend>
			<label for="url">Filename for the page</label>
			<input name="url" id="url" required="required">
			<label for="title">Title for the page</label>
			<input name="title" id="title" required="required">
			<label for="content">Content</label>
			<div id="leftContainer">
				<div class="paneHeader">
					<span>Input</span>
				</div>
				<textarea id="inputPane" name="content" rows="20" required="required" class="pane"></textarea>
			</div>
		</fieldset>
		<fieldset>
			<button type="submit">Submit</button>
		</fieldset>
	</form>
	<select id="paneSetting">
		<option value="previewPane">Preview</option>
		<option value="outputPane">HTML Output</option>
		<option value="syntaxPane">Syntax Guide</option>
	</select>
	<textarea id="outputPane" class="pane" rows="20" readonly="readonly"></textarea>
	<div id="previewPane" class="pane"></div>
	<textarea id="syntaxPane" class="pane" rows="20" readonly="readonly">Visit http://daringfireball.net/projects/markdown/syntax</textarea>
	<footer id="footer">
		<span id="convertTextControls">
			<button id="convertTextButton" type="button" title="Convert text now">
				Convert text
			</button>
			<select id="convertTextSetting">
				<option value="delayed">in the background</option>
				<option value="continuous">every keystroke</option>
				<option value="manual">manually</option>
			</select>
		</span>
		<div id="processingTime" title="Last processing time">0 ms</div>
	</footer>
</body>
</html>';

$admin_edit_template = '<!doctype html>
<html class="page active" lang="en">
	<head>
	<meta charset="utf-8">
	<title>Edit page - Spage</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>
	<header>
		<h1>Spage</h1>
		<ul>
			<li><a href="spage.php">Admin front page</a></li>
			<li><a href="spage.php?rebuild_pages=1">Rebuild all pages</a></li>
			<li><a href="spage.php?create_page_list=1">Create/update index.html</a></li>
			<li><a href="spage.php?edit_page=1">Edit page</a></li>
		</ul>
		{{message}}
	</header>
	<form action="spage.php" method="post">
		<fieldset>
			<legend>Edit existing page</legend>
			<label for="url">Filename for the page</label>
			<input name="url" id="url" value="{{url}}" required="required">
			<label for="title">Title for the page</label>
			<input name="title" id="title" value="{{title}}" required="required">
			<input name="overwrite" id="overwrite" value="true" type="hidden">
			<input name="date" id="date" value="{{date}}" type="hidden">
			<input name="time" id="time" value="{{time}}" type="hidden">
			<input name="timestamp" id="timestamp" value="{{timestamp}}" type="hidden">
			<label for="content">Content</label>
			<textarea id="content" name="content" rows="20" required="required">{{content}}</textarea>
		</fieldset>
		<fieldset>
			<button type="submit">Submit</button>
		</fieldset>
	</form>
	Or <a href="spage.php?delete_page={{url}}.txt">delete</a> the page.
	<footer>
	</footer>
</body>
</html>';

$admin_page_list_template = '<!doctype html>
<html class="page active" lang="en">
	<head>
	<meta charset="utf-8">
	<title>Edit page - Spage</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>
	<header>
		<h1>Spage</h1>
		<ul>
			<li><a href="spage.php">Admin front page</a></li>
			<li><a href="spage.php?rebuild_pages=1">Rebuild all pages</a></li>
			<li><a href="spage.php?create_page_list=1">Create/update index.html</a></li>
			<li><a href="spage.php?edit_page=1">Edit page</a></li>
		</ul>
		{{message}}
	</header>
	<form action="spage.php" method="get">
		<fieldset>
			<legend>Edit existing page</legend>
			<input name="edit_page" id="edit_page" value="1" type="hidden">
			<label for="url">Select page</label>
			<select name="page">
				{{#pages}}
				<option value="{{url}}">{{title}} ({{url}})</option>
				{{/pages}}
			</select> 
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