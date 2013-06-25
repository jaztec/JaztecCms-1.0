<?php
require_once 'Zend/Db/Table/Abstract.php';
/**
 * Table with anchor to all modules registered.
 *
 * @author Jasper van Herpt
 * @version <b>1.0</b><br>
 */
class Jaztec_Framework_Core_DbTable_ModReg extends Zend_Db_Table_Abstract
{
    protected $_name = 'cor_modreg';
        protected $_primairy = 'id';
}
