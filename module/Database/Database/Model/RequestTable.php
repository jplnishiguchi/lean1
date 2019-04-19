<?php

namespace Database\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;

class RequestTable {

	protected $_tableName = "requests";
	protected $_adapter;
	protected $tableGateway;

    public function __construct($adapter) {
        $this->_adapter = $adapter;
        $this->tableGateway = new TableGateway($this->_tableName, $this->_adapter);
    }

	public function fetchAll($params) {

        $resultSet = $this->tableGateway->select(
                function(Select $select) use($params) {
            foreach ($params as $key => $value) {
                if ($params[$key] && (!is_null($value))) {
                    $select->$key($value);
                }
            }
        }
        );
        return $resultSet;
    }

	public function getTransactionCount($params) {
        $resultSet = $this->tableGateway->select(
                        function(Select $select) use ($params) {
                    $select->columns(
                            array(
                                'count' => new Expression('COUNT(*)')
                            )
                    );

                    foreach ($params as $key => $value) {
                        if ($params[$key]) {
                            $select->$key($value);
                        }
                    }
                }
                )->toArray();

        return $resultSet[0]['count'];
    }


	public function insert($insertData) {
        $this->tableGateway->insert($insertData);
    }

    public function update($set, $where) {
        $this->tableGateway->update($set, $where);
    }

    public function fetchWhere($whereCriteria) {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($whereCriteria) {


            if (!empty($whereCriteria)) {
                foreach ($whereCriteria as $cri) {
                    //var_dump($cri);
                    $select->where($cri);
                }
            }
        });
        return $resultSet;
    }



}