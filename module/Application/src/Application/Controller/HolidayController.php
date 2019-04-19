<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Http\PhpEnvironment\Request;
use Zend\Validator\Date;
use Utilities\Holiday;
use Utilities\Playdough\PdValidator;
use Utilities\CbValidator;
use Utilities\Options;
use Utilities\Empgroup;
use Utilities\EmployeeJap;
use Utilities\Playdough\Loggedusers;
use Database\Model\LogsObject;
use Database\Model\HolidayObject;

class HolidayController extends AbstractActionController {

    protected $_holidayClass = NULL;

    public function indexAction() {

    }

	public function getallAction(){

		$config = $this->getServiceLocator()->get('Config');
         $this->_holidayClass = new Holiday($config);

        $params = $this->params()->fromQuery();

		$page = isset($params['pg']) ? $params['pg'] : 1;
		$orderBy = isset($params['sortcol']) && !empty($params['sortcol']) ? $params['sortcol'] : "id";
		$sort = isset($params['sortval']) && !empty($params['sortval']) ? $params['sortval'] : "DESC";
		$dateFrom = isset($params['date-from']) && !empty($params['date-from']) ? $params['date-from'] : "";
		$dateTo = isset($params['date-to']) && !empty($params['date-to']) ? $params['date-to'] : "";
        $columns = array(
			  "id",
                "date",
                "holiday",
                "proclamation",
                "rate"
        );


		$search = isset($params['search']) && !empty($params['search']) ? $params['search'] : "";
        $resultList = $this->_holidayClass->getList($search, $dateFrom, $dateTo, $columns, $orderBy, $sort,  $page);

        echo json_encode($resultList);

        die;
	}

    /**
    * @todo: Validation
    **/

    public function bulkaddAction() {
        $request = $this->getRequest();
        $saveMessage = array();
        if ($request->isPost()) {
            // get file
            $file = $request->getFiles()->toArray();

            $config = $this->getServiceLocator()->get('Config');
            $holidayclass = new Holiday($config);
            $return = $holidayclass->bulkadd($file);

            if ($return['status'] == FALSE) {
                return array('data' => $return['data']);
            } else {
                $this->_holidayClass = new Holiday($config);

                $saveMessages = array(
                    'success' => array(),
                    'failed' => array(),
                );

                $date = new Date();

                $count = 1;
                foreach ($return['data'] as $data) {
                    if (!$this->_holidayClass->checkAllElements($data)) {

                        if(!in_array($data['proclamation'],$this->_holidayClass->holiday_proclamation_array)){
                            $saveMessages['failed'][] = "Entry #".$count.": Invalid proclamation value for " . $data['date'] . " (" . implode(",", $data) . "). ";
                            $count++;
                            continue;
                        }
                        
                        if(!$date->isValid($data['date'])){
                            $saveMessages['failed'][] = "Entry #".$count.": Invalid date format value (" . implode(",", $data) . "). ";
                            $count++;
                            continue;
                        }

                        try {

                            $holidayList = $this->_holidayClass->table->insert(array(
                                'date' => $data['date'],
                                'holiday' => $data['holiday'],
                                'rate' => $data['rate'],
                                'proclamation' => $data['proclamation'],
                            ));

                            $saveMessages['success'][] = "Entry #".$count.": Holiday " . $data['holiday']. " successfully created.";
                            $count++;
                            // $view = new ViewModel(array(
                            //    'data' => array(
                            //    'msg' => "Account for " . $data['date'] . " successfully created.",
                            //    'log_type' => LogsObject::LOG_TYPE_HOLIDAY_BULKADD
                            //    )
                            //));
                            //$saveMessages[] = $view;

                        } catch (\Exception $e) {
                            $saveMessages['failed'][] = "Entry #".$count.": Data error for " . $data['date'] . " (" . implode(",", $data) . "). " . $e->getMessage();
                            $count++;
                        }

                    } else {
                        $saveMessages['failed'][] = "Entry #".$count.": Data incomplete for " . $data['date'] . " (" . implode(",", $data) . ").";
                        $count++;
                    }
                }

                return array('data' => array('saveMessages' => $saveMessages, 'log_type' => LogsObject::LOG_TYPE_HOLIDAY_BULKADD));

            }

        }
    }



    public function addAction() {

        $request = new Request();
        $posts = $request->getPost()->toArray();
        if ($posts == null) {

        } else {
            $config = $this->getServiceLocator()->get('Config');
            $holidayclass = new Holiday($config);
           /* $result = $pdValidator->validate($posts);
            if(is_array($result)){
                $view = new ViewModel(array(
                    'data' => array(
                        'msg' => $result['msg']
                    )
                    ));
                $view->setTemplate('application/holiday/notice.phtml'); // path to phtml file under view folder
                return $view;
            }
            */

             $config = $this->getServiceLocator()->get('Config');
            $holidayclass = new Holiday($config);
            $result = $holidayclass->createHoliday(array(       "date" => $posts['date'],
                "holiday" => $posts['holiday'],
                "proclamation" => $posts['proclamation'],
                "rate" => $posts['rate']
            ));

            $view = new ViewModel(array(
                'data' => array(
                    'date' => $posts['date'], 'msg' => 'Holiday <b>' . $posts['holiday'] . '</b> was successfully created.'
                )
                ));
            $view->setTemplate('application/holiday/notice.phtml'); // path to phtml file under view folder
            return $view;
        }
    }

    public function updateAction(){

        $request = new Request();
         $config = $this->getServiceLocator()->get('Config');
        $holidayclass = new Holiday($config);
        $posts = $request->getPost()->toArray();
        $pdValidator = new PdValidator();
      /*  $result = $pdValidator->validate($posts);
        if (is_array($result)) {
            $view = new ViewModel(
                    array(
                'data' => array('msg' => $result['msg'])
                    )
            );
            $view->setTemplate('application/holiday/notice.phtml'); // path to phtml file under view folder
            return $view;
        }*/

        $postParam = $request->getPost('id');
        $getParam = $request->getQuery('id');
          if (empty($postParam) && empty($getParam)) {

            $this->redirect()->toRoute('holiday');

        }
           if ($posts == null) {

            $data = $holidayclass->getHoliday($getParam);
                      return (array(
                'data' => $data,
            ));
           }   else{
            $result = $holidayclass->updateHoliday($posts);
            $view = new ViewModel(
                array(
                'data' => array(
                    'id' => $posts['id'],
                    'msg' => 'Holiday ID <b>'. $posts['id'] . '</b> was successfully updated.')
            )
        );
             $view->setTemplate('application/holiday/notice.phtml'); // path to phtml file under view folder
            return $view;
    }
            return $result;
        
}

	protected function _getConfig(){
        $config = $this->getServiceLocator()->get('Config');
        return $config['request'];
    }


}














