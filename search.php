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
    'title' => $page['title'].' '.$page['url'],
    'content' => $page['content']
  );
  $terms = array_map('mb_strtolower', $terms);
  $terms = array_map('strip_tags', $terms);
  $page = array_map('mb_strtolower', $page);
  $page = array_map('strip_tags', $page);
  $orig_page['relevance'] = 0;

  // Loops through all search terms and calculates relevancy
  foreach ($terms as $term_key => $term_value) {
    // Check if search term is in title or url
    $occurrences = mb_strstr($page['title'], $term_value);
    if (!empty($occurrences)) {
      $orig_page['relevance'] = $orig_page['relevance'] + 10;
    }
    // Check if search term is in content
    $occurrences = mb_substr_count($page['content'], $term_value);
    if (!empty($occurrences)) {
      $orig_page['relevance'] = $orig_page['relevance'] + $occurrences;
    }
  }

  return $orig_page;
}

if (isset($_GET['terms'])) {
  $orig_terms = $_GET['terms'];
  $terms = array(); // the actual search terms
  $sliced_terms = array(); // valid terms, but not used since there were so many
  $dropped_terms = array(); // invalid terms (too short)

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
