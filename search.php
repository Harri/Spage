<?php

require_once 'spage.php';

/**
* Search given words from all pages, not including drafts.
*
* Returns all pages with hits, sorted by relevancy.
*
* @param array $terms
* @return array
*/
function search($terms) {
  $s = new Spage;

  $pages = $s->list_all_pages();
  $pages = $pages['pages'];

  $results = array();

  foreach ($pages as $key => $value) {
    $value = analyze_page($value, $terms);
    if ($value['relevance'] > 0) {
      $value['url'] = str_replace('.spage', '', $value['url']);
      $results[] = $value;
    }
  }
  $results = $s->aasort($results, 'relevance');
  $results = array_reverse($results);
  return $results;
}


/**
* Analyzes page relevancy against search terms.
* Every title and url hit +10 points.
* Every content hit +1 point.
* 
* Returns page with relevancy points.
*
* @param array $page
* @param array $terms
* @return array
*/
function analyze_page($page, $terms) {
  $orig_page = $page;
  $page = array(
    'title' => $page['title'],
    'url' => $page['url'],
    'content' => $page['content']
  );
  $terms = array_map('mb_strtolower', $terms);
  $terms = array_map('strip_tags', $terms);
  $page = array_map('mb_strtolower', $page);
  $page = array_map('strip_tags', $page);
  $orig_page['relevance'] = 0;
  // Go through every data field in page
  foreach ($page as $key => $value) {
    // Go through every search term with every data field
    foreach ($terms as $term_key => $term_value) {
      $title_hit = FALSE;
      switch ($key) {
        case 'title':
          $occurrences = mb_strstr($value, $term_value);
          if ($occurrences) {
            $orig_page['relevance'] = $orig_page['relevance'] + 10;
            $title_hit = TRUE;
          }
          break;
        case 'url':
          $occurrences = mb_strstr($value, $term_value);
          if (!$title_hit && $occurrences) {
            $orig_page['relevance'] = $orig_page['relevance'] + 10;
          }
          break;
        case 'content':
          $occurrences = mb_substr_count($value, $term_value);
          if ($occurrences > 0) {
            $orig_page['relevance'] = $orig_page['relevance'] + $occurrences;
          }
          break;
        default:
          break;
      }
    }
  }
  return $orig_page;
}

if (isset($_GET['terms'])) {
  $orig_terms = $_GET['terms'];
  $dropped_terms = array();

  $terms = explode(' ', $orig_terms);

  foreach ($terms as $key => $value) {
    if(mb_strlen($value) < 3) {
      unset($terms[$key]);
      $dropped_terms[] = $value;
    }
  }

  $sliced_terms = array_slice($terms, 10);
  $terms = array_slice($terms, 0, 10);

  if (count($dropped_terms) > 0) {
    $results['dropped']['terms'] = implode(', ', $dropped_terms);
  }
  if (count($sliced_terms) > 0) {
    $results['sliced']['terms'] = implode(', ', $sliced_terms);
  }

  $results['results'] = search($terms);
  $results['terms'] = implode(' ', $terms);
  $results['orig_terms'] = $orig_terms;
  echo $m->render($search_results_template, $results);
}
else {
  echo $m->render($search_template, array());
}
