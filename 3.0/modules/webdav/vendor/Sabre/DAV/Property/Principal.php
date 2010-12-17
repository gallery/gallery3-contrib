<?php

/**
 * Principal property
 *
 * The principal property represents a principal from RFC3744 (ACL).
 * The property can be used to specify a principal or pseudo principals. 
 *
 * @package Sabre
 * @subpackage DAV
 * @copyright Copyright (C) 2007-2010 Rooftop Solutions. All rights reserved.
 * @author Evert Pot (http://www.rooftopsolutions.nl/) 
 * @license http://code.google.com/p/sabredav/wiki/License Modified BSD License
 */
class Sabre_DAV_Property_Principal extends Sabre_DAV_Property implements Sabre_DAV_Property_IHref {

    /**
     * To specify a not-logged-in user, use the UNAUTHENTICTED principal
     */
    const UNAUTHENTICATED = 1;

    /**
     * To specify any principal that is logged in, use AUTHENTICATED
     */
    const AUTHENTICATED = 2;

    /**
     * Specific princpals can be specified with the HREF
     */
    const HREF = 3;

    /**
     * Principal-type
     *
     * Must be one of the UNAUTHENTICATED, AUTHENTICATED or HREF constants.
     * 
     * @var int 
     */
    private $type;

    /**
     * Url to principal
     *
     * This value is only used for the HREF principal type.
     * 
     * @var string 
     */
    private $href;

    /**
     * Creates the property.
     *
     * The 'type' argument must be one of the type constants defined in this class.
     *
     * 'href' is only required for the HREF type.
     * 
     * @param int $type 
     * @param string $href 
     * @return void
     */
    public function __construct($type, $href = null) {

        $this->type = $type;

        if ($type===self::HREF && is_null($href)) {
            throw new Sabre_DAV_Exception('The href argument must be specified for the HREF principal type.');
        }
        $this->href = $href;

    }

    /**
     * Returns the principal type 
     * 
     * @return int 
     */
    public function getType() {

        return $this->type;

    }

    /**
     * Returns the principal uri. 
     * 
     * @return string
     */
    public function getHref() {

        return $this->href;

    }

    /**
     * Serializes the property into a DOMElement. 
     * 
     * @param Sabre_DAV_Server $server 
     * @param DOMElement $node 
     * @return void
     */
    public function serialize(Sabre_DAV_Server $server, DOMElement $node) {

        $prefix = $server->xmlNamespaces['DAV:'];
        switch($this->type) {

            case self::UNAUTHENTICATED :
                $node->appendChild(
                    $node->ownerDocument->createElement($prefix . ':unauthenticated')
                );
                break;
            case self::AUTHENTICATED :
                $node->appendChild(
                    $node->ownerDocument->createElement($prefix . ':authenticated')
                );
                break;
            case self::HREF :
                $href = $node->ownerDocument->createElement($prefix . ':href');
                $href->nodeValue = $server->getBaseUri() . $this->href;
                $node->appendChild($href);
                break;

        }

    }

}
