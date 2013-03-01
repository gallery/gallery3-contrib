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

/**
 * Generate a PDF proof sheet on-the-fly of the current album or tag.
 * By Shad Laws.  Version 7, 2012/10/05
 * 
 * 2012/10/04, version 7
 *  Fixed bug related to the URL for proofsheets of tags (as opposed to albums)
 * 2012/06/15, version 6
 *  Fixed bug that could cause a crash when trying to use GD or GIF files (a typo from version 5)
 * 2012/04/05, version 5
 *  Added ability to include GIF thumbnails if GD is installed (FPDF uses GD)
 *  Changed behavior of unhandled file types - now provides missing image icon instead of throwing an exception
 * 2012/03/30, version 4
 *  Major rewrite.  Output is similar, but everything "under the hood" is much cleaner and (I hope) more easily understood and tweakable by other users.
 *  Header link is now an icon.
 *  Fixed encoding problems with diacritic marks and special characters.
 *  Now includes FPDF as a library instead of requiring a separate installtion.
 * 2012/03/28, version 3
 *  Made sizing configuration more flexible
 *  Prettified code so it's easier to understand and tweak as desired
 *  Added header link
 *  First version properly documented and linked to Gallery wiki
 * 2012/03/27, version 2
 *  Determines jpg/png type by file header, not extension, which makes it robust against misnamed extensions
 *  (N.B.: there's a bug in some movie modules that copy missing_movie.png as a jpg thumbnail!)
 *  Made caption size limits to prevent overrun
 * 2012/03/27, version 1
 *  Initial release
 */

/**
 * Include php library FPDF v1.7 by Olivier Plathey.
 * This gives us the tools to make PDFs without GhostScript.
 * The license for FPDF has no usage restrictions and thus 
 * can be included in this code.
 * More information can be found in the tutorials/docs
 * included in the library directory, and at:
 * http://www.fpdf.org
 */
require_once(MODPATH . 'proofsheet/lib/fpdf/fpdf.php');

class proofsheet_Controller extends Controller {
  public function makepdf($page_type, $container_type, $id) {

    /**
     * This part is largely copied from downloadalbum,
     * but does have some additions (headertext/headerlink)
     */
    switch($container_type) {
      case "album":
        $container = ORM::factory("item", $id);
        if (!$container->is_album()) {
          throw new Kohana_Exception('container is not an album: '.$container->relative_path());
        }

        $pdfname = (empty($container->name))
            ? 'Gallery.pdf' // @todo purified_version_of($container->title).'.pdf'
            : $container->name.'.pdf';
        $headerText = $container->title;
        $headerLink = $container->abs_url();
        break;

      case "tag":
        // @todo: if the module is not installed, it crash
        $container = ORM::factory("tag", $id);
        if (is_null($container->name)) {
          throw new Kohana_Exception('container is not a tag: '.$id);
        }

        $pdfname = $container->name.'.pdf';
        $headerText = $container->name;
        //$headerLink = $container->abs_url();
        $headerLink = url::abs_site("tag/{$container->id}/" . urlencode($container->name));
        break;

      default:
        throw new Kohana_Exception('unhandled container type: '.$container_type);
    }
    $files = $this->getFilesList($container);

    /**
     * Configure PDF file.  These are all of the parameters that are used to
     * format the proof sheet.  If you'd like to tweak the formatting, here's
     * where to do it.
     */
    switch($page_type) {
      case "ltr":
        // Setup for LTR 8.5" x 11" paper (215.9mm x 279.4mm)
        $cfg = array(
          'pageW'        =>   215.9, // mm
          'pageH'        =>   279.4, // mm
          'imageNumW'    =>       5, // integer number
          'imageNumH'    =>       5, // integer number
          'imageSizeW'   =>      36, // mm
          'imageSizeH'   =>      36, // mm
          'marginL'      =>      10, // mm
          'marginR'      =>      10, // mm
          'marginT'      =>      21, // mm (header goes in here)
          'marginB'      =>      20, // mm (footer goes in here)
          'headerSpace'  =>       2, // mm (header to top row of images and to link icon)
          'footerSpace'  =>       2, // mm (bottom row of captions to footer)
          'captionSpace' =>       1, // mm (bottom of image to caption)
          'headerFont'   => array(
            'name'       => 'Arial', // included are Arial/Helvetica, Courier, Times, Symbol, ZapfDingbats
            'size'       =>      14, // pt
            'style'      =>     'B', // combo of B, I, U
            'posn'       =>     'L', // combo of L, C, R
            'r'          =>       0, // Red 0-255
            'g'          =>       0, // Green 0-255
            'b'          =>       0),// Blue 0-255
          'footerFont'   => array(
            'name'       => 'Arial', // included are Arial/Helvetica, Courier, Times, Symbol, ZapfDingbats
            'size'       =>      12, // pt
            'style'      =>     'B', // combo of B, I, U
            'posn'       =>     'R', // combo of L, C, R
            'r'          =>       0, // Red 0-255
            'g'          =>       0, // Green 0-255
            'b'          =>       0),// Blue 0-255
          'captionFont'  => array(
            'name'       => 'Arial', // included are Arial/Helvetica, Courier, Times, Symbol, ZapfDingbats
            'size'       =>       8, // pt
            'style'      =>     'U', // combo of B, I, U
            'posn'       =>     'C', // combo of L, C, R
            'r'          =>       0, // Red 0-255
            'g'          =>       0, // Green 0-255
            'b'          =>     255),// Blue 0-255
        );
        break;
      case "a4":
        // Setup for A4 210mm x 297mm paper (8.27" x 11.69")
        $cfg = array(
          'pageW'        =>     210, // mm
          'pageH'        =>     297, // mm
          'imageNumW'    =>       5, // integer number
          'imageNumH'    =>       6, // integer number
          'imageSizeW'   =>      36, // mm
          'imageSizeH'   =>      36, // mm
          'marginL'      =>       8, // mm
          'marginR'      =>       8, // mm
          'marginT'      =>      19, // mm (header goes in here)
          'marginB'      =>      18, // mm (footer goes in here)
          'headerSpace'  =>       2, // mm (header to top row of images and to link icon)
          'footerSpace'  =>       2, // mm (bottom row of captions to footer)
          'captionSpace' =>       1, // mm (bottom of image to caption)
          'headerFont'   => array(
            'name'       => 'Arial', // included are Arial/Helvetica, Courier, Times, Symbol, ZapfDingbats
            'size'       =>      14, // pt
            'style'      =>     'B', // combo of B, I, U
            'posn'       =>     'L', // combo of L, C, R
            'r'          =>       0, // Red 0-255
            'g'          =>       0, // Green 0-255
            'b'          =>       0),// Blue 0-255
          'footerFont'   => array(
            'name'       => 'Arial', // included are Arial/Helvetica, Courier, Times, Symbol, ZapfDingbats
            'size'       =>      12, // pt
            'style'      =>     'B', // combo of B, I, U
            'posn'       =>     'R', // combo of L, C, R
            'r'          =>       0, // Red 0-255
            'g'          =>       0, // Green 0-255
            'b'          =>       0),// Blue 0-255
          'captionFont'  => array(
            'name'       => 'Arial', // included are Arial/Helvetica, Courier, Times, Symbol, ZapfDingbats
            'size'       =>       8, // pt
            'style'      =>     'U', // combo of B, I, U
            'posn'       =>     'C', // combo of L, C, R
            'r'          =>       0, // Red 0-255
            'g'          =>       0, // Green 0-255
            'b'          =>     255),// Blue 0-255
        );
        break;
      default:
        throw new Kohana_Exception('unhandled page type: '.$page_type);
    }

    // Here are some other parameters that need defining
    $cfg['footerTextPage']       = 'Page ';   // Note that this text isn't autofixed by translate module
    $cfg['footerTextSlash']      = ' / ';
    $cfg['headerLinkIconPath']   = MODPATH . 'proofsheet/images/ico-link.png';
    $cfgImageMissing['iconPath'] = MODPATH . 'proofsheet/images/image_missing.png'; 
    $cfgImageMissing['iconType'] = 'PNG';     // PNG or JPG is most robust, GIF okay only if GD is installed
    $pt2mm                       = 25.4/72;   // 25.4mm=1in=72pt

    // Derive a bunch more parameters.  These are all dependent on the above stuff.
    $cfg['headerH'] = $pt2mm * $cfg['headerFont']['size'];
    $cfg['footerH'] = $pt2mm * $cfg['footerFont']['size'];
    $cfg['captionH'] = $pt2mm * $cfg['captionFont']['size'];
    $cfg['imageSpaceW'] = ($cfg['pageW']-$cfg['marginL']-$cfg['marginR']-$cfg['imageNumW']*$cfg['imageSizeW']) / ($cfg['imageNumW']-1);
    $cfg['imageSpaceH'] = ($cfg['pageH']-$cfg['marginT']-$cfg['marginB']-$cfg['imageNumH']*$cfg['imageSizeH']-$cfg['captionH']-$cfg['captionSpace']) / ($cfg['imageNumH']-1);
    $linkInfo = getimagesize($cfg['headerLinkIconPath']);
    $cfg['headerLinkH'] = $cfg['headerH']; // I'm defining this to be the same as the text, but you can change it here.
    $cfg['headerLinkW'] = $linkInfo[0] / $linkInfo[1] * $cfg['headerLinkH'];
    $cfg['headerW'] = $cfg['pageW']-$cfg['marginL']-$cfg['marginR']-$cfg['headerLinkW']-$cfg['headerSpace'];
    $cfg['footerW'] = $cfg['pageW']-$cfg['marginL']-$cfg['marginR'];
    $cfg['captionW'] = $cfg['imageSizeW']; // I'm defining this to be the same as the image, but you can change it here.
    $cfg['headerX'] = $cfg['marginL'];
    $cfg['headerLinkX'] = $cfg['marginL']+$cfg['headerW'];
    $cfg['footerX'] = $cfg['marginL'];
    $cfg['headerY'] = $cfg['marginT']-$cfg['headerH']-$cfg['headerSpace'];
    $cfg['headerLinkY'] = $cfg['marginT']-$cfg['headerLinkH']-$cfg['headerSpace'];
    $cfg['footerY'] = $cfg['pageH']-$cfg['marginB']+$cfg['footerSpace'];
    $cfg['imageNum'] = $cfg['imageNumW']*$cfg['imageNumH'];
    $cfgImageMissing['iconInfo'] = getimagesize($cfgImageMissing['iconPath']);
    $cfgImageMissing['GDflag'] = graphics::detect_toolkits()->gd->installed; // FPDF uses GD to convert GIFs

    /**
     * Initialize and build PDF... the main routine.  Note that almost all of the
     * useful configuration parameters are already defined above.
     */

    // Initialize PDF, disable automatic margins and page breaks
    $pdf = new proofsheet_PDF('P', 'mm', array($cfg['pageW'],$cfg['pageH']) );
    $pdf->SetMargins(0,0);
    $pdf->SetAutoPageBreak(0);

    // Build the PDF
    $numpages = floor(count($files)/$cfg['imageNum'])+1;
    $i = 0;
    foreach($files as $f_path => $f_info) {
      // Initialize new pages, add header and footer
      if (($i % $cfg['imageNum'])==0) {
        $pdf->AddPage();
        $pdf->printText($headerText, $cfg['headerFont'], $cfg['headerX'], $cfg['headerY'], $cfg['headerW'], $cfg['headerH']);
        $pdf->printImage($cfg['headerLinkIconPath'], $cfg['headerLinkX'], $cfg['headerLinkY'], $cfg['headerLinkW'], $cfg['headerLinkH'], $headerLink, $cfgImageMissing);
        $footerText = $cfg['footerTextPage'] . strval(floor($i/$cfg['imageNum'])+1) . $cfg['footerTextSlash'] . strval($numpages);
        $pdf->printText($footerText, $cfg['footerFont'], $cfg['footerX'], $cfg['footerY'], $cfg['footerW'], $cfg['footerH']);
      }
      // Add thumbnail and caption
      $x = $cfg['marginL'] + ($cfg['imageSizeW']+$cfg['imageSpaceW']) * (      $i                    % $cfg['imageNumW']);
      $y = $cfg['marginT'] + ($cfg['imageSizeH']+$cfg['imageSpaceH']) * (floor($i/$cfg['imageNumW']) % $cfg['imageNumH']);
      $pdf->printImage($f_path, $x, $y, $cfg['imageSizeW'], $cfg['imageSizeH'], null, $cfgImageMissing);
      $pdf->printText($f_info['name'], $cfg['captionFont'], $x, $y+$cfg['imageSizeH']+$cfg['captionSpace'], $cfg['captionW'], $cfg['captionH'], $f_info['url']);
      // Increment index and loop
      $i++;
    }

    /**
     * Output the PDF file.  I wrote it in two versions (one should always be commented out).
     */
    // Using a method similar to downloadalbum
    $pdfstring = $pdf->Output('','S');
    $this->prepareOutput();
    $this->sendHeaders($pdfname, strlen($pdfstring));
    echo $pdfstring;

    // Using FPDF directly
    //$pdf->Output($pdfname,'I');

  }

  /**
   * Return the files that must be included in the archive.
   * This is largely borrowed from downloadalbum, but does have
   * significant modifications.
   */
  private function getFilesList($container) {
    $files = array();

    if( $container instanceof Item_Model && $container->is_album() ) {
      $items = $container->viewable()
          ->descendants(null, null, array(array("type", "<>", "album")));

      foreach($items as $i) {
        if (!access::can('view_full', $i)) {
          continue;
        }

        $thumb_realpath = realpath($i->thumb_path());
        if (!is_readable($thumb_realpath)) {
          continue;
        }

        $files[$thumb_realpath] = array('url' => $i->abs_url(), 'name' => $i->title);
      }

    } else if( $container instanceof Tag_Model ) {
      $items = $container->items();
      foreach($items as $i) {
        if (!access::can('view_full', $i)) {
          continue;
        }

        if( $i->is_album() ) {
          foreach($this->getFilesList($i) as $f_name => $f_info) {
            $files[$f_name] = $f_info;
          }

        } else {
          $thumb_realpath = realpath($i->thumb_path());
          if (!is_readable($thumb_realpath)) {
            continue;
          }

          $files[$thumb_realpath] = array('url' => $i->abs_url(), 'name' => $i->title);

        }
      }
    }

    if (count($files) === 0) {
      throw new Kohana_Exception('no thumb files in ['.$container->name.']');
    }

    return $files;
  }


  /**
   * See system/helpers/download.php
   * This is borrowed from downloadalbum without changes.
   */
  private function prepareOutput() {
    // Close output buffers
    Kohana::close_buffers(FALSE);
    // Clear any output
    Event::add('system.display', create_function('', 'Kohana::$output = "";'));
  }

  /**
   * See system/helpers/download.php
   * This is borrowed from downloadalbum without changes.
   */
  private function sendHeaders($filename, $filesize = null) {
    if (!is_null($filesize)) {
      header('Content-Length: '.$filesize);
    }

    // Retrieve MIME type by extension
    $mime = Kohana::config('mimes.'.strtolower(substr(strrchr($filename, '.'), 1)));
    $mime = empty($mime) ? 'application/octet-stream' : $mime[0];
    header("Content-Type: $mime");
    header('Content-Transfer-Encoding: binary');

    // Send headers necessary to invoke a "Save As" dialog
    header('Content-Disposition: attachment; filename="'.$filename.'"');

    // Prevent caching
    header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');

    $pragma = 'no-cache';
    $cachecontrol = 'no-cache, max-age=0';

    // request::user_agent('browser') seems bugged
    if (request::user_agent('browser') === 'Internet Explorer'
        || stripos(request::user_agent(), 'msie') !== false
        || stripos(request::user_agent(), 'internet explorer') !== false)
    {
      if (request::protocol() === 'https') {
        // See http://support.microsoft.com/kb/323308/en-us
        $pragma = 'cache';
        $cachecontrol = 'private';

      } else if (request::user_agent('version') <= '6.0') {
        $pragma = '';
        $cachecontrol = 'must-revalidate, post-check=0, pre-check=0';
      }
    }

    header('Pragma: '.$pragma);
    header('Cache-Control: '.$cachecontrol);
  }
}

class proofsheet_PDF extends FPDF {

  /**
   * Print text (header, footer, or caption) with link, formatted as in cfg.
   * It converts UTF-8 back to CP1252, which is used by FPDF.
   * This will trim formatted text to fit and add ellipsis if needed.
   */
  function printText($text, $font, $x, $y, $w, $h, $link = null) {
    $ellipsis = '…'; // ASCII character 133
    // Convert from UTF-8 back to CP1252
    $text = iconv('utf-8','cp1252',$text);
    // Set color, font, and position
    $this->SetTextColor($font['r'],$font['g'],$font['b']);
    $this->SetFont($font['name'],$font['style'],$font['size']);
    $this->SetXY($x, $y);
    // Trim text if needed
    if (($this->GetStringWidth($text)) > $w) {
      // Keep trimming until the size, with ellipsis, is small enough
      while (($this->GetStringWidth($text.$ellipsis)) > $w) {
        $text = substr($text,0,strlen($text)-1);
      }
      // Add the ellipsis to the shortened text
      $text = $text.$ellipsis;
    }
    // Create text cell
    $this->Cell($w,$h,$text,0,0,$font['posn'],false,$link);
  }
      
  /**
   * Print image.  This is basically a wrapper around the FPDF image function,
   * except that it determines the file type independent of the file extension
   * and automatically resizes to main aspect ratio within the defined space.
   * Note that this provides robustness for images with incorrect filenames, such
   * as missing_movie.png being called a jpg when copied as a thumbnail in v3.0.2.
   */
  function printImage($imagePath, $x, $y, $w, $h, $link = null, $cfgImageMissing) {
    $imageInfo = getimagesize($imagePath); // [0]=w, [1]=h, [2]=type (1=GIF, 2=JPG, 3=PNG)
    // Figure out the filetype
    switch($imageInfo[2]) {
      case 3:
        $imageType = 'PNG';
        break;
      case 2:
        $imageType = 'JPG';
        break;
      case 1:
        if ($cfgImageMissing['GDflag']) {
          $imageType = 'GIF';
          break;
        }
      default:
        // use the missing image icon instead
        $imagePath = $cfgImageMissing['iconPath'];
        $imageType = $cfgImageMissing['iconType'];
        $imageInfo = $cfgImageMissing['iconInfo'];
    }
    // Determine image orientation and create image
    $ratioWH = ($imageInfo[0]/$w) / ($imageInfo[1]/$h);
    if ($ratioWH>1) {
        $this->image($imagePath, $x, $y+(1-1/$ratioWH)*$h/2, $w, 0, $imageType, $link);
    } else {
        $this->image($imagePath, $x+(1-$ratioWH)*$w/2,   $y, 0, $h, $imageType, $link);
    }
  }
}
