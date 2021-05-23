<?php
mb_internal_encoding("UTF-8");
mb_http_output("UTF-8");

require_once 'spage.php';

define('MAX_AUTHOR_LENGTH', 200);
define('MAX_MESSAGE_LENGTH', 10000);

/**
 * Adds new comment to page.
 *
 * $author max size is 200
 * $comment max size is 10000
 * Characters after those limits are just dropped.
 *
 * Will automatically add p elements and tansform URLs to links.
 *
 * @param string $page_name
 * @param string $author
 * @param string $comment
 * @return void
 */
function comment($page_name, $author, $comment)
{
  $s = new Spage;
  $page = $s->get_page($page_name . Spage::DATA_EXT);
  // Index page, non-existent pages and drafts can not have comments
  if (
    $page_name === 'index' ||
    empty($page) ||
    isset($page['draft']) ||
    !isset($page['allow_comments']) ||
    !in_array($page['allow_comments'], array('allow_comments', 'moderate_comments'))
  ) {
    header("HTTP/1.0 404 Not Found");
    die();
  }

  $date = date('Y-m-d');
  $time = date('H:i');
  $timestamp = time();

  // Some limits for the lenghts
  $author = mb_substr($author, 0, MAX_AUTHOR_LENGTH);
  $comment = mb_substr($comment, 0, MAX_MESSAGE_LENGTH);

  $comment = array(
    'author' => htmlspecialchars(
      $author,
      ENT_QUOTES | ENT_HTML5 | ENT_SUBSTITUTE | ENT_DISALLOWED,
      'UTF-8',
      FALSE
    ),
    'comment' => htmlspecialchars(
      $comment,
      ENT_QUOTES | ENT_HTML5 | ENT_SUBSTITUTE | ENT_DISALLOWED,
      'UTF-8',
      FALSE
    ),
    'date' => $date,
    'time' => $time,
    'timestamp' => $timestamp,
    'uuid' => uniqid($page_name . '_'),
  );
  // All line breaks to br tags. Then double br tags to paragraphs.
  // Email addresses from author are linked
  // URLs from comments are linked
  $comment['comment'] = nl2br($comment['comment'], FALSE);
  $comment['comment'] = str_replace(
    "<br>\r\n<br>",
    '</p><p>',
    $comment['comment']
  );
  $comment['comment'] = '<p>' . $comment['comment'] . '</p>';
  $comment['comment'] = url_to_link($comment['comment']);
  $comment['author'] = email_to_link($comment['author']);

  if (
    isset($page['allow_comments']) &&
    $page['allow_comments'] === 'moderate_comments'
  ) {
    $page['comment_queue'][] = $comment;
  } else {
    $page['comments'][] = $comment;
  }

  // edit_page or add_new_page from Spage can not be used
  // since they modify the edit timestamps of the page.
  $s->write_to_file($page['url'] . Spage::DATA_EXT, serialize($page));
  if ($page['allow_comments'] === 'moderate_comments') {
    $template = 'moderate_template_with_comments';
  } else {
    $template = 'default_template_with_comments';
  }
  $s->write_to_file(
    $page['url'] . Spage::PAGE_EXT,
    $GLOBALS['m']->render(
      $GLOBALS[$template],
      $page
    )
  );

  header('Location: ' . $page_name . '#' . $comment['uuid']);
  die();
}

/**
 * Looks for pieces of text that look like URLs
 * and transforms them to links.
 * Adds rel="nofollow" to the links.
 *
 * @param string $text
 * @return void
 */
function url_to_link($text)
{
  // Tries its best to match any URL.
  $regex = '#(?i)\b((?:[a-z][\w-]+:(?:/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))#';
  $replace = '<a rel="nofollow" href="$1">$1</a>';
  return preg_replace($regex, $replace, $text);
}

/**
 * Looks for pieces of text that look like email addressess
 * and transforms them to mailto: links.
 *
 * @param string $text
 * @return void
 */
function email_to_link($text)
{
  $regex = '/(\S+@\S+\.\S+)/';
  $replace = '<a rel="nofollow" href="mailto:$1">$1</a>';
  return preg_replace($regex, $replace, $text);
}

// page, author and messsage are required
if (
  !isset($_POST['page']) ||
  !isset($_POST['author']) ||
  !isset($_POST['message'])
) {
  header("HTTP/1.0 404 Not Found");
  die();
} else {
  comment($_POST['page'], $_POST['author'], $_POST['message']);
}
