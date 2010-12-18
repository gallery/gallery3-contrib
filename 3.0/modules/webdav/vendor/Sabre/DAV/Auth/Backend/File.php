<?php

/**
 * This is an authentication backend that uses a file to manage passwords.
 *
 * The backend file must conform to Apache's htdigest format
 * 
 * @package Sabre
 * @subpackage DAV
 * @copyright Copyright (C) 2007-2010 Rooftop Solutions. All rights reserved.
 * @author Evert Pot (http://www.rooftopsolutions.nl/) 
 * @license http://code.google.com/p/sabredav/wiki/License Modified BSD License
 */
class Sabre_DAV_Auth_Backend_File extends Sabre_DAV_Auth_Backend_AbstractDigest {

    /**
     * List of users 
     * 
     * @var array
     */
    protected $users = array();

    /**
     * Creates the backend object. 
     *
     * If the filename argument is passed in, it will parse out the specified file fist.
     * 
     * @param string $filename 
     * @return void
     */
    public function __construct($filename=null) {

        if (!is_null($filename))
            $this->loadFile($filename);

    }

    /**
     * Loads an htdigest-formatted file. This method can be called multiple times if
     * more than 1 file is used.
     * 
     * @param string $filename 
     * @return void
     */
    public function loadFile($filename) {

        foreach(file($filename,FILE_IGNORE_NEW_LINES) as $line) {

            if (substr_count($line, ":") !== 2) 
                throw new Sabre_DAV_Exception('Malformed htdigest file. Every line should contain 2 colons');
            
            list($username,$realm,$A1) = explode(':',$line);

            if (!preg_match('/^[a-zA-Z0-9]{32}$/', $A1))
                throw new Sabre_DAV_Exception('Malformed htdigest file. Invalid md5 hash');
                
            $this->users[$username] = array(
                'digestHash' => $A1,
                'uri'        => 'principals/' . $username
            );

        }

    }

    /**
     * Returns a users' information
     * 
     * @param string $realm 
     * @param string $username 
     * @return string 
     */
    public function getUserInfo($realm, $username) {

        return isset($this->users[$username])?$this->users[$username]:false;

    }


    /**
     * Returns the full list of users.
     *
     * This method must at least return a uri for each user.
     * 
     * @return array 
     */
    public function getUsers() {

        $re = array();
        foreach($this->users as $userName=>$A1) {

            $re[] = array(
                'uri'=>'principals/' . $userName
            );

        }

        return $re;

    }

}
