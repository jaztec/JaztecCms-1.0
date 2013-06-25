<?php
require_once 'Jaztec/Framework/Acl/Role.php';
require_once 'Jaztec/Framework/Acl/Resource.php';
require_once 'Jaztec/Framework/Acl/DbTable/Roles.php';
require_once 'Jaztec/Framework/Acl/DbTable/Resources.php';
require_once 'Jaztec/Framework/Acl/DbTable/Privileges.php';
require_once 'Jaztec/Framework/Form/Login.php';
require_once 'Jaztec/Framework/Log.php';
require_once 'Zend/Auth.php';

define('DS', DIRECTORY_SEPARATOR);
/**
 * Loads the ACL resources of the default and requested module into an Acl object,
 * this object will be usable by other parts of the application
 *
 * @author Jasper van Herpt
 * @version <b>1.1</b><br>
 * 1.0 - Setup the plugin<br>
 * 1.1 - Using Database<br>
 */
class Jaztec_Framework_Controller_Plugin_FrameworkAclAuthenticationLoader extends Zend_Controller_Plugin_Abstract
{
    /**
        * Holds the acl object for further processing
        *
        * @var Zend_Acl
        */
    protected $_acl = null;

    /**
        * Holds the auhentication object
        *
        * @var Zend_Auth
        */
    protected $_auth = null;

    /**
        * Holds the error logging object
        *
        * @var Jaztec_Framework_Log
        */
    protected $_logger = null;

    /**
     * @var Binairy flags
     */
    const   ACL_NOTALLOWED          = 0x01;
    const   ACL_NOIDENTITY          = 0x02;
    const   ACL_ALLOWED             = 0x04;
    const   ACL_INVALIDROLE         = 0x08;
    const   ACL_INVALIDRESOURCE     = 0x10;

    /**
        * Sets up a Zend_Acl object
        *
        */
    public function __construct(Zend_Auth $auth)
    {
            // Setup the internal Acl object
            $this->_acl = new Zend_Acl();
            // Setup the internal Auth object
            $this->_auth = $auth;
            // Setup the logging object
            $this->_logger = Jaztec_Framework_Log::getInstance();
            $this->_logger->info(get_class() . ' started');
    }

    /**
        * Setup the internal acl
        *
        * @param Zend_Controller_Request_Abstract $request
        * @return Jaztec_Framework_Controller_Plugin_FrameworkAclAuthenticationLoader
        */
    protected function _configurate(Zend_Controller_Request_Abstract $request)
    {
            // Get the requested module
        $module = $request->getModuleName();
            // Load module specific roles into the internal acl variable if it exists
            $roles = $this->_loadRoles($module);
            if ($roles) {
                    $this->_addRolesToAcl($roles);
            }
            // Load module specific resources into the internal acl variable if it exists
            $resources = $this->_loadResources($module);
            if ($resources) {
                    $this->_addResourcesToAcl($resources);
            }
            // Connect the roles and resources with privileges
            $privileges = $this->_loadPrivileges($module);
            if ($privileges) {
                    $this->_addPrivilegesToAcl($privileges);
            }

            // Finally add all known resources to the ACl
            $this->_completeResourceAcl($request);

            // Set this acl in the registry
            Zend_Registry::set('acl', $this->_acl);

            return $this;
    }

    protected function _completeResourceAcl(Zend_Controller_Request_Abstract $request)
    {
        $module = $request->getModuleName();
        $front = Zend_Controller_Front::getInstance();
        $dirs['default'] = $front->getModuleDirectory('default')
                             . DIRECTORY_SEPARATOR . 'controllers/';
        if ($module !== 'default') {
            $dirs[$module] = $front->getModuleDirectory($module)
                             . DIRECTORY_SEPARATOR . 'controllers/';
        }
        foreach ($dirs as $mod => $path) {
            // A module! lets check and add
            $modStr = 'module:'. $mod;
            if (!$this->_acl->has($modStr)) {
                $this->_acl->addResource($modStr);
                $this->_logger->debug('Loading resource: ' . $modStr);
            }
            $it = new DirectoryIterator($path);
            foreach ($it as $file) {
                if (strstr($file, "Controller.php") !== false) {
                    // We've got an controller, lets check and add
                    $conStr = 'controller:'. $mod .':'. strtolower(substr($file, 0, strpos($file,"Controller.php")));
                    if (!$this->_acl->has($conStr)) {
                        $this->_acl->addResource ($conStr, $modStr);
                        $this->_logger->debug('Loading resource: ' . $conStr);
                    }
                }
            }
        }
    }
    /**
        * Load the roles from the basic application and/or module
        * specific roles.
        *
        * @param	string	$module Load roles from respective modules, defaults to default module
        * @throws 	Jaztec_Plugin_Exception
        * @return 	array|null This function returns an array or null when no file is found
        */
    protected function _loadRoles($module = 'default')
    {
            $dbTable = new Jaztec_Framework_Acl_DbTable_Roles();
            $stmt = $dbTable->select()
                            ->setIntegrityCheck(false)
                            ->from(array('ro' => 'acl_roles'),
                                        array('id','name'))
                            ->joinLeft(array('rp' => 'acl_roles'),
                                        'ro.parent = rp.id',
                                        array('parent'=> 'name'))
                            ->joinLeft(array('mo' => 'cor_modreg'),
                                        'ro.source = mo.id',
                                        '')
                            ->where('mo.name = "default"');
            if($module !== 'default')
                $stmt = $stmt->orWhere('mo.name = "'. $module .'"');
            $stmt = $stmt->order('ro.sort');
            $roles = $dbTable->fetchAll($stmt);

            return $roles->toArray();
    }

    /**
        * Load the resources from the basic application and/or module specific resources, module resources have to be specified inside the modules by a resource xml file
        *
        * @param	string	$module Load resources from respective modules, defaults to default module
        * @return 	array|null This function returns an array or null when no file is found
        */
    protected function _loadResources($module = 'default')
    {
        $dbTable = new Jaztec_Framework_Acl_DbTable_Resources();
        $stmt = $dbTable->select()
                        ->setIntegrityCheck(false)
                        ->from(array('re' => 'acl_resources'),
                                    array('re.id','re.name'))
                        ->joinLeft(array('rp' => 'acl_resources'),
                                    're.parent = rp.id',
                                    array('parent'=> 'rp.name'))
                        ->joinLeft(array('mo' => 'cor_modreg'),
                                    're.source = mo.id',
                                    '')
                        ->where('mo.name = "default"');
        if($module !== 'default')
            $stmt = $stmt->orWhere('mo.name = "'. $module .'"');
        $stmt = $stmt->order('re.sort');
        $resources = $dbTable->fetchAll($stmt);
        $resources = $resources->toArray();

//            var_dump($stmt->assemble());
//            var_dump($resources->toArray());
        return $resources;
    }

    /**
        * Load the privileges from the basic application and/or module specific privileges, module privileges have to be specified inside the modules by a resource xml file
        *
        * @param	string	$module Load privileges from respective modules, defaults to default module
        * @return 	array|null This function returns an array or null when no file is found
        */
    protected function _loadPrivileges($module = 'default')
    {
            $dbTable = new Jaztec_Framework_Acl_DbTable_Privileges();
            $stmt = $dbTable->select()
                    ->setIntegrityCheck(false)
                    ->from(array('pr' => 'acl_privileges'),
                        array(
                            'type',
                            'privilege')
                        )
                    ->joinLeft(
                        array('ro' => 'acl_roles'),
                        'pr.role = ro.id',
                        array('role'=> 'name'))
                    ->joinLeft(
                        array('re' => 'acl_resources'),
                        'pr.resource = re.id',
                        array('resource'=> 'name'))
                    ->joinLeft(array('mo' => 'cor_modreg'),
                        'pr.source = mo.id',
                        '')
                    ->where('mo.name = "default"');
            if($module !== 'default')
                $stmt = $stmt->orWhere('mo.name = "'. $module .'"');
            $privileges = $dbTable->fetchAll($stmt);

            return $privileges->toArray();
    }

    /**
        * Fills the internal acl var with the roles
        *
        * @param 	array $roles
        * @throws 	Jaztec_Plugin_Exception
        * @return 	void
        */
    protected function _addRolesToAcl($roles)
    {
            if (!is_array($roles)) {
                    $this->_logger->crit("Acl roles must be passed as array");
                    require_once 'Jaztec/Plugin/Exception.php';
                    throw new Jaztec_Plugin_Exception('Acl roles must be passed as array');
            }
            try {
                    foreach ($roles as $r) {
                            $role = new Jaztec_Framework_Acl_Role($r['name']);
                            $this->_logger->debug('Loading role: ' . $r['name']);
                            if (!$this->_acl->hasRole($role)) {
                                    if (array_key_exists('parent', $r)) {
                                            $this->_acl->addRole($role, $r['parent']);
                                    } else {
                                            $this->_acl->addRole($role);
                                    }
                            }
                    }
            } catch (Exception $e) {
                    $this->_logger->err($e);
            }
    }

    /**
        * Fills the internal acl var with resources
        *
        * @param 	array $resources
        * @throws 	Jaztec_Plugin_Exception
        * @return 	void
        */
    protected function _addResourcesToAcl($resources)
    {
            if (!is_array($resources)) {
                $this->_logger->crit("Acl resources must be passed as array");
                require_once 'Jaztec/Plugin/Exception.php';
                throw new Jaztec_Plugin_Exception('Acl resources must be passed as array');
            }
            try {
                foreach ($resources as $r) {
                    $resource = new Jaztec_Framework_Acl_Resource($r['name']);
                    $this->_logger->debug('Loading resource: ' . $r['name']);
                    if (!$this->_acl->has($resource)) {
                        if (array_key_exists('parent', $r)) {
                            $this->_acl->addResource($resource, $r['parent']);
                        } else {
                            $this->_acl->addResource($resource);
                        }
                    }
                }
            } catch (Exception $e) {
                    $this->_logger->err($e);
            }
    }

    /**
        * Connects the resources and roles by privileges
        *
        * @param 	array $privileges
        * @throws 	Jaztec_Plugin_Exception
        * @return 	void
        */
protected function _addPrivilegesToAcl($privileges)
{
    if (!is_array($privileges)) {
        $this->_logger->crit("Acl privileges must be passed as array");
        require_once 'Jaztec/Plugin/Exception.php';
        throw new Jaztec_Plugin_Exception('Acl privileges must be passed as array');
    }
    try {
        // Start inserting roles into the Acl object
        foreach ($privileges as $privilege) {
            $role = isset($privilege['role']) ? $privilege['role'] : null;
            $resource = isset($privilege['resource']) ? $privilege['resource'] : null;
            $priv = isset($privilege['privilege']) ? $privilege['privilege'] : null;
            if ($privilege['type'] === 'allow') {
                $this->_acl->allow($role, $resource, $priv);
                $this->_logger->debug("Allowing " . $role . " " . $priv . " on " . $resource);
            } else {
                $this->_acl->deny($role, $resource, $priv);
                $this->_logger->debug("Denying " . $role . " " . $priv . " on " . $resource);
            }
        }
    } catch (Exception $e) {
        $this->_logger->crit($e);
        require_once 'Jaztec/Plugin/Exception.php';
        throw new Jaztec_Plugin_Exception('Got error: ' . get_class($e) . ': ' . $e->getMessage());
    }
}

    /**
        * Checks whether the request can be responded or if the user has to login/get better credentials
        *
        * @param Zend_Controller_Request_Abstract $request
        * @return Uint8 Integer value to determine the outcome of the credentials
        */
    protected function _checkCredentials(Zend_Controller_Request_Abstract $request)
    {
        // Load the request values
        $resource = 'controller:' . $request->getModuleName() . ':' . $request->getControllerName();
        $action = $request->getActionName();
        // Check if there was a form posted, it needs to be processed before the routine should continue
        $this->_validateForm($request);
        // Load identity if set
        $hasIdentity = $this->_auth->hasIdentity();
        // Check if we have an identity else define guest
        if ($hasIdentity) {
            $role = $this->_auth->getIdentity()->role;
        } else {
            $role = 'guest';
        }
        // Set not allowed as default
        $allowed = self::ACL_NOTALLOWED;

        $this->_logger->debug('The role to be tested is: ' . $role . ' performing ' . $action . ' on resource ' . $resource);
        if ($this->_acl->has($resource)) {
            if ($this->_acl->hasRole($role)) {
                try {
                    if (!$this->_acl->isAllowed($role,$resource,$action)) {
                        if (!$hasIdentity) {
                            $allowed = self::ACL_NOIDENTITY;
                            $this->_logger->debug($role . " is anonymous, registration needed");
                        } else {
                            $this->_logger->debug($role . " is not allowed at this resource");
                        }
                    } else {
                        $allowed = self::ACL_ALLOWED;
                        $this->_logger->debug($role . " is allowed at this resource");
                    }
                } catch (Exception $e) {
                    $this->_logger->err($e);
            }
        } else {
            $allowed = self::ACL_INVALIDROLE;
            $this->_logger->notice($role . " is not a valid role");
        }
    } else {
        $allowed = self::ACL_INVALIDRESOURCE;
        $this->_logger->notice($resource . " is not a valid resource");
    }

    return $allowed;
}

    /**
        * Checks to see if there is a post request and acts opun it
        *
        * @param Zend_Controller_Request_Abstract $request
        * @return Jaztec_Framework_Controller_Plugin_FrameworkAclAuthenticationLoader
        */
    protected function _validateForm(Zend_Controller_Request_Abstract $request)
    {
        $this->_logger->debug('Starting form validation');
        // Add the needed Form
        $form = new Jaztec_Framework_Form_Login();
        // Check if the request is POST, otherwise no need for checking
        if ($request->isPost()) {
            // Check if the Form has been filled completely
            if ($form->isValid($_POST)) {
                $this->_logger->debug('Form is marked valid');
                // Setup the adapter and check if its authenticated
                $adapter = $this->_getAuthAdapter();
                $adapter->setIdentity($request->getParam('username'))
                    ->setCredential($request->getParam('password'));
                $result = $this->_auth->authenticate($adapter);
                // Report the loggin
                $this->_logger->notice($request->getParam('username') . " is logged in");
                // Write the user to the storage if authenticated
                if ($result->isValid()) {
                    $storage = $this->_auth->getStorage();
                    $storage->write($adapter->getResultRowObject(
                            array(
                                'id',
                                'username',
                                'email',
                                'role'
                            )
                        )
                    );
                }
            } else {
                $this->_logger->debug('Form is marked invalid');
            }
        }
    }

    /**
        * Return an adapter to check credentials with
        *
        * @return Zend_Auth_Adapter_DbTable
        */
    protected function _getAuthAdapter()
    {
        $adapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());

        $adapter->setTableName('view_users')
            ->setIdentityColumn('username')
            ->setCredentialColumn('password')
            ->setCredentialTreatment('MD5(CONCAT(?,signature)) AND active != 0');

        $this->_logger->debug('Returning auth adapter');

        return $adapter;
    }

    /**
     *
     * (non-PHPdoc)
     * @see Zend_Controller_Plugin_Abstract::preDispatch()
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $this->_logger->debug("Start " . get_class() . "'s preDispatch routine");
        // Make sure the internal acl is loaded
        $this->_configurate($request);
        // Test if this request is legit
        switch ($this->_checkCredentials($request)) {
            case self::ACL_ALLOWED:
                break;
            case self::ACL_NOIDENTITY:
                $request->setModuleName('default')
                    ->setControllerName('access')
                    ->setActionName('login');
                break;
            case self::ACL_INVALIDRESOURCE:
                $request->setModuleName('default')
                    ->setControllerName('error')
                    ->setActionName('notfound');
                break;
            case self::ACL_INVALIDROLE:
            case self::ACL_NOTALLOWED:
            default:
                $request->setModuleName('default')
                    ->setControllerName('error')
                    ->setActionName('notallowed');
                break;
        }
    }
}
