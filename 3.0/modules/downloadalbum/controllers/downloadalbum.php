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
class downloadalbum_Controller extends Controller {

  /**
   * Generate a ZIP on-the-fly.
   */
  public function zip($id) {
    $album = $this->init($id);
    $files = $this->getFilesList($album);

    // Calculate ZIP size (look behind for details)
    $zipsize = 22;
    foreach($files as $f) {
      $zipsize += 76 + 2*strlen($f) + filesize($f);
    }

    // Send headers
    $this->prepareOutput();
    $this->sendHeaders($album->name.'.zip', $zipsize);

    // Generate and send ZIP file
    // http://www.pkware.com/documents/casestudies/APPNOTE.TXT (v6.3.2)
    $lfh_offset = 0;
    $cds = '';
    $cds_offset = 0;
    foreach($files as $f) {
      $f_namelen = strlen($f);
      $f_size = filesize($f);
      $f_mtime = $this->unix2dostime(filemtime($f));
      $f_crc32 = $this->fixBug45028(hexdec(hash_file('crc32b', $f, false)));

      // Local file header
      echo pack('VvvvVVVVvva' . $f_namelen,
          0x04034b50,         // local file header signature (4 bytes)
          0x0a,               // version needed to extract (2 bytes) => 1.0
          0x0800,             // general purpose bit flag (2 bytes) => UTF-8
          0x00,               // compression method (2 bytes) => store
          $f_mtime,           // last mod file time and date (4 bytes)
          $f_crc32,           // crc-32 (4 bytes)
          $f_size,            // compressed size (4 bytes)
          $f_size,            // uncompressed size (4 bytes)
          $f_namelen,         // file name length (2 bytes)
          0,                  // extra field length (2 bytes)

          $f                  // file name (variable size)
                              // extra field (variable size) => n/a
      );

      // File data
      readfile($f);

      // Data descriptor (n/a)

      // Central directory structure: File header
      $cds .= pack('VvvvvVVVVvvvvvVVa' . $f_namelen,
          0x02014b50,         // central file header signature (4 bytes)
          0x031e,             // version made by (2 bytes) => v3 / Unix
          0x0a,               // version needed to extract (2 bytes) => 1.0
          0x0800,             // general purpose bit flag (2 bytes) => UTF-8
          0x00,               // compression method (2 bytes) => store
          $f_mtime,           // last mod file time and date (4 bytes)
          $f_crc32,           // crc-32 (4 bytes)
          $f_size,            // compressed size (4 bytes)
          $f_size,            // uncompressed size (4 bytes)
          $f_namelen,         // file name length (2 bytes)
          0,                  // extra field length (2 bytes)
          0,                  // file comment length (2 bytes)
          0,                  // disk number start (2 bytes)
          0,                  // internal file attributes (2 bytes)
          0x81b40000,         // external file attributes (4 bytes) => chmod 664
          $lfh_offset,        // relative offset of local header (4 bytes)

          $f                  // file name (variable size)
                              // extra field (variable size) => n/a
                              // file comment (variable size) => n/a
      );

      // Update local file header/central directory structure offset
      $cds_offset = $lfh_offset += 30 + $f_namelen + $f_size;
    }

    // Archive decryption header (n/a)
    // Archive extra data record (n/a)

    // Central directory structure: Digital signature (n/a)
    echo $cds; // send Central directory structure

    // Zip64 end of central directory record (n/a)
    // Zip64 end of central directory locator (n/a)

    // End of central directory record
    $numfile = count($files);
    $cds_len = strlen($cds);
    echo pack('VvvvvVVv',
        0x06054b50,             // end of central dir signature (4 bytes)
        0,                      // number of this disk (2 bytes)
        0,                      // number of the disk with the start of
                                // the central directory (2 bytes)
        $numfile,               // total number of entries in the
                                // central directory on this disk (2 bytes)
        $numfile,               // total number of entries in the
                                // central directory (2 bytes)
        $cds_len,               // size of the central directory (4 bytes)
        $cds_offset,            // offset of start of central directory
                                // with respect to the
                                // starting disk number (4 bytes)
        0                       // .ZIP file comment length (2 bytes)
                                // .ZIP file comment (variable size)
    );
  }


  /**
   * Init
   */
  private function init($id) {
    $item = ORM::factory("item", $id);

    // Only send an album
    if (!$item->is_album()) {
      // @todo: throw an exception?
      Kohana::log('error', 'item is not an album: '.$item->relative_path());
      exit;
    }

    // Must have view_full to download the originals files
    access::required("view_full", $item);

    return $item;
  }

  /**
   * Return the files that must be included in the archive.
   */
  private function getFilesList($album) {
    $files = array();

    // Go to the parent of album so the ZIP will not contains all the
    // server hierarchy
    if (!chdir($album->file_path().'/../')) {
      // @todo: throw an exception?
      Kohana::log('error', 'unable to chdir('.$item->file_path().'/../)');
      exit;
    }
    $cwd = getcwd();

    $items = $album->viewable()
        ->descendants(null, null, array(array("type", "<>", "album")));
    foreach($items as $i) {
      if (!access::can('view_full', $i)) {
        continue;
      }

      $relative_path = str_replace($cwd.'/', '', realpath($i->file_path()));
      if (!is_readable($relative_path)) {
        continue;
      }

      $files[] = $relative_path;
    }

    if (count($files) === 0) {
      // @todo: throw an exception?
      Kohana::log('error', 'no zippable files in ['.$album->relative_path().']');
      exit;
    }

    return $files;
  }


  /**
   * See system/helpers/download.php
   */
  private function prepareOutput() {
    // Close output buffers
    Kohana::close_buffers(FALSE);
    // Clear any output
    Event::add('system.display', create_function('', 'Kohana::$output = "";'));
  }

  /**
   * See system/helpers/download.php
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

  /**
   * @return integer             DOS date and time
   * @param  integer _timestamp  Unix timestamp
   * @desc                       returns DOS date and time of the timestamp
   */
  private function unix2dostime($timestamp)
  {
    $timebit = getdate($timestamp);

    if ($timebit['year'] < 1980) {
      return (1 << 21 | 1 << 16);
    }

    $timebit['year'] -= 1980;

    return ($timebit['year']    << 25 | $timebit['mon']     << 21 |
            $timebit['mday']    << 16 | $timebit['hours']   << 11 |
            $timebit['minutes'] << 5  | $timebit['seconds'] >> 1);
  }

  /**
   * See http://bugs.php.net/bug.php?id=45028
   */
  private function fixBug45028($hash) {
    return (version_compare(PHP_VERSION, '5.2.7', '<'))
      ? (($hash & 0x000000ff) << 24) + (($hash & 0x0000ff00) << 8)
          + (($hash & 0x00ff0000) >> 8) + (($hash & 0xff000000) >> 24)
      : $hash;
  }
}
