<?php

namespace Utilities;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Database\Model\HolidayObject;
use Database\Model\HolidayTable;
use Zend\Db\Sql\Where;
use Utilities\Playdough\Pwd;

class Holiday {

    protected $_config;
    protected $_dbConfig;
    protected $_adapter;
    protected $_identityObject;
    protected $_gateway;
    public $table;
    public $holiday_proclamation_array = array(
        "Regular",
        "Special Non-Working"
    );

    public function __construct($config) {
        $this->_config = $config['holiday'];
        $this->_dbConfig = $config['db'];
        $this->_adapter = new Adapter($this->_dbConfig);
        $this->_identityObject = new HolidayObject();
        $this->_gateway = new TableGateway($this->_identityObject->getTablename(), $this->_adapter);
        $this->table = new HolidayTable($this->_gateway);
    }

    public function createHoliday($posts)
    {
        $holiday = $this->table->insert($posts);

    }

	public function getList($search, $dateFrom, $dateTo, $columns, $orderBy, $sort,  $page){
		$limit = $this->_config['limit_per_page'];
        $offset = ($page - 1) * $limit;

		$where = new Where();
            if (!empty($search)) {
				$where->like("holiday", '%'.$search.'%')->or->equalTo('id', $search)->or->equalTo('proclamation', $search)->or->equalTo('rate', $search);
            }if(!empty($dateFrom)||!empty($dateTo)){
			$where->literal("date >= '".$dateFrom."' AND date <= '".$dateTo."'");
		}

		$params = array(
                'limit' => $limit,
                'offset' => $offset,
                'columns' => $columns,
                //  'order' => 'orderDate ' . $sort,
                'order' => $orderBy . ' ' . $sort,
                'where' => $where
            );

		$records = $this->table->fetchAll($params)->toArray();

		// query the count of all matching records. unset limiters first
            unset($params['columns']);
            unset($params['limit']);
            unset($params['offset']);
            unset($params['order']);
            $count = $this->table->getTransactionCount($params);

		   $pageCount = intval($count / $limit);
            if ($count % $limit > 0)
                $pageCount++;

		  return array(
                'records' => $records,
                'count' => $count,
                'pageCount' => $pageCount,
                'currPage' => $page,
                'limit' => $limit,
            );
	}

  public function updateHoliday($posts) {
        $holidaylist = $this->table->update(array(
            'date' => $posts['date_view'],
            'holiday' => $posts['holiday_view'],
            'proclamation' => $posts['proclamation_view'],
            'rate' => $posts['rate_view']), array('id' => $posts['id']));
    }
    public function getHoliday($id) {
        $holiday = $this->table->fetchWhere([['id'=>$id]]);
        return $holiday->current();
    }

    public function bulkadd($file) {

        $mimes = array('application/vnd.ms-excel', 'text/plain', 'text/csv', 'text/tsv');
        if (empty($file) || !in_array($file['file']['type'], $mimes)) {
            $saveMessage[] = 'ERROR Wrong File Format.';
            return array('data' => array('saveMessages' => $saveMessage, 'file' =>$file ),'status' => FALSE);
        }

        $fileLocation = $file['file']['tmp_name'];

        $row = 1;

        $rowName = array();
        $bulkData = array();

        // try to open file on the tmp location
        if (($handle = fopen($fileLocation, "r")) !== FALSE) {
            // loop CSV
            $rowCount = 0;
            $userData = array();
            $rowName = array();
            $allData = array();
            while (($csvRow = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $rowCount++;
                // fetch first row to get headers
                if ($row == 1) {
                    //$rowName = $csvRow;
                    foreach ($csvRow as $val) {
                        $string = str_replace(' ', '-', $val);
                        $rowName[] = preg_replace('/[^A-Za-z0-9_\-]/', '', $string);
                    }
                    $row++;
                    if (!$this->_checkRequiredCols($rowName)) {
                        return array('status' => FALSE, 'data' => array(
                            'saveMessages' => array(
                                'Columns does not match, must have date, holiday, proclamation, and rate headers.',
                                'Received header are: '.implode(",", $rowName))
                            ));
                    };
                    continue;
                }

                // associate header to its data
                for ($csvColumn = 0; $csvColumn < count($rowName); $csvColumn++) {

                    $userData[$rowName[$csvColumn]] = $csvRow[$csvColumn];
                }

                $allData[] = $userData;
            }
            fclose($handle);
            return array('status' => TRUE, 'data' => $allData);
        }
    }

    protected function _checkRequiredCols($cols) {
        $required = array('date', 'proclamation', 'rate','holiday');
        sort($required);
        sort($cols);

        if ($required !== $cols) {
            return false;
        }

        return true;
    }

    public function checkAllElements($row){
        return array_filter(
            $row,
            function($v, $k){
                // return if $k have value and $k do not have value
                return $k && !$v;
            },
            ARRAY_FILTER_USE_BOTH
        );

    }

}
