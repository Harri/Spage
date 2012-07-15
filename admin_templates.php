<?php
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
			<li><a href="spage.php?operation=rebuild_pages">Rebuild all pages</a></li>
			<li><a href="spage.php?operation=edit_front_page">Create/update index.html</a></li>
			<li><a href="spage.php?operation=list_pages">Edit page</a></li>
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
			<input name="operation" id="operation" value="create_page" type="hidden">
			<label for="inputPane">Content</label>
			<textarea id="inputPane" name="content" rows="20" required="required" class="pane"></textarea>
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
	<script type="text/javascript" src="lib/showdown.js"></script>
	<script type="text/javascript" src="lib/showdown-gui.js"></script>
</head>
<body>
	<header>
		<h1>Spage</h1>
		<ul>
			<li><a href="spage.php">Admin front page</a></li>
			<li><a href="spage.php?operation=rebuild_pages">Rebuild all pages</a></li>
			<li><a href="spage.php?operation=edit_front_page">Create/update index.html</a></li>
			<li><a href="spage.php?operation=list_pages">Edit page</a></li>
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
			<input name="date" id="date" value="{{date}}" type="hidden">
			<input name="time" id="time" value="{{time}}" type="hidden">
			<input name="timestamp" id="timestamp" value="{{timestamp}}" type="hidden">
			<input name="operation" id="operation" value="create_page" type="hidden">
			<input name="overwrite" id="overwrite" value="TRUE" type="hidden">
			<label for="content">Content</label>
			<textarea id="inputPane" name="content" rows="20" required="required" class="pane">{{content}}</textarea>
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
		Or <a href="spage.php?operation=delete_page&page={{url}}.txt">delete</a> the page.
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
			<li><a href="spage.php?operation=rebuild_pages">Rebuild all pages</a></li>
			<li><a href="spage.php?operation=edit_front_page">Create/update index.html</a></li>
			<li><a href="spage.php?operation=list_pages">Edit page</a></li>
		</ul>
		{{message}}
	</header>
	<form action="spage.php" method="get">
		<fieldset>
			<legend>Edit existing page</legend>
			<input name="operation" id="operation" value="edit_page" type="hidden">
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

$admin_front_page_template = '<!doctype html>
<html class="page active" lang="en">
	<head>
	<meta charset="utf-8">
	<title>Edit front page - Spage</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>
	<header>
		<h1>Spage</h1>
		<ul>
			<li><a href="spage.php">Admin front page</a></li>
			<li><a href="spage.php?operation=rebuild_pages">Rebuild all pages</a></li>
			<li><a href="spage.php?operation=edit_front_page">Create/update index.html</a></li>
			<li><a href="spage.php?operation=list_pages">Edit page</a></li>
		</ul>
		{{message}}
	</header>
	<form action="spage.php" method="post">
		<fieldset>
			<legend>Edit front page</legend>
			<input name="operation" id="operation" value="create_front_page" type="hidden">
			<label for="front_page_content">Content</label>
			<textarea id="front_page_content" name="front_page_content" rows="20" required="required">{{front_page_content}}</textarea>
		</fieldset>
		<fieldset>
			<button type="submit">Submit</button>
		</fieldset>
	</form>
	<footer>
	</footer>
</body>
</html>';