<?php

namespace Database\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class EmployeeTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
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

    public function update($set, $where) {
        $this->tableGateway->update($set, $where);
    }

    public function insert($set) {
        $this->tableGateway->insert($set);
    }

    public function delete($id) {
        $this->tableGateway->delete(array('id' => $id));
    }

}
