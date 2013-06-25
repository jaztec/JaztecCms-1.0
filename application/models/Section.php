<?php

class Application_Model_Section
{
    protected $_id;
    protected $_name;
    protected $_description;
    protected $_parentId;
    protected $_alias;
    protected $_container;
    protected $_isPage;

    public function __construct($options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }
    public function __set($name, $value)
    {
        $method = 'set' . $name;
        if ('mapper' == $name || !method_exists($this, $method)) {
            throw new Exception('Invalid property');
        }
        $this->$method($name);
    }
    public function __get($name)
    {
        $method = 'get' . $name;
        if ('mapper' == $name || !method_exists($this, $method)) {
            throw new Exception('Invalid property');
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
     *
     * Enter description here ...
     * @param  string                    $id
     * @return Application_Model_Section
     */
    public function setId($id)
    {
        $this->_id = (int) $id;

        return $this;
    }
    /**
     *
     * @return int $id
     */
    public function getId()
    {
        return $this->_id;
    }
    /**
     *
     * Enter description here ...
     * @param  string                    $name
     * @return Application_Model_Section
     */
    public function setName($name)
    {
        $this->_name = (string) $name;

        return $this;
    }
    /**
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }
    /**
     *
     * Enter description here ...
     * @param  string                    $description
     * @return Application_Model_Section
     */
    public function setDescription($description)
    {
        $this->_description = (string) $description;

        return $this;
    }
    /**
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }
    /**
     *
     * Enter description here ...
     * @param  string                    $parentId
     * @return Application_Model_Section
     */
    public function setParentId($parentId)
    {
        $this->_parentId = (int) $parentId;

        return $this;
    }
    /**
     *
     * @return int
     */
    public function getParentId()
    {
        return $this->_parentId;
    }
    /**
     *
     * Enter description here ...
     * @param  string                    $alias
     * @return Application_Model_Section
     */
    public function setAlias($alias)
    {
        $this->_alias = (string) $alias;

        return $this;
    }
    /**
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->_alias;
    }
    /**
     *
     * Enter description here ...
     * @param  int                       $container
     * @return Application_Model_Section
     */
    public function setContainer($container)
    {
        $this->_container = $container;

        return $this;
    }
    /**
     *
     * @return int
     */
    public function getContainer()
    {
        return $this->_container;
    }
    /**
     *
     * Enter description here ...
     * @param  bool                      $isPage
     * @return Application_Model_Section
     */
    public function setIsPage($isPage)
    {
        $this->_isPage = (bool) $isPage;

        return $this;
    }
    /**
     *
     * @return bool
     */
    public function getIsPage()
    {
        return $this->_isPage;
    }
}
