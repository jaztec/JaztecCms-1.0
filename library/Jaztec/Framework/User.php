<?php

/**
 * User fot the Jaztec_Framework
 *
 * @author Jasper van Herpt
 * @version 1.0
 *
 */
class Jaztec_Framework_User
{
    protected $_id;
    protected $_username;
    protected $_password;
    protected $_email;
    protected $_signature;
    protected $_group;
    protected $_role;

    public function __construct($options)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }
    public function __set($name, $value)
    {
        $method = 'set' . $name;
        if ('mapper' == $name || !method_exists($this, $method)) {
            throw new Jaztec_Framework_Exception(__CLASS__ . ': ' . __FUNCTION__ . ' => Invalid property');
        }
        $this->$method($name);
    }
    public function __get($name)
    {
        $method = 'get' . $name;
        if ('mapper' == $name || !method_exists($this, $method)) {
            throw new Jaztec_Framework_Exception(__CLASS__ . ': ' . __FUNCTION__ . ' => Invalid property');
        }

        return $this->$method();
    }
    /**
     * Uses classes set methods to initizialize protected atributes
     *
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }

        return $this;
    }

    /**
     * @return the $_id
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @param field_type $_id
     */
    public function setId($id)
    {
        $this->_id = $id;
    }

    /**
     * @return the $_username
     */
    public function getUsername()
    {
        return $this->_username;
    }

    /**
     * @param field_type $_username
     */
    public function setUsername($username)
    {
        $this->_username = $username;
    }

    /**
     * @return the $_password
     */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     * @param field_type $_password
     */
    public function setPassword($password)
    {
        $this->_password = $password;
    }

    /**
     * @return the $_email
     */
    public function getEmail()
    {
        return $this->_email;
    }

    /**
     * @param field_type $_email
     */
    public function setEmail($email)
    {
        $this->_email = $email;
    }

    /**
     * @return the $_signature
     */
    public function getSignature()
    {
        return $this->_signature;
    }

    /**
     * @param field_type $_signature
     */
    public function setSignature($signature)
    {
        $this->_signature = $signature;
    }

    /**
     * @return the $_group
     */
    public function getGroup()
    {
        return $this->_group;
    }

    /**
     * @param field_type $_group
     */
    public function setGroup($group)
    {
        $this->_group = $group;
    }

    /**
     * @return the $_role
     */
    public function getRole()
    {
        return $this->_role;
    }

    /**
     * @param field_type $_role
     */
    public function setRole($role)
    {
        $this->_role = $role;
    }

}
