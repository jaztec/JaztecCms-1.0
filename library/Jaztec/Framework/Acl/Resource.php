<?php
require_once 'Zend/Acl/Resource/Interface.php';
/**
 * Represents a resource for the acl
 *
 * @author Jasper van Herpt
 * @version <b>1.0</b><br>
 */
class Jaztec_Framework_Acl_Resource implements Zend_Acl_Resource_Interface
{
    /**
     * @var string
     */
    protected $_resourceId;

    /**
     * @param  string $resourceId
     * @return void
     */
    public function __construct($resourceId)
    {
        $this->_resourceId = (string) $resourceId;
    }

    /**
     *
     * (non-PHPdoc)
     * @see Zend_Acl_Resource_Interface::getResourceId()
     */
    public function getResourceId()
    {
        return $this->_resourceId;
    }

    /**
     * Proxies to getResourceId()
     *
     * (non-PHPdoc)
     * @see Jaztec_Framework_Acl_Resource::getResourceId()
     */
    public function __toString()
    {
        return $this->getResourceId();
    }
}
