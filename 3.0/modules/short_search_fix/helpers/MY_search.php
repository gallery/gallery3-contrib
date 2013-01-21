<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2013 Bharat Mediratta
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
    $MAX_TERMS = 5; // used to limit the max number of extra terms
    $prefix = module::get_var("short_search_fix","search_prefix"); // short search fix prefix
    // strip leading, trailing, and extra whitespaces
    $terms = preg_replace('/^\s+/', '', $q);
    $terms = preg_replace('/\s+$/', '', $terms);
    $terms = preg_replace('/\s\s+/', ' ', $terms);
    // explode terms, initialize the loop
    $terms = explode(" ", $terms); // array
    $termsextra = ""; // string, not array
    $numtermsextra = 0;
    $flagwild = 1;
    $countquote = 0;
    $countparen = 0;
    // run the loop for each term
    foreach ($terms as &$term) {
      $countprefix = 0;
      $countsuffix = 0;
      $flagopenparen = 0;
      $flagcloseparen = 0;
      // set flagwild to 0 if we're over MAX_TERMS (only runs if we're not in the middle of parens/quotes)
      if ($countparen == 0 && $countquote == 0 && ($numtermsextra >= ($MAX_TERMS - 1))) {
        $flagwild = 0;
      }
      // find opening special characters
      while ((substr($term, $countprefix, 1) == "(" ||
              substr($term, $countprefix, 1) == '"' ||
              substr($term, $countprefix, 1) == "+" ||
              substr($term, $countprefix, 1) == "-" ||
              substr($term, $countprefix, 1) == "~" ||
              substr($term, $countprefix, 1) == "<" ||
              substr($term, $countprefix, 1) == ">") &&
             ($countprefix+$countsuffix) < strlen($term)) {
        if (substr($term, $countprefix, 1) == '"') {
          $countquote++;
          $flagwild = 0;
        }
        if (substr($term, $countprefix, 1) == "(") {
          $countparen++;
          $flagopenparen = 1;
        }
        $countprefix++;
      }
      // reset flagwild to 1 if we're under MAX_TERMS (only runs if we're not in the middle of quotes, and forced to run if we're still in paren)
      if ($countquote == 0 && ($countparen > 0 || $numtermsextra < ($MAX_TERMS - 1))) {
        $flagwild = 1;
      }
      // find closing special characters
      while ((substr($term, -$countsuffix-1, 1) == ")" ||
              substr($term, -$countsuffix-1, 1) == '"') &&
             ($countprefix+$countsuffix) < strlen($term)) {
        if (substr($term, -$countsuffix-1, 1) == '"') {
          $countquote = max(0, $countquote-1);
        }
        if (substr($term, -$countsuffix-1, 1) == ")") {
          $countparen = max(0, $countparen-1);
          $flagcloseparen = 1;
        }
        $countsuffix++;
      }
      // split term
      $termprefix = substr($term, 0, $countprefix);
      $termterm = substr($term."A", $countprefix, -$countsuffix-1); // artificial padded A assures that the third argument is always negative
      $termsuffix = substr($term, -$countsuffix, $countsuffix);
      // add extra terms with wildcards
      if ($flagwild == 1 && 
          substr($termterm, -1, 1) != "*" &&
          strlen($termterm) > 0) {
        // @todo: make this i18n friendly with the plural character (only works here with s)
        $termsextra = $termsextra . $termprefix . $prefix . rtrim($termterm, "s") . "*" . $termsuffix . " ";
        $numtermsextra++;
      } elseif ($flagopenparen == 1 && $flagcloseparen == 0) {
        $termsextra = $termsextra . str_replace('"', '', $termprefix);
      } elseif ($flagopenparen == 0 && $flagcloseparen == 1) {
        $termsextra = preg_replace('/\s+$/', '', $termsextra) . ") ";
      }
      // add short search prefixes
      if (strlen($termterm) > 0) {
        $term = $termprefix . $prefix . $termterm . $termsuffix;
      }
    }
    // implode terms, trim termsextra trailing space (if it exists)
    $terms = implode(" ", $terms);
    $termsextra = preg_replace('/\s+$/', '', $termsextra);
    // add extra closing quotes and parentheses
    while ($countquote > 0) {
      $terms = $terms.'"';
      $termsextra = $termsextra.'"';
      $countquote--;
    }
    while ($countparen > 0) {
      $terms = $terms.")";
      $termsextra = $termsextra.")";
      $countparen--;
    }
    // all done!
    return ($terms." ".$termsextra);
  }
}