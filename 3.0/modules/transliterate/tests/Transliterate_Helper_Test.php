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
class Transliterate_Helper_Test extends Gallery_Unit_Test_Case {
  public function utf8_to_ascii_test() {
    $this->assert_equal("Te glossa mou edosan ellenike",
                        transliterate::utf8_to_ascii("Τη γλώσσα μου έδωσαν ελληνική"));
    $this->assert_equal("Na bierieghu pustynnykh voln",
                        transliterate::utf8_to_ascii("На берегу пустынных волн"));
    $this->assert_equal("vepxis tqaosani shot`a rust`aveli",
                        transliterate::utf8_to_ascii("ვეპხის ტყაოსანი შოთა რუსთაველი"));
    $this->assert_equal("WoNengTunXiaBoLiErBuShangShenTi",
                        transliterate::utf8_to_ascii("我能吞下玻璃而不伤身体"));
  }
}
