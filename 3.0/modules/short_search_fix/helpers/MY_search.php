<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2012 Bharat Mediratta
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 */
class search extends search_Core {
  /**
   * Add more terms to the query by wildcarding the stem value of the first
   * few terms in the query.
   */
  static function add_query_terms($q) {
    $MAX_TERMS = 5;

    // strip leading, trailing, and extra whitespaces
    $terms = preg_replace('/^\s+/', '', $q);
    $terms = preg_replace('/\s+$/', '', $terms);
    $terms = preg_replace('/\s\s+/', ' ', $terms);

    $terms = explode(" ", $terms, $MAX_TERMS);
    //$terms = explode(" ", $q, $MAX_TERMS); // commented out from original function
    for ($i = 0; $i < min(count($terms), $MAX_TERMS - 1); $i++) {
      // Don't wildcard quoted or already wildcarded terms
      if ((substr($terms[$i], 0, 1) != '"') && (substr($terms[$i], -1, 1) != "*")) {
        $terms[] = rtrim($terms[$i], "s") . "*";
      }
    }
    //return implode(" ", $terms); // commented out from original function

  /**
   * Add the search prefix to the start of every word.  
   */
    $prefix = module::get_var("short_search_fix","search_prefix");
    $terms = implode(" ", $terms);
    $terms = preg_replace('/^\s+/', '', $terms); // the implode seems to add this back in
    // add the prefixes
    if (preg_match('/\w/',$terms) > 0) {
      $terms = ' ' . $terms;
      $terms = str_replace(' ', ' '.$prefix, $terms);
      $terms = str_replace(' '.$prefix.'"', ' '.'"'.$prefix, $terms);
      $terms = substr($terms,1);
    }
    return $terms;
  }
}
