<?php
/*
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
 * This class provides an API for parsing Gallery 1 data files
 * Updated to Gallery 3 by Thomas E. Horner
 */
class Gallery1DataParser {

    /**
     * Verify that the given path holds an albumdb and that the albumdb is readable
     *
     * @param string $path Path to albums
     * @return boolean True if the path is valid, otherwise false
     */
    function isValidAlbumsPath($path) {
        if (file_exists($path . 'albumdb.dat')
                && is_readable($path . 'albumdb.dat')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Load and return user metadata from given file
     *
     * @param string $fileName Path to user file to unserialize
     * @return array GalleryStatus a status code,
     *               object Unserialized user metadata
     */
    function loadFile($fileName) {
				$fileName = str_replace('//','/',$fileName);
        if (!file_exists($fileName) || !is_readable($fileName)) {
            if (file_exists($fileName . '.bak') &&
                    is_readable($fileName . '.bak')) {
                $fileName .= '.bak';
            } else {
                message::warning(
                  t('Gallery1 inconsistency: Missing or not readable file %file',
                  array('file' => $fileName)));
                return array('ERROR_BAD_PARAMETER', null);
            }
        }
        $tmp = file($fileName);

        if (empty($tmp)) {
            message::warning(
              t('Gallery1 inconsistency: Empty file %file',
              array('file' => $fileName)));
            return array('ERROR_MISSING_VALUE', null);
        }

        $tmp = join('', $tmp);

        /*
         * We renamed User.php to Gallery_User.php in v1.2, so port forward
         * any saved user objects.
         */
        if (stripos($tmp, 'O:4:"user"')!==false) {
            $tmp = str_ireplace('O:4:"user"', 'O:12:"gallery_user"', $tmp);
        }
        
        /*
         * Gallery3 already contains a class named Image so
         * we need to rename the G1 Image class to G1Img here
         */
        if (stripos($tmp, 'O:5:"image"')!==false) {
            $tmp = str_ireplace('O:5:"image"', 'O:5:"G1Img"', $tmp);
        }

        $object = unserialize($tmp);
        return array(null, $object);
    }

    /**
     * Fetch an array of albums from a given path
     *
     * @param string $path Path to albums directory
     * @return array GalleryStatus a status code,
     *               array of objects
     */
    function getAlbumList($path) {
        
        list ($ret, $albumOrder) = Gallery1DataParser::loadFile($path . 'albumdb.dat');
        if ($ret) {
            return array($ret, null);
        }

        /* TODO: check that there is an $albumOrder */
        foreach ($albumOrder as $albumName) {
            list ($ret, $albumFields) =
                Gallery1DataParser::loadAlbumFields($path . $albumName . DIRECTORY_SEPARATOR);
            if ($ret) {
                return array($ret,'');
            }
            $albumList[$albumName] = $albumFields;
        }

        return array(null, $albumList);
    }

    /**
     * Fetch an associative array of parentalbum names from a given path to gallery1 albums
     *
     * @param string $path Path to albums directory
     * @return array GalleryStatus a status code,
     *               array of albums and their parents
     */
    function getParentAlbumList($path) {
        
        list ($ret, $albumOrder) = Gallery1DataParser::loadFile($path . 'albumdb.dat');
        if ($ret) {
            return array($ret, null);
        }

        foreach ($albumOrder as $albumName) {
            list ($ret, $albumFields) =
                Gallery1DataParser::loadAlbumFields($path . $albumName . DIRECTORY_SEPARATOR);
            if ($ret) {
                return array($ret,'');
            }
            $parentAlbumList[$albumName] = $albumFields['parentAlbumName'];
        }
        return array(null, $parentAlbumList);
    }

    /**
     * Build a data tree of albums
     *
     * @param string $path Path to albums directory
     * @return array GalleryStatus a status code,
     *               array of albumnames and their children, and their children...
     */
    function getAlbumHierarchy($path) {
        list ($ret, $parentAlbumList) = Gallery1DataParser::getParentAlbumList($path);
        if ($ret) {
            return array($ret, null);
        }

        foreach ($parentAlbumList as $myName => $parentName) {
            if (!isset($tempAlbums[$myName])) {
                $tempAlbums[$myName] = array();
            }
            if (empty($parentName) || $parentName == '.root') {
                $hierarchy[$myName] = &$tempAlbums[$myName];
            } else {
                if (!isset($tempAlbums[$parentName])) {
                    $tempAlbums[$parentName] = array();
                }
                $tempAlbums[$parentName][$myName] = &$tempAlbums[$myName];
            }
        }
        return array(null, $hierarchy);
    }

    /**
     * Fetch an array of albums with no parents
     *
     * @param string $path Path to albums directory
     * @return array GalleryStatus a status code,
     *               array of albumnames
     */
    function getRootAlbums($path) {
        
        list ($ret, $albumOrder) = Gallery1DataParser::loadFile($path . 'albumdb.dat');
        if ($ret) {
            return array($ret, null);
        }

        foreach ($albumOrder as $albumName) {
            list ($ret, $albumFields) =
                Gallery1DataParser::loadAlbumFields($path . $albumName . DIRECTORY_SEPARATOR);
            if ($ret) {
                return array($ret,'');
            }
            if ($albumFields['parentAlbumName'] == '.root') {
                $rootAlbums[] = $albumName;
            }
        }
        return array(null, $rootAlbums);
    }

    /**
     * Load and return album metadata from given directory
     *
     * @param string $path Path to album directory
     * @return array GalleryStatus a status code,
     *               object Unserialized album metadata
     */
    function loadAlbumFields($path) {
        
        $tmp = trim($path);
        if ($tmp[strlen($tmp)-1] != DIRECTORY_SEPARATOR) {
            $tmp .= DIRECTORY_SEPARATOR;
        }
        $path = trim($tmp);
        $albumPath = explode(DIRECTORY_SEPARATOR, $path);
        $albumName = $albumPath[count($albumPath)-2];
        list ($ret, $album) = Gallery1DataParser::loadFile($path . 'album.dat');
        if ($ret) {
            return array($ret, null);
        }
        $album->fields['name'] = $albumName;
        if (!$album->fields['parentAlbumName']) {
            $album->fields['parentAlbumName'] = '.root';
        }
        return array(null, $album->fields);
    }

    /**
     * Count the number of photos in an album dir
     *
     * @param string $path Path to album directory
     * @return array GalleryStatus a status code,
     *               integer Count of photos
     */
    function getPhotoCount($path) {
        list ($ret, $photos) = Gallery1DataParser::loadFile($path . 'photos.dat');
        if ($ret) {
            return array($ret, null);
        }
        $photoCount = count($photos);
        return array(null, $photoCount);
    }

    /**
     * Count the photo data from an album dir
     *
     * @param string $path Path to album directory
     * @return array GalleryStatus a status code,
     *               array Galleryphotos
     */
    function getPhotos($path) {
        
        list ($ret, $photos) = Gallery1DataParser::loadFile($path . DIRECTORY_SEPARATOR . 'photos.dat');
        if ($ret) {
            return array($ret, null);
        }
        return array(null, $photos);
    }

    /**
     * Load user uids from path
     *
     * @param string $path Path to album directory
     * @return array GalleryStatus a status code,
     *               array Associative array of uids and usernames
     */
    function getUserUids($path) {
        static $uids;
        if (!isset($uids[$path])) {
            if (!isset($uids)) {
                $uids = array();
            }
            
            list ($ret, $userDB) =
                Gallery1DataParser::loadFile($path . '.users' . DIRECTORY_SEPARATOR . 'userdb.dat');
            if ($ret) {
                return array($ret, null);
            }
            $uids[$path] = array();
            foreach ($userDB->userMap as $username => $uid) {
                if (Gallery1DataParser::isValidUid($path, $uid)
                    && !Gallery1DataParser::isValidUid($path, $username)
                    && !preg_match('/nobody|everybody|loggedin/i', $username)) {
                    $uids[$path][$uid] = $username;
                }
            }
        }
        return array(null, $uids[$path]);
    }

    /**
     * Validate user id string from gallery v1.x.x
     *
     * @param string $uid Uid to be tested
     * @return boolean
     */
    function isValidUidString($uid) {
        if (preg_match('/^\d{9,}_\d+$/', $uid)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Validate user id from gallery v1.x.x
     *
     * @param string $path
     * @param string $uid Uid to be tested
     * @return boolean
     */
    function isValidUid($path, $uid) {
        static $valid;
        if (!isset($valid[$path][$uid])) {
            if (!isset($valid)) {
                $valid = array();
            }
            if (!isset($valid[$path])) {
                $valid[$path] = array();
            }
                        if (Gallery1DataParser::isValidUidString($uid)) {
                list ($ret, $fields) = Gallery1DataParser::getUserFieldsByUid($path, $uid);
                if (!$ret) {
                    $valid[$path][$uid] = TRUE;
                } else {
                    $valid[$path][$uid] = FALSE;
                }
            } else {
                $valid[$path][$uid] = FALSE;
            }
        }
        return $valid[$path][$uid];
    }

    /**
     * Load user metadata given a path and uid
     *
     * @param string $path Path to album directory
     * @param string $uid Uid to import
     * @return array GalleryStatus a status code,
     *               array User metadata
     */
    function getUserFieldsByUid($path, $uid) {
        static $fields;

        if (!isset($fields[$path][$uid])) {
            if (!isset($fields)) {
                $fields = array();
            }
            if (!isset($fields[$path])) {
                $fields[$path] = array();
            }
                        $fields[$path][$uid] = array();
            if (Gallery1DataParser::isValidUidString($uid)) {
                list ($ret, $user) = Gallery1DataParser::loadFile($path . '.users' . DIRECTORY_SEPARATOR . $uid);
                if ($ret) {
                    return array($ret, null);
                }
                foreach ($user as $key => $value) {
                    $fields[$path][$uid][$key] = $value;
                }
            }
        }
        return array(null, $fields[$path][$uid]);
    }

    /**
     * Load user metadata given a path and username
     *
     * @param string $path Path to album directory
     * @param string $username Username to import
     * @return array GalleryStatus a status code,
     *               array User metadata
     */
    function getUserFieldsByUsername($path, $username) {
        list ($ret, $uids) = Gallery1DataParser::getUserUids($path);
        if ($ret) {
            return array($ret, null);
        }
        $usernames = array_flip($uids);
        $uid = $usernames[$username];
        list ($ret, $fields) = Gallery1DataParser::getUserFieldsByUid($path, $uid);
        if ($ret) {
            return array($ret, null);
        }
        return array(null, $fields);
    }
}


/* Define these classes so that unserialize can use them */
/**
 * A stub class into which various G1 objects can be unserialized.
 */
class G1AlbumDB{ }
/**
 * A stub class into which various G1 objects can be unserialized.
 */
class Album { }
/**
 * A stub class into which various G1 objects can be unserialized.
 */
class Gallery_UserDB { }
/**
 * A stub class into which various G1 objects can be unserialized.
 */
class Gallery_User { }
/**
 * A stub class into which various G1 objects can be unserialized.
 */
class AlbumItem { }
/**
 * A stub class into which various G1 objects can be unserialized.
 */
class G1Img { }
/**
 * A stub class into which various G1 objects can be unserialized.
 */
class Comment { }
