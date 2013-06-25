<?php
require_once 'Zend/Acl/Role/Interface.php';
/**
 * Represents a role for the acl
 *
 * @author Jasper van Herpt
 * @version <b>1.0</b><br>
 */
class Jaztec_Framework_Acl_Role implements Zend_Acl_Role_Interface
{
    /**
     * @var string
     */
    protected $_roleId;

    /**
     * @param  string $roleId
     * @return void
     */
    public function __construct($roleId)
    {
        $this->_roleId = (string) $roleId;
    }

    /**
     *
     * (non-PHPdoc)
     * @see Zend_Acl_Role_Interface::getRoleId()
     */
    public function getRoleId()
    {
        return $this->_roleId;
    }

    /**
     * Proxies to getRoleId()
     *
     * (non-PHPdoc)
     * @see Jaztec_Framework_Acl_Role::getRoleId()
     */
    public function __toString()
    {
        return $this->getRoleId();
    }
}
