<?php

class Application_Model_Article
{
    protected $_id;
    protected $_title;
    protected $_description;
    protected $_urlRewrite;
    protected $_section;
    protected $_content;
    protected $_userAdded;
    protected $_dateTimeAdded;
    protected $_dateTimeLastModified;

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
     * Set id
     *
     * @param  int           $id
     * @return Model_Article
     */
    public function setId($id)
    {
        $this->_id = (int) $id;

        return $this;
    }
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->_id;
    }
    /**
     * Set title
     *
     * @param  string        $title
     * @return Model_Article
     */
    public function setTitle($title)
    {
        $this->_title = (string) $title;

        return $this;
    }
    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }
    /**
     * Set description
     *
     * @param  string        $description
     * @return Model_Article
     */
    public function setDescription($description)
    {
        $this->_description = (string) $description;

        return $this;
    }
    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }
    /**
     * Set urlRewrite
     *
     * @param  string        $urlRewrite
     * @return Model_Article
     */
    public function setUrlRewrite($urlRewrite)
    {
        $this->_urlRewrite = (string) $urlRewrite;

        return $this;
    }
    /**
     * Get urlRewrite
     *
     * @return string
     */
    public function getUrlRewrite()
    {
        return $this->_urlRewrite;
    }
    /**
     * Set section
     *
     * @param  int           $section
     * @return Model_Article
     */
    public function setSection($section)
    {
        $this->_section = (string) $section;

        return $this;
    }
    /**
     * Get section
     *
     * @return int
     */
    public function getSection()
    {
        return $this->_section;
    }
    /**
     * Set content
     *
     * @param  string        $content
     * @return Model_Article
     */
    public function setContent($content)
    {
        $this->_content = (string) $content;

        return $this;
    }
    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->_content;
    }
    /**
     * Set user
     *
     * @param  string        $user
     * @return Model_Article
     */
    public function setUser($user)
    {
        $this->_userAdded = (string) $user;

        return $this;
    }
    /**
     * Get user
     *
     * @return string
     */
    public function getUser()
    {
        return $this->_userAdded;
    }
    /**
     * Set date added
     *
     * @param  DateTime      $dateAdded
     * @return Model_Article
     */
    public function setDateTimeAdded($dateAdded)
    {
        $this->_dateTimeAdded = $dateAdded;

        return $this;
    }
    /**
     * Get date added
     *
     * @return DateTime
     */
    public function getDateTimeAdded()
    {
        return $this->_dateTimeAdded;
    }
    /**
     * Set date last modified
     *
     * @param  DateTime      $dateTimeLastModified
     * @return Model_Article
     */
    public function setDateTimeLastModified($dateTimeLastModified)
    {
        $this->_dateTimeLastModified = $dateTimeLastModified;

        return $this;
    }
    /**
     * Get date last modified
     *
     * @return DateTime
     */
    public function getDateTimeLastModified()
    {
        return $this->_dateTimeLastModified;
    }

}
