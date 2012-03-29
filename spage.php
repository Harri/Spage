<?php

/**
 * Simple static file page creation system
 *
 * {@link https://github.com/Harri/Spage}
 *
 * Spage does not need database, it lets web servers do what they do best;
 * serve flat files.
 *
 * @author Harri Paavola {@link https://plus.google.com/108215938928178695462/about}
 */

require('lib/markdown.php');
require('lib/mustache.php');
require('templates.php');

define('SPAGE_VERSION',  "1.0" ); # 2012-03-28

class Spage {	
	/**
	* Adds new page and updates RSS feed. Does not overwrite existing files, unless told so.
	* 
	* @access public
	* @param array $data
	* @param string $template
	* @param bool $overwrite (default: FALSE)
	* @return int
	*/
	public function add_new_page($data, $template, $overwrite=FALSE) {
		if (!$overwrite) {
			$data['content_html'] = Markdown($data['content']);
			$data['url'] = $data['url'];
			$data['date'] = date('Y-m-d');
			$data['time'] = date('H:i');
			$data['timestamp'] = time();
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
			$this->create_rss_feed($GLOBALS['rss_template']);
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
			$data['content_html'] .= '<li><a href="'.$page['url'].'.html">'.$page['title'].'</a> ('.$page['date'].')</li>';
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
				$this->add_new_page($data, $template, TRUE);
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
	* Moves page (*.html and *.txt) to trash/ directory.
	* 
	* @access public
	* @param string $page
	* @return void
	*/
	public function delete_page($page) {
		if (!strpbrk($page, '/\\') and $this->ends_with($page, '.txt')) {
			rename($page, 'trash/'.$page);
			$page = str_replace('.txt', '.html', $page);
			rename($page, 'trash/'.$page);
		}
	}
	
	/**
	* Creates (and updates) RSS page. Returns 0 if everything went ok.
	* 
	* @access public
	* @return int
	*/
	public function create_rss_feed($template) {
		$dir = scandir('.');
		$page_list = array();
		
		foreach ($dir as $name) {
			if ($this->ends_with($name, '.txt')) {
				$page_list[] = unserialize(file_get_contents($name));
			}
		}
		$page_list = $this->aasort($page_list, 'timestamp');
		$page_list = array_reverse($page_list);;
		array_splice($page_list, 5);
		$data = array('pages' => $page_list);
		if (!$file_handle = fopen('rss.xml', 'w')) {
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
	
	/**
	* Helper function to sort multidimensional array. Returns given array in sorted order
	* 
	* @access public
	* @param array $array
	* @param string $key
	* @return array
	*/
	function aasort (&$array, $key) {
		$sorter=array();
		$ret=array();
		reset($array);
		foreach ($array as $ii => $va) {
			$sorter[$ii]=$va[$key];
		}
		asort($sorter);
		foreach ($sorter as $ii => $va) {
			$ret[$ii]=$array[$ii];
		}
		return $ret;
	}

	
}

$page = new Spage;
$m = new Mustache;

if (isset($_POST['url'], $_POST['title'], $_POST['content'])) {
	/* Creates new page. Also page edit form POSTs here.*/
	$data = array('url' => urlencode($_POST['url']), 'title' => htmlspecialchars($_POST['title']), 'content' => $_POST['content']);

	if (isset($_POST['overwrite']) and $_POST['overwrite']=="true") {
		if (isset($_POST['date'], $_POST['time'])) {
			$data['date'] = htmlspecialchars($_POST['date']);
			$data['time'] = htmlspecialchars($_POST['time']);
			$data['timestamp'] = htmlspecialchars($_POST['timestamp']);
		}
		$overwrite = TRUE;
	}
	else {
		$overwrite = FALSE;
	}
	$error_code = $page->add_new_page($data, $default_template, $overwrite);
	
	if ($error_code===1) {
		$message = 'Something went wrong. Maybe the page exists already?';
	}
	else {
		$message = 'Page created.';
	}
	echo $m->render($admin_template, array('message' => $message));
}
else if (isset($_GET['create_page_list'])) {
	/* Creates index.html with list of pages */
	$dir = scandir('.');
	$page_list = array();
	
	foreach ($dir as $name) {
		if ($page->ends_with($name, '.txt')) {
			$page_list[] = unserialize(file_get_contents($name));
		}
	}
	
	$page_list = $page->aasort($page_list, 'timestamp');
	$page_list = array_reverse($page_list);;
	
	$data = array('content' => $page_list);
	$page->create_page_list($data, $page_list_template);
	echo $m->render($admin_template, array('message' => 'Page list created.'));
}
else if (isset($_GET['rebuild_pages'])) {
	/* Rebuilds given page. Can be used for example after changing page template */
	$page->rebuild_pages($default_template);
	echo $m->render($admin_template, array('message' => 'Rebuilt pages.'));
}
else if (isset($_GET['edit_page'])) {
	/* Used for editing given page. If 'page' query parmater is set
		admin_edit_template is shown. Othervise admin_page_list_template is used. */
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
else if (isset($_GET['delete_page'])) {
	/* Deletes given page */
	$page->delete_page($_GET['delete_page']);
	echo $m->render($admin_template, array('message' => 'Page deleted.'));
}
else {
	/* Shows the default view. */
	echo $m->render($admin_template, array());
}


/*

Copyright (C) 2012 Harri Paavola

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
"Software"), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be included
in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

*/