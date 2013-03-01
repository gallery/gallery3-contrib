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

class Form_Input extends Form_Input_Core {

  /**
   * Custom validation rule: numrange
   *  0 args : returns error if not numeric
   *  1 arg  : returns error if not numeric OR if below min
   *  2 args : returns error if not numeric OR if below min OR if above max
   */
	protected function rule_numrange($min = null, $max = null) {
    if (is_numeric($this->value)) {
      if (!is_null($min) && ($this->value < $min)) {
        // below min
        $this->errors['numrange'] = true;
        $this->error_messages['numrange'] = t('Value is below minimum of').' '.$min;
      } elseif (!is_null($max) && ($this->value > $max)) {
        // above max
        $this->errors['numrange'] = true;
        $this->error_messages['numrange'] = t('Value is above maximum of').' '.$max;;
      }
    } else {
      // not numeric
      $this->errors['numrange'] = true;
      $this->error_messages['numrange'] = t('Value is not numeric');
    }
	}

  /**
   * Custom validation rule: color
   *  returns no error if string is formatted as #hhhhhh OR if string is empty
   *  to exclude the empty case, add "required" as another rule
   */
	protected function rule_color() {
    if (preg_match("/^#[0-9A-Fa-f]{6}$|^$/", $this->value) == 0) {
      $this->errors['color'] = true;
      $this->error_messages['color'] = t('Color is not in #hhhhhh format');
    }
  }
}