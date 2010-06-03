<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2010 Bharat Mediratta
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

/**
 * Provides a driver-based interface for internationaization and localization.
 */
class Language_Multi_Driver extends Language_Driver {
  public function locale($locale=null) {
    if ($locale) {
      $this->_config['default_locale'] = $locale;
      $php_locale = setlocale(LC_ALL, 0);
      list ($php_locale, $unused) = explode('.', $php_locale . '.');
      if ($php_locale != $locale) {
        // Attempt to set PHP's locale as well (for number formatting, collation, etc.)
        $locale_prefs = array($locale);
        // Try appending some character set names; some systems (like FreeBSD) need this.
        // Some systems require a format with hyphen (eg. Gentoo) and others without (eg. FreeBSD).
        $charsets = array('utf8', 'UTF-8', 'UTF8', 'ISO8859-1', 'ISO-8859-1');
        if (substr($locale, 0, 2) != 'en') {
          $charsets = array_merge($charsets, array(
              'EUC', 'Big5', 'euc', 'ISO8859-2', 'ISO8859-5', 'ISO8859-7',
              'ISO8859-9', 'ISO-8859-2', 'ISO-8859-5', 'ISO-8859-7', 'ISO-8859-9'));
        }
        foreach ($charsets as $charset) {
          $locale_prefs[] = $locale . '.' . $charset;
        }
        $locale_prefs[] = 'en_US';
        $php_locale = setlocale(LC_ALL, $locale_prefs);
      }
      if (is_string($php_locale) && substr($php_locale, 0, 2) == 'tr') {
        // Make PHP 5 work with Turkish (the localization results are mixed though).
        // Hack for http://bugs.php.net/18556
        setlocale(LC_CTYPE, 'C');
      }
    }
    return $this->_config['default_locale'];
  }

  public function is_rtl($locale=null) {
    $is_rtl = !empty($this->_config["force_rtl"]);
    if (empty($is_rtl)) {
      $locale or $locale = $this->locale();
      list ($language, $territory) = explode('_', $locale . "_");
      $is_rtl = in_array($language, array("he", "fa", "ar"));
    }
    return $is_rtl;
  }

  /**
   * @see Language_Driver::translate
   */
  public function translate($message, $options=array()) {
    $locale = empty($options['locale']) ? $this->_config['default_locale'] : $options['locale'];
    $count = isset($options['count']) ? $options['count'] : null;
    $values = $options;
    unset($values['locale']);
    $this->log($message, $options);

    $entry = $this->lookup($locale, $message);

    if (null === $entry) {
      // Default to the root locale.
      $entry = $message;
      $locale = $this->_config['root_locale'];
    }

    $entry = $this->pluralize($locale, $entry, $count);

    $entry = $this->interpolate($locale, $entry, $values);

    return SafeString::of_safe_html($entry);
  }

  public function has_translation($message, $options=null) {
    $locale = empty($options['locale']) ? $this->_config['default_locale'] : $options['locale'];

    $entry = $this->lookup($locale, $message);

    if (null === $entry) {
      return false;
    } else if (!is_array($message)) {
      return $entry !== '';
    } else {
      if (!is_array($entry) || empty($entry)) {
        return false;
      }
      // It would be better to verify that all the locale's plural forms have a non-empty
      // translation, but this is fine for now.
      foreach ($entry as $value) {
        if ($value === '') {
          return false;
        }
      }
      return true;
    }
  }

  private function lookup($locale, $message) {
    if (!isset($this->_cache[$locale])) {
      $this->_cache[$locale] = $this->_load_translations($locale);
    }

    $key = $this->get_message_key($message);

    if (isset($this->_cache[$locale][$key])) {
      return $this->_cache[$locale][$key];
    } else {
      return null;
    }
  }

  private function _load_translations($locale) {
    $cache_key = "translation|" . $locale;
    $cache = Cache::instance();
    $translations = $cache->get($cache_key);
    if (!isset($translations) || !is_array($translations)) {
      $translations = array();
      foreach (db::build()
               ->select("key", "translation")
               ->from("incoming_translations")
               ->where("locale", "=", $locale)
               ->execute() as $row) {
        $translations[$row->key] = unserialize($row->translation);
      }

      // Override incoming with outgoing...
      foreach (db::build()
               ->select("key", "translation")
               ->from("outgoing_translations")
               ->where("locale", "=", $locale)
               ->execute() as $row) {
        $translations[$row->key] = unserialize($row->translation);
      }

      $cache->set($cache_key, $translations, array("translation"), 0);
    }
    return $translations;
  }

  protected function _get_plural_key($locale, $count) {
    $parts = explode('_', $locale);
    $language = $parts[0];

    // Data from CLDR 1.6 (http://unicode.org/cldr/data/common/supplemental/plurals.xml).
    // Docs: http://www.unicode.org/cldr/data/charts/supplemental/language_plural_rules.html
    switch ($language) {
      case 'az':
      case 'fa':
      case 'hu':
      case 'ja':
      case 'ko':
      case 'my':
      case 'to':
      case 'tr':
      case 'vi':
      case 'yo':
      case 'zh':
      case 'bo':
      case 'dz':
      case 'id':
      case 'jv':
      case 'ka':
      case 'km':
      case 'kn':
      case 'ms':
      case 'th':
        return 'other';

      case 'ar':
        if ($count == 0) {
          return 'zero';
        } else if ($count == 1) {
          return 'one';
        } else if ($count == 2) {
          return 'two';
        } else if (is_int($count) && ($i = $count % 100) >= 3 && $i <= 10) {
          return 'few';
        } else if (is_int($count) && ($i = $count % 100) >= 11 && $i <= 99) {
          return 'many';
        } else {
          return 'other';
        }

      case 'pt':
      case 'am':
      case 'bh':
      case 'fil':
      case 'tl':
      case 'guw':
      case 'hi':
      case 'ln':
      case 'mg':
      case 'nso':
      case 'ti':
      case 'wa':
        if ($count == 0 || $count == 1) {
          return 'one';
        } else {
          return 'other';
        }

      case 'fr':
        if ($count >= 0 and $count < 2) {
          return 'one';
        } else {
          return 'other';
        }

      case 'lv':
        if ($count == 0) {
          return 'zero';
        } else if ($count % 10 == 1 && $count % 100 != 11) {
          return 'one';
        } else {
          return 'other';
        }

      case 'ga':
      case 'se':
      case 'sma':
      case 'smi':
      case 'smj':
      case 'smn':
      case 'sms':
        if ($count == 1) {
          return 'one';
        } else if ($count == 2) {
          return 'two';
        } else {
          return 'other';
        }

      case 'ro':
      case 'mo':
        if ($count == 1) {
          return 'one';
        } else if (is_int($count) && $count == 0 && ($i = $count % 100) >= 1 && $i <= 19) {
          return 'few';
        } else {
          return 'other';
        }

      case 'lt':
        if (is_int($count) && $count % 10 == 1 && $count % 100 != 11) {
          return 'one';
        } else if (is_int($count) && ($i = $count % 10) >= 2 && $i <= 9 && ($i = $count % 100) < 11 && $i > 19) {
          return 'few';
        } else {
          return 'other';
        }

      case 'hr':
      case 'ru':
      case 'sr':
      case 'uk':
      case 'be':
      case 'bs':
      case 'sh':
        if (is_int($count) && $count % 10 == 1 && $count % 100 != 11) {
          return 'one';
        } else if (is_int($count) && ($i = $count % 10) >= 2 && $i <= 4 && ($i = $count % 100) < 12 && $i > 14) {
          return 'few';
        } else if (is_int($count) && ($count % 10 == 0 || (($i = $count % 10) >= 5 && $i <= 9) || (($i = $count % 100) >= 11 && $i <= 14))) {
          return 'many';
        } else {
          return 'other';
        }

      case 'cs':
      case 'sk':
        if ($count == 1) {
          return 'one';
        } else if (is_int($count) && $count >= 2 && $count <= 4) {
          return 'few';
        } else {
          return 'other';
        }

      case 'pl':
        if ($count == 1) {
          return 'one';
        } else if (is_int($count) && ($i = $count % 10) >= 2 && $i <= 4 &&
                   ($i = $count % 100) < 12 && $i > 14 && ($i = $count % 100) < 22 && $i > 24) {
          return 'few';
        } else {
          return 'other';
        }

      case 'sl':
        if ($count % 100 == 1) {
          return 'one';
        } else if ($count % 100 == 2) {
          return 'two';
        } else if (is_int($count) && ($i = $count % 100) >= 3 && $i <= 4) {
          return 'few';
        } else {
          return 'other';
        }

      case 'mt':
        if ($count == 1) {
          return 'one';
        } else if ($count == 0 || is_int($count) && ($i = $count % 100) >= 2 && $i <= 10) {
          return 'few';
        } else if (is_int($count) && ($i = $count % 100) >= 11 && $i <= 19) {
          return 'many';
        } else {
          return 'other';
        }

      case 'mk':
        if ($count % 10 == 1) {
          return 'one';
        } else {
          return 'other';
        }

      case 'cy':
        if ($count == 1) {
          return 'one';
        } else if ($count == 2) {
          return 'two';
        } else if ($count == 8 || $count == 11) {
          return 'many';
        } else {
          return 'other';
        }

      default: // en, de, etc.
        return $count == 1 ? 'one' : 'other';
    }
  }

  // @todo Might want to add a localizable language name as well.
  public function initialize_language_data() {
    $l["af_ZA"] = "Afrikaans";                // Afrikaans
    $l["ar_SA"] = "العربية";                   // Arabic
    $l["be_BY"] = "Беларускі";           // Belarusian
    $l["bg_BG"] = "български";           // Bulgarian
    $l["ca_ES"] = "Catalan";                  // Catalan
    $l["cs_CZ"] = "čeština";                  // Czech
    $l["da_DK"] = "Dansk";                    // Danish
    $l["de_DE"] = "Deutsch";                  // German
    $l["el_GR"] = "Greek";                    // Greek
    $l["en_GB"] = "English (UK)";             // English (UK)
    $l["en_US"] = "English (US)";             // English (US)
    $l["es_AR"] = "Español (AR)";             // Spanish (AR)
    $l["es_ES"] = "Español";                  // Spanish (ES)
    $l["es_MX"] = "Español (MX)";             // Spanish (MX)
    $l["et_EE"] = "Eesti";                    // Estonian
    $l["eu_ES"] = "Euskara";                  // Basque
    $l["fa_IR"] = "فارس";                     // Farsi
    $l["fi_FI"] = "Suomi";                    // Finnish
    $l["fo_FO"] = "Føroyskt";                    // Faroese
    $l["fr_FR"] = "Français";                 // French
    $l["ga_IE"] = "Gaeilge";                  // Irish
    $l["he_IL"] = "עברית";                    // Hebrew
    $l["hu_HU"] = "Magyar";                   // Hungarian
    $l["is_IS"] = "Icelandic";                // Icelandic
    $l["it_IT"] = "Italiano";                 // Italian
    $l["ja_JP"] = "日本語";                    // Japanese
    $l["ko_KR"] = "한국어";                    // Korean
    $l["lt_LT"] = "Lietuvių";                 // Lithuanian
    $l["lv_LV"] = "Latviešu";                 // Latvian
    $l["nl_NL"] = "Nederlands";               // Dutch
    $l["no_NO"] = "Norsk bokmål";             // Norwegian
    $l["pl_PL"] = "Polski";                   // Polish
    $l["pt_BR"] = "Português do Brasil";      // Portuguese (BR)
    $l["pt_PT"] = "Português ibérico";        // Portuguese (PT)
    $l["ro_RO"] = "Română";                   // Romanian
    $l["ru_RU"] = "Русский";              // Russian
    $l["sk_SK"] = "Slovenčina";               // Slovak
    $l["sl_SI"] = "Slovenščina";              // Slovenian
    $l["sr_CS"] = "Srpski";                   // Serbian
    $l["sv_SE"] = "Svenska";                  // Swedish
    $l["tr_TR"] = "Türkçe";                   // Turkish
    $l["uk_UA"] = "українська";         // Ukrainian
    $l["vi_VN"] = "Tiếng Việt";               // Vietnamese
    $l["zh_CN"] = "简体中文";                  // Chinese (CN)
    $l["zh_TW"] = "繁體中文";                  // Chinese (TW)
    asort($l, SORT_LOCALE_STRING);

    // Language subtag to (default) locale mapping
    foreach ($l as $locale => $name) {
      list ($language) = explode("_", $locale . "_");
      // The first one mentioned is the default
      if (!isset($d[$language])) {
        $d[$language] = $locale;
      }
    }

    $this->locales = $l;
    $this->language_subtag_to_locale = $d;
  }

  public function set_request_locale() {
    // 1. Check the session specific preference (cookie)
    $locale = $this->cookie_locale();
    // 2. Check the user's preference
    if (!$locale) {
      $locale = identity::active_user()->locale;
    }
    // 3. Check the browser's / OS' preference
    if (!$locale) {
      $locale = $this->_locale_from_http_request();
    }
    // If we have any preference, override the site's default locale
    if ($locale) {
      $this->locale($locale);
    }
  }

  /**
   * Returns the best match comparing the HTTP accept-language header
   * with the installed locales.
   * @todo replace this with request::accepts_language() when we upgrade to Kohana 2.4
   */
  private function _locale_from_http_request() {
    $http_accept_language = Input::instance()->server("HTTP_ACCEPT_LANGUAGE");
    if ($http_accept_language) {
      // Parse the HTTP header and build a preference list
      // Example value: "de,en-us;q=0.7,en-uk,fr-fr;q=0.2"
      $locale_preferences = array();
      foreach (explode(",", $http_accept_language) as $code) {
        list ($requested_locale, $qvalue) = explode(";", $code . ";");
        $requested_locale = trim($requested_locale);
        $qvalue = trim($qvalue);
        if (preg_match("/^([a-z]{2,3})(?:[_-]([a-zA-Z]{2}))?/", $requested_locale, $matches)) {
          $requested_locale = strtolower($matches[1]);
          if (!empty($matches[2])) {
            $requested_locale .= "_" . strtoupper($matches[2]);
          }
          $requested_locale = trim(str_replace("-", "_", $requested_locale));
          if (!strlen($qvalue)) {
            // If not specified, default to 1.
            $qvalue = 1;
          } else {
            // qvalue is expected to be something like "q=0.7"
            list ($ignored, $qvalue) = explode("=", $qvalue . "==");
            $qvalue = floatval($qvalue);
          }
          // Group by language to boost inexact same-language matches
          list ($language) = explode("_", $requested_locale . "_");
          if (!isset($locale_preferences[$language])) {
            $locale_preferences[$language] = array();
          }
          $locale_preferences[$language][$requested_locale] = $qvalue;
        }
      }

      // Compare and score requested locales with installed ones
      $scored_locales = array();
      foreach ($locale_preferences as $language => $requested_locales) {
        // Inexact match adjustment (same language, different region)
        $fallback_adjustment_factor = 0.95;
        if (count($requested_locales) > 1) {
          // Sort by qvalue, descending
          $qvalues = array_values($requested_locales);
          rsort($qvalues);
          // Ensure inexact match scores worse than 2nd preference in same language.
          $fallback_adjustment_factor *= $qvalues[1];
        }
        foreach ($requested_locales as $requested_locale => $qvalue) {
          list ($matched_locale, $match_score) =
              $this->_locale_match_score($requested_locale, $qvalue, $fallback_adjustment_factor);
          if ($matched_locale &&
              (!isset($scored_locales[$matched_locale]) ||
               $match_score > $scored_locales[$matched_locale])) {
            $scored_locales[$matched_locale] = $match_score;
          }
        }
      }

      arsort($scored_locales);

      list ($locale) = each($scored_locales);
      return $locale;
    }

    return null;
  }

  private function _locale_match_score($requested_locale, $qvalue, $adjustment_factor) {
    $installed = $this->installed();
    if (isset($installed[$requested_locale])) {
      return array($requested_locale, $qvalue);
    }
    list ($language) = explode("_", $requested_locale . "_");
    if (isset($this->language_subtag_to_locale[$language]) &&
        isset($installed[$this->language_subtag_to_locale[$language]])) {
      $score = $adjustment_factor * $qvalue;
      return array($this->language_subtag_to_locale[$language], $score);
    }
    return array(null, 0);
  }

  public function cookie_locale() {
    // Can't use Input framework for client side cookies since
    // they're not signed.
    $cookie_data = isset($_COOKIE["g_locale"]) ? $_COOKIE["g_locale"] : null;
    $locale = null;
    if ($cookie_data) {
      if (preg_match("/^([a-z]{2,3}(?:_[A-Z]{2})?)$/", trim($cookie_data), $matches)) {
        $requested_locale = $matches[1];
        $installed_locales = locales::installed();
        if (isset($installed_locales[$requested_locale])) {
          $locale = $requested_locale;
        }
      }
    }
    return $locale;
  }

  public function update_installed($locales) {
    // Ensure that the default is included...
    $default = module::get_var("gallery", "default_locale");
    $locales = in_array($default, $locales)
      ? $locales
      : array_merge($locales, array($default));

    module::set_var("gallery", "installed_locales", join("|", $locales));

    // Clear the cache
    $this->locales = null;
  }

  public function display_name($locale=null) {
    if (empty($this->locales)) {
      $this->initialize_language_data();
    }
    $locale or $locale = Gallery_I18n::instance()->locale();

    return $this->locales[$locale];
  }

}
