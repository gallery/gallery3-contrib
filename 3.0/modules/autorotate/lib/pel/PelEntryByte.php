<?php

/**
 *  PEL: PHP Exif Library.  A library with support for reading and
 *  writing all Exif headers in JPEG and TIFF images using PHP.
 *
 *  Copyright (C) 2004, 2005, 2006, 2007  Martin Geisler.
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program in the file COPYING; if not, write to the
 *  Free Software Foundation, Inc., 51 Franklin St, Fifth Floor,
 *  Boston, MA 02110-1301 USA
 */

/* $Id$ */


/**
 * Classes used to hold bytes, both signed and unsigned.  The {@link
 * PelEntryWindowsString} class is used to manipulate strings in the
 * format Windows XP needs.
 *
 * @author Martin Geisler <mgeisler@users.sourceforge.net>
 * @version $Revision$
 * @date $Date$
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public
 * License (GPL)
 * @package PEL
 */

/**#@+ Required class definitions. */
require_once('PelEntryNumber.php');
/**#@-*/


/**
 * Class for holding unsigned bytes.
 *
 * This class can hold bytes, either just a single byte or an array of
 * bytes.  The class will be used to manipulate any of the Exif tags
 * which has format {@link PelFormat::BYTE}.
 *
 * @author Martin Geisler <mgeisler@users.sourceforge.net>
 * @package PEL
 */
class PelEntryByte extends PelEntryNumber {

    /**
     * Make a new entry that can hold an unsigned byte.
     *
     * The method accept several integer arguments.  The {@link
     * getValue} method will always return an array except for when a
     * single integer argument is given here.
     *
     * @param PelTag the tag which this entry represents.  This
     * should be one of the constants defined in {@link PelTag}
     * which has format {@link PelFormat::BYTE}.
     *
     * @param int $value... the byte(s) that this entry will represent.
     * The argument passed must obey the same rules as the argument to
     * {@link setValue}, namely that it should be within range of an
     * unsigned byte, that is between 0 and 255 (inclusive).  If not,
     * then a {@link PelOverflowException} will be thrown.
     */
    function __construct($tag /* $value... */) {
        $this->tag    = $tag;
        $this->min    = 0;
        $this->max    = 255;
        $this->format = PelFormat::BYTE;

        $value = func_get_args();
        array_shift($value);
        $this->setValueArray($value);
    }


    /**
     * Convert a number into bytes.
     *
     * @param int the number that should be converted.
     *
     * @param PelByteOrder one of {@link PelConvert::LITTLE_ENDIAN} and
     * {@link PelConvert::BIG_ENDIAN}, specifying the target byte order.
     *
     * @return string bytes representing the number given.
     */
    function numberToBytes($number, $order) {
        return chr($number);
    }

}


/**
 * Class for holding signed bytes.
 *
 * This class can hold bytes, either just a single byte or an array of
 * bytes.  The class will be used to manipulate any of the Exif tags
 * which has format {@link PelFormat::BYTE}.
 *
 * @author Martin Geisler <mgeisler@users.sourceforge.net>
 * @package PEL
 */
class PelEntrySByte extends PelEntryNumber {

    /**
     * Make a new entry that can hold a signed byte.
     *
     * The method accept several integer arguments.  The {@link getValue}
     * method will always return an array except for when a single
     * integer argument is given here.
     *
     * @param PelTag the tag which this entry represents.  This
     * should be one of the constants defined in {@link PelTag}
     * which has format {@link PelFormat::BYTE}.
     *
     * @param int $value... the byte(s) that this entry will represent.
     * The argument passed must obey the same rules as the argument to
     * {@link setValue}, namely that it should be within range of a
     * signed byte, that is between -128 and 127 (inclusive).  If not,
     * then a {@link PelOverflowException} will be thrown.
     */
    function __construct($tag /* $value... */) {
        $this->tag    = $tag;
        $this->min    = -128;
        $this->max    = 127;
        $this->format = PelFormat::SBYTE;

        $value = func_get_args();
        array_shift($value);
        $this->setValueArray($value);
    }


    /**
     * Convert a number into bytes.
     *
     * @param int the number that should be converted.
     *
     * @param PelByteOrder one of {@link PelConvert::LITTLE_ENDIAN} and
     * {@link PelConvert::BIG_ENDIAN}, specifying the target byte order.
     *
     * @return string bytes representing the number given.
     */
    function numberToBytes($number, $order) {
        return chr($number);
    }

}


/**
 * Class used to manipulate strings in the format Windows XP uses.
 *
 * When examining the file properties of an image in Windows XP one
 * can fill in title, comment, author, keyword, and subject fields.
 * Filling those fields and pressing OK will result in the data being
 * written into the Exif data in the image.
 *
 * The data is written in a non-standard format and can thus not be
 * loaded directly --- this class is needed to translate it into
 * normal strings.
 *
 * It is important that entries from this class are only created with
 * the {@link PelTag::XP_TITLE}, {@link PelTag::XP_COMMENT}, {@link
 * PelTag::XP_AUTHOR}, {@link PelTag::XP_KEYWORD}, and {@link
 * PelTag::XP_SUBJECT} tags.  If another tag is used the data will no
 * longer be correctly decoded when reloaded with PEL. (The data will
 * be loaded as an {@link PelEntryByte} entry, which isn't as useful.)
 *
 * This class is to be used as in
 * <code>
 * $title = $ifd->getEntry(PelTag::XP_TITLE);
 * print($title->getValue());
 * $title->setValue('My favorite cat');
 * </code>
 * or if no entry is present one can add a new one with
 * <code>
 * $title = new PelEntryWindowsString(PelTag::XP_TITLE, 'A cute dog.');
 * $ifd->addEntry($title);
 * </code>
 *
 * @author Martin Geisler <mgeisler@users.sourceforge.net>
 * @package PEL
 */
class PelEntryWindowsString extends PelEntry {

    /**
     * The string hold by this entry.
     *
     * This is the string that was given to the {@link __construct
     * constructor} or later to {@link setValue}, without any extra NULL
     * characters or any such nonsense.
     *
     * @var string
     */
    private $str;


    /**
     * Make a new PelEntry that can hold a Windows XP specific string.
     *
     * @param int the tag which this entry represents.  This should be
     * one of {@link PelTag::XP_TITLE}, {@link PelTag::XP_COMMENT},
     * {@link PelTag::XP_AUTHOR}, {@link PelTag::XP_KEYWORD}, and {@link
     * PelTag::XP_SUBJECT} tags.  If another tag is used, then this
     * entry will be incorrectly reloaded as a {@link PelEntryByte}.
     *
     * @param string the string that this entry will represent.  It will
     * be passed to {@link setValue} and thus has to obey its
     * requirements.
     */
    function __construct($tag, $str = '') {
        $this->tag    = $tag;
        $this->format = PelFormat::BYTE;
        $this->setValue($str);
    }


    /**
     * Give the entry a new value.
     *
     * This will overwrite the previous value.  The value can be
     * retrieved later with the {@link getValue} method.
     *
     * @param string the new value of the entry.  This should be use the
     * Latin-1 encoding and be given without any extra NULL characters.
     */
    function setValue($str) {
        $l = strlen($str);

        $this->components = 2 * ($l + 1);
        $this->str        = $str;
        $this->bytes      = '';
        for ($i = 0; $i < $l; $i++)
        $this->bytes .= $str{$i} . chr(0x00);

        $this->bytes .= chr(0x00) . chr(0x00);
    }


    /**
     * Return the string of the entry.
     *
     * @return string the string held, without any extra NULL
     * characters.  The string will be the same as the one given to
     * {@link setValue} or to the {@link __construct constructor}.
     */
    function getValue() {
        return $this->str;
    }


    /**
     * Return the string of the entry.
     *
     * This methods returns the same as {@link getValue}.
     *
     * @param boolean not used.
     *
     * @return string the string held, without any extra NULL
     * characters.  The string will be the same as the one given to
     * {@link setValue} or to the {@link __construct constructor}.
     */
    function getText($brief = false) {
        return $this->str;
    }

}

