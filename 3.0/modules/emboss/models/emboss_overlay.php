<?php defined('SYSPATH') or die('No direct script access.');
/*************************************************************************
 * Copyright (C) 2012  Michel A. Mayer                                   *
 *                                                                       *
 * This program is free software: you can redistribute it and/or modify  *
 * it under the terms of the GNU General Public License as published by  *
 * the Free Software Foundation, either version 3 of the License, or     *
 * (at your option) any later version.                                   *
 *                                                                       *
 * This program is distributed in the hope that it will be useful,       *
 * but WITHOUT ANY WARRANTY; without even the implied warranty of        *
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         *
 * GNU General Public License for more details.                          *
 *                                                                       *
 * You should have received a copy of the GNU General Public License     *
 * along with this program.  If not, see <http://www.gnu.org/licenses/>. *
 *************************************************************************/
class Emboss_Overlay_Model_Core extends ORM {
  protected $sorting = array('width' => 'desc', 'height' => 'desc');

  public function score($W,$H,$function)
  {
    /*************************************************************
     * (W,H) = Image (Width,Height)
     * (w,h) = Overlay (width,height)
     *************************************************************/

    $w = $this->width;
    $h = $this->height;
    if( ($w>$W) || ($h>$H) ) { return 0; }

    /*************************************************************
     * Minimize Margin Method
     *************************************************************
     * Score =  (W^2 + H^2) - ((W-w)^2 + (H-h)^2)
     *       =  (W^2 - (W-w)^2) + (H^2 - (H-h)^2)
     *       =  (2Ww - w^2) + (2Hh - h^2)
     *       =  (2W-w)w + (2H-h)h
     *************************************************************/
    
    if($function == 'margin') {
      $score = ( (2*$W - $w)*$w + (2*$H - $h)*$h );
    }

    /*************************************************************
     * Aspect Ratio Weighted
     *************************************************************
     * if h < w*(H/W)
     *   peak value = area on diagonal (w*h)
     *   null value = 0 on w axis (h=0)
     *   quadratic fit between:
     *      Score = W/H h^2
     * if w < h*(W/H)
     *      Score = H/W w^2  (by symmetry)
     *************************************************************/
    
    else if($function == 'diag') {
      if($h*$W < $w*$H) {
        $score = $h*$h*($W/$H);
      } else {
        $score = $w*$w*($H/$W);
      }
    }

    /*************************************************************
     * Area Method  (Default if no match to $function)
     *************************************************************
     * Score =  w * h
     *************************************************************/
    
    else {
      $score = $w * $h;
    }

    return $score;
  }

  public function area()
  {
    return $this->width * $this->height;
  }
  
}