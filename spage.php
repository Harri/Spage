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
require('admin_templates.php');

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
		$data['url'] = urlencode($data['url']);
		$data['title'] = htmlspecialchars($data['title']);
		$data['content_html'] = Markdown($data['content']);
		
		if ($overwrite) {
			$data['date'] = htmlspecialchars($data['date']);
			$data['time'] = htmlspecialchars($data['time']);
			$data['timestamp'] = htmlspecialchars($data['timestamp']);
			$data['date_edited'] = date('Y-m-d');
			$data['time_edited'] = date('H:i');
		}
		else {
			$data['date'] = date('Y-m-d');
			$data['time'] = date('H:i');
			$data['timestamp'] = time();
		}
		
		if (!$file_handle = fopen($data['url'].'.html', 'w') or !$plain_file_handle = fopen($data['url'].'.txt', 'w')) {
			if ($overwrite and file_exists($data['url'].'.html')) {
				return 1;
			}
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
	* Creates or updates front page
	* 
	* @access public
	* @param array $data
	* @param string $template
	* @return int
	*/
	public function create_front_page($data, $template) {
		$dir = scandir('.');
		$page_list = array();
	
		foreach ($dir as $name) {
			if ($this->ends_with($name, '.txt') and $name != 'index.txt') {
				$page_list[] = unserialize(file_get_contents($name));
			}
		}
		$page_list = $this->aasort($page_list, 'timestamp');
		$page_list = array_reverse($page_list);

		$data['page_list'] = $page_list;
		$data['content_html'] = Markdown($data['front_page_content']);
		
		if (!$file_handle = fopen('index.html', 'w') or !$plain_file_handle = fopen('index.txt', 'w')) {
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
			if ($this->ends_with($name, '.txt') and $name != 'index.txt') {
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
		if (!strpbrk($page, '/\\') and $this->ends_with($page, '.txt') and is_file($page)) {
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
		if (!strpbrk($page, '/\\') and $this->ends_with($page, '.txt') and is_file($page)) {
			rename($page, 'trash/'.$page);
			$page = str_replace('.txt', '.html', $page);
			rename($page, 'trash/'.$page);
		}
	}
	
	/**
	* Creates (and updates) RSS page. Returns 0 if everything went ok.
	* 
	* @access public
	* @param string $template
	* @param int $number_of_items (default: 5)
	* @return int 
	*/
	public function create_rss_feed($template, $number_of_items=5) {
		$dir = scandir('.');
		$page_list = array();
		
		foreach ($dir as $name) {
			if ($this->ends_with($name, '.txt') and $name != 'index.txt') {
				$page_list[] = unserialize(file_get_contents($name));
			}
		}
		$page_list = $this->aasort($page_list, 'timestamp');
		$page_list = array_reverse($page_list);
		array_splice($page_list, $number_of_items);
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
	* Gets all pages
	* 
	* @access public
	* @return array
	*/
	public function list_all_pages() {
		$dir = scandir('.');
		$pages = array();

		foreach ($dir as $name) {
			if ($this->ends_with($name, '.txt') and $name != 'index.txt') {
				$content = unserialize(file_get_contents($name));
				$content['url'] .= ".txt";
				$pages[] = $content;
			}
		}
		return $pages;
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

if (!isset($_REQUEST['operation'])) {
	$_REQUEST['operation'] = '';
}

switch ($_REQUEST['operation']) {
	case 'create_page':
		// Page creation and editing
		$error_code = $page->add_new_page($_POST, $default_template, isset($_POST['overwrite']));
		if ($error_code===1) {
			$message = 'Something went wrong. Maybe the page exists already?';
		}
		else {
			$message = 'Page created.';
		}
		echo $m->render($admin_template, array('message' => $message));
		break;
	case 'edit_front_page':
		$data = $page->get_page('index.txt');
		echo $m->render($admin_front_page_template, $data);
		break;
	case 'create_front_page':
		$page->create_front_page($_POST, $front_page_template);
		echo $m->render($admin_template, array('message' => 'Front page created.'));	
		break;
	case 'rebuild_pages':
		$page->rebuild_pages($default_template);
		echo $m->render($admin_template, array('message' => 'Rebuilt pages.'));
		break;
	case 'list_pages':
		$data['pages'] = $page->list_all_pages();
		echo $m->render($admin_page_list_template, $data);
		break;
	case 'edit_page':
		$data = $page->get_page(urlencode($_GET['page']));
		echo $m->render($admin_edit_template, $data);
		break;
	case 'delete_page':
		$page->delete_page(urlencode($_GET['page']));
		echo $m->render($admin_template, array('message' => 'Page deleted.'));
		break;
	default:
		echo $m->render($admin_template, array());
		break;
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