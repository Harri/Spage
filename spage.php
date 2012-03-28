<?php

/**
 * Simple static file page creation system
 *
 * {@link http://artturi.org/spage/}
 *
 * Spage does not need database, it lets web servers do what they do best;
 * serve flat files. Depends on Markdown and Mustache.
 *
 * @author Harri Paavola {@link http://artturi.org/}
 */

require('markdown.php');
require('mustache.php');
require('templates.php');

define('SPAGE_VERSION',  "1.0" ); # 2012-03-28

class Spage {	
	/**
	* Adds new page. Does not overwrite existing files, unless told so.
	* 
	* @access public
	* @param array $data
	* @param string $template
	* @param bool $overwrite (default: false)
	* @return int
	*/
	public function add_new_page($data, $template, $overwrite=false) {
		if (!$overwrite) {
			$data['content_html'] = Markdown($data['content']);
			$data['url'] = $data['url'];
			$data['date'] = date('Y-m-d');
			$data['time'] = date('H:i');
		}
		else {
			$data['content_html'] = Markdown($data['content']);
			$data['date_edited'] = date('Y-m-d');
			$data['time_edited'] = date('H:i');
		}

		if (!$overwrite and file_exists($data['url'].'.html') or !$file_handle = fopen($data['url'].'.html', 'w') or !$plain_file_handle = fopen($data['url'].'.txt', 'w')) {
			return 1;
		}
		else {	
			$m = new Mustache;
			fwrite($file_handle, $m->render($template, $data));
			fclose($file_handle);
			fwrite($plain_file_handle, serialize($data));
			fclose($plain_file_handle);			
			return 0;
		}
	}
	
	/**
	* Creates list of html pages in current directory.
	* 
	* @access public
	* @param array $data
	* @param string $template
	* @return int
	*/
	public function create_page_list($data, $template) {
		$data['content_html'] = '<ol>';
		foreach ($data['content'] as $page) {
			$data['content_html'] .= '<li><a href="'.$page[2].'">'.$page[1].'</a> ('.$page[0].')</li>';
		}
		$data['content_html'] .= '</ol>';
		
		if (!$file_handle = fopen('index.html', 'w')) {
			return 1;
		}
		else {
			$m = new Mustache;
			fwrite($file_handle, $m->render($template, $data));
			fclose($file_handle);
			return 0;
		}			
	}
	
	/**
	* Rebuilds all pages in current directory to match new template.
	* 
	* @access public
	* @param string $template
	* @return void
	*/
	public function rebuild_pages($template) {
		$dir = scandir('.');
		$page_list = array();
		
		foreach ($dir as $name) {
			if ($this->ends_with($name, '.txt')) {
				$data = unserialize(file_get_contents($name));
				$this->add_new_page($data, $template, true);
			}
		}
	}

	/**
	* Gets given page. Requested page name must not contain / or \
	* and it must end with .txt
	* 
	* @access public
	* @param string $page
	* @return array
	*/
	public function get_page($page) {
		if (!strpbrk($page, '/\\') and $this->ends_with($page, '.txt')) {
			$data = unserialize(file_get_contents($page));
			return $data;
		}
		return array();
	}

	/**
	* Helper function to verify if string ends with given substring
	* 
	* @access public
	* @param string $haystack
	* @param string $needle
	* @return string
	*/
	public function ends_with($haystack, $needle) {
		$length = strlen($needle);
		$start  = $length * -1;
		return (substr($haystack, $start) === $needle);
	}
	
}

$page = new Spage;
$m = new Mustache;

/* Data is POSTed here when creating new pages and when editing existing ones. */
if (isset($_POST['url'], $_POST['title'], $_POST['content'])) {
	
	$data = array(	'url' 		=> urlencode($_POST['url']),
					'title' 	=> htmlspecialchars($_POST['title']),
					'content'	=> $_POST['content']);

	if (isset($_POST['overwrite']) and $_POST['overwrite']=="true") {
		if (isset($_POST['date'], $_POST['time'])) {
			$data['date'] = htmlspecialchars($_POST['date']);
			$data['time'] = htmlspecialchars($_POST['time']);
		}
		$error_code = $page->add_new_page($data, $default_template, true);
	}
	else {
		$error_code = $page->add_new_page($data, $default_template);
	}
	
	if ($error_code===1) {
		echo $m->render($admin_template, array('admin_title' => 'Create new page', 'message' => 'Something went wrong. Maybe the page exists already?.'));
	}
	else {
		echo $m->render($admin_template, array('admin_title' => 'Create new page', 'message' => 'Page created.'));
	}
}
else if (isset($_GET['create_page_list'])) {
	$dir = scandir('.');
	$page_list = array();
	
	// Goes through all files in current dir.
	// If file name ends with .html and is not index.html,
	// gets title tag content and creation date from span element.
	// Creates list with all post names, post file names and creation dates.
	foreach ($dir as $name) {
		if ($page->ends_with($name, '.html') and $name != 'index.html') {
			$file_content = file_get_contents($name);
			
			$real_name = explode('<title>', $file_content);
			$real_name = explode('</title>', $real_name[1]);
			
			$date = explode('<span id="page_creation_date">', $file_content);
			$date = explode('</span>', $date[1]);
			
			$page_list[] = array($date[0], $real_name[0], $name);
		}
	}
	
	$data = array('content' => $page_list);
	
	$page->create_page_list($data, $page_list_template);
	echo $m->render($admin_template, array('admin_title' => 'Create new page', 'message' => 'Page list created.'));
}
else if (isset($_GET['rebuild_pages'])) {
	$page->rebuild_pages($default_template);
	echo $m->render($admin_template, array('admin_title' => 'Create new page', 'message' => 'Rebuilt pages.'));
}
else if (isset($_GET['edit_page'])) {
	if (isset($_GET['page'])) {
		$data = $page->get_page(htmlspecialchars($_GET['page']));
		echo $m->render($admin_edit_template, $data);
	}
	else {
		$dir = scandir('.');
		$pages = array();

		foreach ($dir as $name) {
			if ($page->ends_with($name, '.txt')) {
				$content = unserialize(file_get_contents($name));
				$content['url'] .= ".txt";
				$pages[] = $content;
			}
		}
		$data['pages'] = $pages;
		echo $m->render($admin_page_list_template, $data);
	}
}
else {
	echo $m->render($admin_template, array());
}