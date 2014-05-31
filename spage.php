<?php

/**
 * Simple static file page creation system
 *
 * Spage does not need database, it lets web servers do what they do best;
 * serve flat files.
 *
 * @author Harri Paavola, harri.paavola@gmail.com
 * @link https://github.com/Harri/Spage
 */

require 'lib/markdown.php';
require 'lib/mustache.php';
require 'templates.php';
require 'admin_templates.php';

date_default_timezone_set('Europe/Helsinki');

$m = new Mustache_Engine;

class Spage {

  const TRASH_DIR = 'trash';
  const RSS_ITEMS = 5;
  const FRONT_ITEMS = 5;
  const DATA_EXT = '.spage';
  const PAGE_EXT = '.html';

  /**
  * Adds new page and updates RSS feed.
  * Does not overwrite existing files, unless told so.
  *
  * Each page has the following data
  *   url - it's the same as file name, excluding file extension
  *   title - title for the page
  *   content - exactly the content that was written
  *   content_html - same as content, but went through Markdown
  *   date - date of creation YYYY-MM-DD
  *   time - time of creation HH:MM
  *   timestamp - same as date and time, but in UNIX time
  *
  * Edited pages have also the following data
  *   overwrite - always TRUE
  *   date_edited - date when edited
  *   time_edited - time when edited
  *   timestamp_edited - same as date and time, but in UNIX time
  *
  * Each page might have any of the following data
  *    allow_comments - If set, uses template with comments and comment form
  *    draft - If set, no *.html file is created
  *    unlisted - If set, the page is not shown in RSS, page list or search
  *
  *
  * @access public
  * @param array $data
  * @param string $template
  * @param bool $overwrite (default: FALSE)
  * @return int
  */
  public function add_new_page($data, $template, $overwrite=FALSE) {
    $data['url'] = mb_substr($data['url'], 0, 200);
    $data['url'] = urlencode($data['url']);
    $data['title'] = htmlspecialchars($data['title']);
    $data['content_html'] = Markdown::defaultTransform($data['content']);

    if ($overwrite) {
      $data['date'] = htmlspecialchars($data['date']);
      $data['time'] = htmlspecialchars($data['time']);
      $data['timestamp'] = htmlspecialchars($data['timestamp']);
      $data['date_edited'] = date('Y-m-d');
      $data['time_edited'] = date('H:i');
      $data['timestamp_edited'] = time();
    }
    else {
      $data['date'] = date('Y-m-d');
      $data['time'] = date('H:i');
      $data['timestamp'] = time();
    }

    // only edits can overwrite
    if (file_exists($data['url'].self::PAGE_EXT) && $overwrite!==TRUE) {
      return 1;
    }
    else {
      $error_code = $this->write_to_file(
        $data['url'].self::DATA_EXT,
        serialize($data)
      );
      $error_code_2 = 0;
      // drafts don't get .html page
      if (!isset($data['draft'])) {
        $error_code_2 = $this->write_to_file(
          $data['url'].self::PAGE_EXT,
          $GLOBALS['m']->render($template, $data)
        );
      }
      // When moving page from non-draft to draft, delete the *.html
      else if (
        isset($data['draft']) &&
        file_exists($data['url'].self::PAGE_EXT)
      ) {
        rename(
          dirname(__FILE__).DIRECTORY_SEPARATOR.$data['url'].self::PAGE_EXT,
          dirname(__FILE__).DIRECTORY_SEPARATOR.self::TRASH_DIR.DIRECTORY_SEPARATOR.$data['url'].self::PAGE_EXT
        );
      }

      if ($error_code !== 0 || $error_code_2 !== 0) {
        return 2;
      }
      $this->create_rss_feed($GLOBALS['rss_template']);
      $this->create_sitemap($GLOBALS['sitemap_template']);
      return 0;
    }
  }

  /**
  * Saves edited page. Merges new data to old data.
  *
  * @access public
  * @param array $data
  * @param string $template
  * @return int
  */
  public function edit_page($data, $template) {
    $orig_page = $this->get_page($data['url'].self::DATA_EXT);
    $new_page = array_merge($orig_page, $data);

    if (!isset($data['unlisted'])) {
      unset($new_page['unlisted']);
      unset($new_page['unlisted_checked']);
    }

    if (!isset($data['draft'])) {
      unset($new_page['draft']);
      unset($new_page['draft_checked']);
    }

    if (!isset($data['allow_comments'])) {
      unset($new_page['allow_comments']);
      unset($new_page['allow_comments_checked']);
    }

    $error = $this->add_new_page($new_page, $template, TRUE);
    return $error;
  }

  /**
  * Creates or updates front page
  *
  * Front page consists of list of all pages sorted by timestamp and
  * content written by admin.
  *
  * @access public
  * @param array $data
  * @param string $template
  * @return int
  */
  public function create_front_page($data, $template) {
    $page_list = $this->list_all_pages();
    $page_list = $page_list['pages'];

    $page_list = $this->aasort($page_list, 'timestamp');
    $page_list = array_reverse($page_list);

    $data['page_list'] = $page_list;
    $data['content_html'] = Markdown::defaultTransform($data['front_page_content']);

    $orig_content = $this->get_page('index.spage');
    if (isset($orig_content['date'])) {
      $data['date'] = $orig_content['date'];
      $data['time'] = $orig_content['time'];
      $data['timestamp'] = $orig_content['timestamp'];
    }
    else {
      $data['date'] = date('Y-m-d');
      $data['time'] = date('H:i');
      $data['timestamp'] = time();
    }

    $data['date_edited'] = date('Y-m-d');
    $data['time_edited'] = date('H:i');
    $data['timestamp_edited'] = time();

    array_splice($page_list, self::FRONT_ITEMS);
    $data['few_latests'] = $page_list;

    $bytes_written = $this->write_to_file(
      'index'.self::PAGE_EXT,
      $GLOBALS['m']->render($template, $data)
    );
    $bytes_written_2 = $this->write_to_file(
      'index'.self::DATA_EXT,
      serialize($data)
    );
    if (!$bytes_written || !$bytes_written_2) {
      return 1;
    }
    else {
      return 0;
    }
  }

  /**
  * Rebuilds all pages in current directory to match new template.
  *
  * @access public
  * @param string $template
  * @param string $template_with_comments
  * @return void
  */
  public function rebuild_pages($template, $template_with_comments) {
    $page_list = $this->list_all_pages();
    foreach ($page_list as $type => $item) {
      foreach ($item as $page => $content) {
        // If page has comments enabled, let's use proper template
        if (
          isset($content['allow_comments']) &&
          $content['allow_comments'] === 'allow_comments'
        ) {
          $template = $template_with_comments;
        }
        $this->add_new_page($content, $template, TRUE);
      }
    }
  }

  /**
  * Gets given page. Requested page name must not contain '/' or '\'
  * and it must end with '.spage'
  *
  * @access public
  * @param string $page
  * @return array
  */
  public function get_page($page) {
    $page = mb_substr($page, 0, 200);
    if (!$this->starts_with($page, '/') &&
      !$this->starts_with($page, '\\') &&
      $this->ends_with($page, self::DATA_EXT) && is_file($page)) {
      $data = $this->read_from_file($page);
      if (isset($data['draft']) && $data['draft'] === 'draft') {
        $data['draft_checked'] = 'checked';
      }

      if (isset($data['unlisted']) && $data['unlisted'] === 'unlisted') {
        $data['unlisted_checked'] = 'checked';
      }

      if (
        isset($data['allow_comments']) &&
        $data['allow_comments'] === 'allow_comments'
      ) {
        $data['allow_comments_checked'] = 'checked';
      }
      return $data;
    }
    return array();
  }


  /**
  * Moves page (*.html and *.spage) to trash directory.
  *
  * @access public
  * @param string $page
  * @return void
  */
  public function delete_page($page) {
    if (!$this->starts_with($page, '/') &&
      !$this->starts_with($page, '\\') &&
      $this->ends_with($page, self::DATA_EXT) && is_file($page)) {
      rename(
        dirname(__FILE__).DIRECTORY_SEPARATOR.$page,
        dirname(__FILE__).DIRECTORY_SEPARATOR.self::TRASH_DIR.DIRECTORY_SEPARATOR.$page
      );
      $page = str_replace(self::DATA_EXT, self::PAGE_EXT, $page);
      rename(
        dirname(__FILE__).DIRECTORY_SEPARATOR.$page,
        dirname(__FILE__).DIRECTORY_SEPARATOR.self::TRASH_DIR.DIRECTORY_SEPARATOR.$page
      );
      $this->create_rss_feed($GLOBALS['rss_template']);
    }
  }

  /**
  * Creates (and updates) RSS page.
  *
  * By default will list five newest pages sorted by timestamp.
  *
  * @access public
  * @param string $template
  * @return int
  */
  public function create_rss_feed($template) {
    $page_list = $this->list_all_pages();
    $page_list = $page_list['pages'];
    $page_list = $this->aasort($page_list, 'timestamp');
    $page_list = array_reverse($page_list);
    array_splice($page_list, self::RSS_ITEMS);
    $data = array('pages' => $page_list);

    $bytes_written = $this->write_to_file(
      'rss.xml', $GLOBALS['m']->render($template, $data)
    );
    if (!$bytes_written) {
      return 1;
    }
    else {
      return 0;
    }
  }

  /**
  * Creates (and updates) sitemap file.
  *
  * @access public
  * @param string $template
  * @return int
  */
  public function create_sitemap($template) {
    $page_list = $this->list_all_pages();
    $page_list = $page_list['pages'];
    $page_list = $this->aasort($page_list, 'timestamp');
    $data = array('pages' => $page_list);

    $bytes_written = $this->write_to_file(
      'sitemap.txt', $GLOBALS['m']->render($template, $data)
    );
    if (!$bytes_written) {
      return 1;
    }
    else {
      return 0;
    }
  }

  /**
  * Gets all pages and drafts
  *
  *
  * @access public
  * @return array
  */
  public function list_all_pages() {
    $dir = scandir('.');
    $pages = array();
    $drafts = array();
    $unlisted = array();

    foreach ($dir as $name) {
      if (
        $this->ends_with($name, self::DATA_EXT) &&
        $name !== 'index'.self::DATA_EXT
      ) {
        $content = $this->read_from_file($name);
        if (isset($content['draft']) && $content['draft'] === 'draft') {
          $drafts[] = $content;
        }
        else if (
          isset($content['unlisted']) &&
          $content['unlisted'] === 'unlisted'
        ) {
          $unlisted[] = $content;
        }
        else {
          $pages[] = $content;
        }
      }
    }
    return array(
      'pages' => $pages,
      'unlisted' => $unlisted,
      'drafts' => $drafts
    );
  }


  /**
  * Lists all comments from all pages.
  *
  * @access public
  * @param bool $limit
  * @param int $amount
  * @return array
  */
  public function list_all_comments($limit = FALSE, $amount = 0) {
    $all_pages = $this->list_all_pages();
    $comments = array();

    // Loops through pages, unlisteds and drafts
    foreach ($all_pages as $page_type) {
      // Loops through all pages in one page type
      foreach ($page_type as $page) {
        if (isset($page['comments'])) {
          // Loops through all comments in one page
          foreach ($page['comments'] as $c) {
            $comments[] = array(
              'author' => $c['author'],
              'comment' => $c['comment'],
              'timestamp' => $c['timestamp'],
              'uuid' => $c['uuid']
            );
          }
        }
      }
    }

    $comments = $this->aasort($comments, 'timestamp');
    $comments = array_reverse($comments);
    $comments = array('comments' => $comments);
    if ($limit) {
      $comments['comments'] = array_slice($comments['comments'], 0, $amount);
    }

    return $comments;
  }

  /**
  * Deletes given comments
  *
  * @access public
  * @param array $bad_comments
  * @return void
  */
  public function delete_comments($bad_comments) {
    $affected_pages = array();
    // Get list of affected pages
    foreach ($bad_comments as $comment) {
      $comment = explode('_', $comment);
      $affected_pages[] = $comment[0];
    }
    $affected_pages = array_unique($affected_pages);

    // Open each affected page
    foreach ($affected_pages as $page) {
      $page_content = $this->get_page($page.self::DATA_EXT);
      if (empty($page_content)) {
        break;
      }
      // Loop through all comments in current page
      $comments_count = count($page_content['comments']);
      for ($i = 0; $i <= $comments_count; $i++) {
        // If current comment UUID is marked for deletion > unset
        if (in_array($page_content['comments'][$i]['uuid'], $bad_comments)) {
          unset($page_content['comments'][$i]);
        }
      }
      // Reset comment indexes, remove comments section if none is left
      $comments = array_values($page_content['comments']);
      if (!empty($comments)) {
        $page_content['comments'] = $comments;
      }
      else {
        unset($page_content['comments']);
      }

      $this->write_to_file(
        $page_content['url'].Spage::DATA_EXT,
        serialize($page_content)
      );
      $this->write_to_file(
        $page_content['url'].Spage::PAGE_EXT,
        $GLOBALS['m']->render(
          $GLOBALS['default_template_with_comments'],
          $page_content
        )
      );
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
    $length = mb_strlen($needle);
    $start  = $length * -1;
    return (mb_substr($haystack, $start) === $needle);
  }

  /**
  * Helper function to verify if string starts with given substring
  *
  * @access public
  * @param string $haystack
  * @param string $needle
  * @return string
  */
  public function starts_with($haystack, $needle) {
    $length = mb_strlen($needle);
    return (mb_substr($haystack, 0, $length) === $needle);
  }


  /**
  * Helper function to sort multidimensional array.
  * Returns given array in sorted order
  *
  * @access public
  * @param array &$array
  * @param string $key
  * @return array
  */
  public function aasort(&$array, $sort_key) {
    $sorter = array();
    $sorted = array();
    reset($array);

    foreach ($array as $key => $va) {
      $sorter[$key] = $va[$sort_key];
    }

    asort($sorter);

    foreach ($sorter as $key => $va) {
      $sorted[$key] = $array[$key];
    }

    return $sorted;
  }


  /**
  * Writes given file to disk with given content
  *
  * @access public
  * @param string $name
  * @param string $content
  * @return int
  */
  public function write_to_file($name, $content) {
    $bytes_written = file_put_contents(
      dirname(__FILE__).DIRECTORY_SEPARATOR.$name,
      $content
    );
    if (!$bytes_written) {
      return 2;
    }
    else {
      return 0;
    }
  }

  /**
  * Reads given file from disk
  *
  * @access private
  * @param string $name
  * @return void
  */
  private function read_from_file($name) {
    $data = unserialize(file_get_contents(
      dirname(__FILE__).DIRECTORY_SEPARATOR.$name)
    );
    return $data;
  }
}

$current_file = explode('/', $_SERVER["PHP_SELF"]);
$current_file = $current_file[count($current_file) - 1];

$this_file = explode(DIRECTORY_SEPARATOR, __FILE__);
$this_file = $this_file[count($this_file) - 1];

if ($current_file === $this_file) {
  $s = new Spage;

  if (!isset($_SERVER['HTTP_REFERER'])) {
    $_SERVER['HTTP_REFERER'] = '';
  }
  $protocols = array('http://', 'https://');
  $http_referer_without_protocol = str_replace(
    $protocols,
    '',
    $_SERVER['HTTP_REFERER']
  );

  // If no 'operation' parameters is not set or if referer is not self,
  // 'operation' is set to empty (blocks CSRF vulnerability).
  if (!isset($_REQUEST['operation']) || !$s->starts_with(
    $http_referer_without_protocol,
    $_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'])
  ) {
    $_REQUEST['operation'] = '';
  }

  switch ($_REQUEST['operation']) {
    case 'create_page':
      if (isset($_POST['allow_comments'])) {
        $template = $default_template_with_comments;
      }
      else {
        $template = $default_template;
      }
      $error_code = $s->add_new_page(
        $_POST,
        $template,
        isset($_POST['overwrite'])
      );
      if ($error_code===1) {
        $message = 'Page with same name already exists.';
      }
      else if ($error_code===2) {
        $message = 'Something went wrong while creating the page.';
      }
      else {
        $message = 'Page created.';
      }
      $page = $s->get_page(urlencode($_POST['url']).Spage::DATA_EXT);
      $page['message'] = $message;
      echo $m->render($admin_continue_template, $page);
      break;
    case 'save_page':
      if (isset($_POST['allow_comments'])) {
        $template = $default_template_with_comments;
      }
      else {
        $template = $default_template;
      }
      $error_code = $s->edit_page($_POST, $template);
      if ($error_code != 0) {
        $message = 'Something went wrong while saving the page.';
      }
      else {
        $message = 'Page saved.';
      }
      $page = $s->get_page(urlencode($_POST['url']).Spage::DATA_EXT);
      $page['message'] = $message;
      echo $m->render($admin_continue_template, $page);
      break;
    case 'edit_front_page':
      $data = $s->get_page('index'.Spage::DATA_EXT);
      echo $m->render($admin_front_page_template, $data);
      break;
    case 'create_front_page':
      $s->create_front_page($_POST, $front_page_template);
      echo $m->render(
        $admin_template,
        array('message' => 'Front page created.')
      );
      break;
    case 'rebuild_pages':
      $s->rebuild_pages($default_template, $default_template_with_comments);
      echo $m->render($admin_template, array('message' => 'Rebuilt pages.'));
      break;
    case 'list_pages':
      $data['all_pages'] = $s->list_all_pages();
      echo $m->render($admin_page_list_template, $data);
      break;
    case 'edit_page':
      $data = $s->get_page(urlencode($_GET['page']).Spage::DATA_EXT);
      echo $m->render($admin_edit_template, $data);
      break;
    case 'delete_page':
      $s->delete_page(urlencode($_GET['page']).Spage::DATA_EXT);
      echo $m->render($admin_template, array('message' => 'Page deleted.'));
      break;
    case 'list_comments':
      $comments = $s->list_all_comments(TRUE, 50);
      $comments['message'] = '<p>
        Only 50 latest comments are shown.
        <a href="spage.php?operation=list_all_comments">List all comments</a>.
        </p>';
      echo $m->render($admin_list_comments_template, $comments);
      break;
    case 'list_all_comments':
      $comments = $s->list_all_comments();
      echo $m->render($admin_list_comments_template, $comments);
      break;
    case 'delete_comments':
      $comments = $_POST;
      unset($comments['operation']);
      $comments = array_keys($comments);
      $s->delete_comments($comments);
      echo $m->render($admin_template, array('message' => 'Comments deleted.'));
      break;
    default:
      echo $m->render($admin_template, array());
      break;
  }
}

/*

Copyright Harri Paavola

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
