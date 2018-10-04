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
<body id="admin">
	<header>
		<h1>Spage</h1>
		<ul>
			<li><a href="spage.php">Admin front page</a></li>
			<li><a href="spage.php?operation=rebuild_pages">Rebuild all pages</a></li>
			<li><a href="spage.php?operation=edit_front_page">Create/update index.html</a></li>
			<li><a href="spage.php?operation=list_pages">Edit page</a></li>
			<li><a href="spage.php?operation=list_comments">Moderate comments</a></li>
			<li><a href="spage.php?operation=list_comments_in_queue">Moderate comments in queue</a></li>
		</ul>
		{{message}}
	</header>
	<form action="spage.php" method="post">
		<fieldset>
			<legend>Add new page</legend>
			<input name="url" id="url" value="{{url}}" required="required">
			<label for="title">Title for the page</label>
			<input name="title" id="title" value="{{title}}" required="required">
			<input name="operation" id="operation" value="create_page" type="hidden">
			<label for="inputPane">Content</label>
			<textarea id="inputPane" name="content" rows="20" required="required" class="pane">{{content}}</textarea>
		</fieldset>
		<fieldset>
			<button type="submit">Submit</button>
			<input type="checkbox" id="draft" name="draft" value="draft"><label for="draft">Save as draft</label>
			<input type="checkbox" id="unlisted" name="unlisted" value="unlisted"><label for="unlisted">Save as unlisted</label>
			<input type="radio" id="allow_comments" name="allow_comments" value="allow_comments"><label for="allow_comments">Allow comments</label>
			<input type="radio" id="moderate_comments" name="allow_comments" value="moderate_comments"><label for="moderate_comments">Moderate comments</label>
			<input type="radio" id="disallow_comments" name="allow_comments" value="disallow_comments" checked="checked"><label for="disallow_comments">Disallow comments</label>

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

$admin_list_comments_template = '<!doctype html>
<html class="page active" lang="en">
	<head>
	<meta charset="utf-8">
	<title>Create new page - Spage</title>
	<link rel="stylesheet" href="style.css">
	<script type="text/javascript" src="lib/showdown.js"></script>
	<script type="text/javascript" src="lib/showdown-gui.js"></script>
</head>
<body id="admin">
	<header>
		<h1>Spage</h1>
		<ul>
			<li><a href="spage.php">Admin front page</a></li>
			<li><a href="spage.php?operation=rebuild_pages">Rebuild all pages</a></li>
			<li><a href="spage.php?operation=edit_front_page">Create/update index.html</a></li>
			<li><a href="spage.php?operation=list_pages">Edit page</a></li>
			<li><a href="spage.php?operation=list_comments">Moderate comments</a></li>
			<li><a href="spage.php?operation=list_comments_in_queue">Moderate comments in queue</a></li>
		</ul>
		{{{message}}}
	</header>
	<form action="spage.php" method="post">
		<fieldset>
			<legend>Delete comments</legend>
			<input name="operation" id="operation" value="delete_comments" type="hidden">
			{{#comments}}
			<div class="comment">
				<p>Page: <a href="{{url}}" target="_blank">{{url}}</a></p>
				<input type="checkbox" id="{{uuid}}" name="{{uuid}}" value="delete">
				<label for="{{uuid}}">{{{comment}}}, {{{author}}}, {{uuid}}</label>
			</div>
			{{/comments}}
		</fieldset>
		<fieldset>
			<button type="submit">Submit</button>
		</fieldset>
	</form>
	<footer id="footer">
	</footer>
</body>
</html>';

$admin_comment_queue_template = '<!doctype html>
<html class="page active" lang="en">
	<head>
	<meta charset="utf-8">
	<title>Create new page - Spage</title>
	<link rel="stylesheet" href="style.css">
	<script type="text/javascript" src="lib/showdown.js"></script>
	<script type="text/javascript" src="lib/showdown-gui.js"></script>
</head>
<body id="admin">
	<header>
		<h1>Spage</h1>
		<ul>
			<li><a href="spage.php">Admin front page</a></li>
			<li><a href="spage.php?operation=rebuild_pages">Rebuild all pages</a></li>
			<li><a href="spage.php?operation=edit_front_page">Create/update index.html</a></li>
			<li><a href="spage.php?operation=list_pages">Edit page</a></li>
			<li><a href="spage.php?operation=list_comments">Moderate comments</a></li>
			<li><a href="spage.php?operation=list_comments_in_queue">Moderate comments in queue</a></li>
		</ul>
		{{{message}}}
	</header>
	<form action="spage.php" method="post">
		<fieldset>
			<legend>Moderate comments in queue</legend>
			<input name="operation" id="operation" value="moderate_comments" type="hidden">
			{{#comment_queue}}
			<div class="comment">
				<p>Page: <a href="{{url}}" target="_blank">{{url}}</a></p>
				<input type="radio" id="{{uuid}}_del" name="{{uuid}}" value="delete">
				<label for="{{uuid}}_del">Delete</label>
				<input type="radio" id="{{uuid}}_pub" name="{{uuid}}" value="publish">
				<label for="{{uuid}}_pub">Publish</label>
				<div>{{{comment}}}, {{{author}}}, {{uuid}}</div>
			</div>
			{{/comment_queue}}
		</fieldset>
		<fieldset>
			<button type="submit">Submit</button>
		</fieldset>
	</form>
	<footer id="footer">
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
<body id="admin">
	<header>
		<h1>Spage</h1>
		<ul>
			<li><a href="spage.php">Admin front page</a></li>
			<li><a href="spage.php?operation=rebuild_pages">Rebuild all pages</a></li>
			<li><a href="spage.php?operation=edit_front_page">Create/update index.html</a></li>
			<li><a href="spage.php?operation=list_pages">Edit page</a></li>
			<li><a href="spage.php?operation=list_comments">Moderate comments</a></li>
			<li><a href="spage.php?operation=list_comments_in_queue">Moderate comments in queue</a></li>
		</ul>
		{{message}}
	</header>
	<form action="spage.php" method="post">
		<fieldset>
			<legend>Edit existing page</legend>
			<label for="url">Filename for the page</label>
			<input name="url" id="url" value="{{url}}" required="required" readonly="readonly">
			<label for="title">Title for the page</label>
			<input name="title" id="title" value="{{title}}" required="required">
			<input name="date" id="date" value="{{date}}" type="hidden">
			<input name="time" id="time" value="{{time}}" type="hidden">
			<input name="timestamp" id="timestamp" value="{{timestamp}}" type="hidden">
			<input name="operation" id="operation" value="save_page" type="hidden">
			<input name="overwrite" id="overwrite" value="TRUE" type="hidden">
			<label for="content">Content</label>
			<textarea id="inputPane" name="content" rows="20" required="required" class="pane">{{content}}</textarea>
		</fieldset>
		<fieldset>
			<button type="submit">Submit</button>
			<input type="checkbox" id="draft" name="draft" value="draft" {{draft_checked}}><label for="draft">Save as draft</label>
			<input type="checkbox" id="unlisted" name="unlisted" value="unlisted" {{unlisted_checked}}><label for="unlisted">Save as unlisted</label>
			<input type="radio" id="allow_comments" name="allow_comments" value="allow_comments" {{allow_comments_checked}}><label for="allow_comments">Allow comments</label>
			<input type="radio" id="moderate_comments" name="allow_comments" value="moderate_comments" {{moderate_comments_checked}}><label for="moderate_comments">Moderate comments</label>
			<input type="radio" id="disallow_comments" name="allow_comments" value="disallow_comments" {{disallow_comments_checked}}><label for="disallow_comments">Disallow comments</label>
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
	<form action="spage.php" method="post">
		<fieldset>
			<legend>Select old version of the page</legend>
			<input name="operation" id="operation" value="history" type="hidden">
			<input name="url" id="url" value="{{url}}" type="hidden">
			<select name="history_id">
				{{#history}}
					<option value="{{history_id}}">Archived: {{archived}}</option>
				{{/history}}
			</select>
		</fieldset>
		<fieldset>
			<button type="submit">Submit</button>
		</fieldset>
	</form>
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
		Or <a href="spage.php?operation=delete_page&page={{url}}">delete</a> the page.
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
<body id="admin">
	<header>
		<h1>Spage</h1>
		<ul>
			<li><a href="spage.php">Admin front page</a></li>
			<li><a href="spage.php?operation=rebuild_pages">Rebuild all pages</a></li>
			<li><a href="spage.php?operation=edit_front_page">Create/update index.html</a></li>
			<li><a href="spage.php?operation=list_pages">Edit page</a></li>
			<li><a href="spage.php?operation=list_comments">Moderate comments</a></li>
			<li><a href="spage.php?operation=list_comments_in_queue">Moderate comments in queue</a></li>
		</ul>
		{{message}}
	</header>
	<form action="spage.php" method="get">
		<fieldset>
			<legend>Edit existing page</legend>
			<input name="operation" id="operation" value="edit_page" type="hidden">
			<label for="url">Select page</label>
			<select name="page">
				{{#all_pages}}
					<optgroup label="Published">
					{{#pages}}
						<option value="{{url}}">{{title}} ({{url}})</option>
					{{/pages}}
					</optgroup>
					<optgroup label="Unlisted">
					{{#unlisted}}
						<option value="{{url}}">{{title}} ({{url}})</option>
					{{/unlisted}}
					</optgroup>
					<optgroup label="Drafts">
					{{#drafts}}
						<option value="{{url}}">{{title}} ({{url}})</option>
					{{/drafts}}
					</optgroup>
				{{/all_pages}}
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
<body id="admin">
	<header>
		<h1>Spage</h1>
		<ul>
			<li><a href="spage.php">Admin front page</a></li>
			<li><a href="spage.php?operation=rebuild_pages">Rebuild all pages</a></li>
			<li><a href="spage.php?operation=edit_front_page">Create/update index.html</a></li>
			<li><a href="spage.php?operation=list_pages">Edit page</a></li>
			<li><a href="spage.php?operation=list_comments">Moderate comments</a></li>
			<li><a href="spage.php?operation=list_comments_in_queue">Moderate comments in queue</a></li>
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

$admin_continue_template = '<!doctype html>
<html lang="en">
	<head>
	<meta charset="utf-8">
	<title>Continue editing page - Spage</title>
	<link rel="stylesheet" href="style.css">
	<script type="text/javascript" src="lib/showdown.js"></script>
	<script type="text/javascript" src="lib/showdown-gui.js"></script>
</head>
<body id="admin">
	<header>
		<h1>Spage</h1>
		<ul>
			<li><a href="spage.php">Admin front page</a></li>
			<li><a href="spage.php?operation=rebuild_pages">Rebuild all pages</a></li>
			<li><a href="spage.php?operation=edit_front_page">Create/update index.html</a></li>
			<li><a href="spage.php?operation=list_pages">Edit page</a></li>
			<li><a href="spage.php?operation=list_comments">Moderate comments</a></li>
			<li><a href="spage.php?operation=list_comments_in_queue">Moderate comments in queue</a></li>
		</ul>
		{{message}}
	</header>
	<form action="spage.php" method="post">
		<fieldset>
			<legend>Add new page</legend>
			<label for="url">Filename for the page</label>
			<input name="url" id="url" value="{{url}}" required="required" readonly="readonly">
			<label for="title">Title for the page</label>
			<input name="title" id="title" value="{{title}}" required="required">
			<input name="date" id="date" value="{{date}}" type="hidden">
			<input name="time" id="time" value="{{time}}" type="hidden">
			<input name="timestamp" id="timestamp" value="{{timestamp}}" type="hidden">
			<input name="overwrite" id="overwrite" value="TRUE" type="hidden">
			<input name="operation" id="operation" value="save_page" type="hidden">
			<label for="inputPane">Content</label>
			<textarea id="inputPane" name="content" rows="20" required="required" class="pane">{{content}}</textarea>
		</fieldset>
		<fieldset>
			<button type="submit">Submit</button>
			<input type="checkbox" id="draft" name="draft" value="draft" {{draft_checked}}><label for="draft">Save as draft</label>
			<input type="checkbox" id="unlisted" name="unlisted" value="unlisted" {{unlisted_checked}}><label for="unlisted">Save as unlisted</label>
			<input type="radio" id="allow_comments" name="allow_comments" value="allow_comments"><label for="allow_comments">Allow comments</label>
			<input type="radio" id="moderate_comments" name="allow_comments" value="moderate_comments"><label for="moderate_comments">Moderate comments</label>
			<input type="radio" id="disallow_comments" name="allow_comments" value="disallow_comments" checked="checked"><label for="disallow_comments">Disallow comments</label>
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
	<form action="spage.php" method="post">
		<fieldset>
			<legend>Select old version of the page</legend>
			<input name="operation" id="operation" value="history" type="hidden">
			<input name="url" id="url" value="{{url}}" type="hidden">
			<select name="history_id">
				{{#history}}
					<option value="{{history_id}}">Archived: {{archived}}</option>
				{{/history}}
			</select>
		</fieldset>
		<fieldset>
			<button type="submit">Submit</button>
		</fieldset>
	</form>
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
