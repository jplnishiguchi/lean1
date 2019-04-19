<?php

namespace Utilities\Playdough;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Where;
use Database\Model\BillingKeywordsObject;
use Database\Model\BillingKeywordsTable;

class BillingKeywords {

    protected $_dbConfig;    
    protected $_adapter;
    protected $_object;
    protected $_gateway;
    protected $_table;        

    function __construct($config) {
        $this->_dbConfig = isset($config['db']) ? $config['db'] : $config;

        $this->_adapter = new Adapter($this->_dbConfig);
        $this->_object = new BillingKeywordsObject();
        $this->_gateway = new TableGateway($this->_object->getTablename(), $this->_adapter);
        $this->_table = new BillingKeywordsTable($this->_gateway);
    }    
    
    public function getByRetailRate($retailRate, $type){
        return $this->_table->getByRetailRate($retailRate, $type);
    }
}
