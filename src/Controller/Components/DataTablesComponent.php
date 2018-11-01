<?php
namespace Datatables\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Event\Event;
use Cake\ORM\Table;
use Cake\ORM\Query;

/**
 * DataTables component
 */
class DataTablesComponent extends Component
{
	    public $components = ['RequestHandler'];


    /**
     * Default configuration.
     *
     * @var array
     */

    protected $_defaultConfig = [
        'element'=>'Datatables.adminlte3'
    ];
    protected $_datatables = [];
    	//
	// $columns[] = [
    //   "data" => "0"
    //   "name" => ""
    //   "searchable" => "true"
    //   "orderable" => "true"
    //   "search" => ["value" => "","regex" => "false"]
    // ];
    // $order[] = ["column" => "0","dir" => "asc"];
	// "draw"=>1,
	// "columns"=>$columns,
	// "order"=>$order,
	// "start"=>"0",
	// "length"=>"10",
	// "search"=>["value" => "","regex" => "false"]
    	
    private function loadModel($model){
    	if(!isset($this->_datatables[$model])){
    		
    	}
    	return $this->_datatables[$model];
    }

    public function process(){
    	if (!$this->request->isPost() || ($model = $this->request->getQuery('dt')) === false || !isset($this->_datatables[$model])){
    		if($this->request->isAjax()){
    			debug($this->_datatables);
    		}
    		return false;
    	}
    	
    	$modelObject = $this->_datatables[$model]->modelObject;
    	$conditions = $this->_datatables[$model]->conditions;
    	$fields = $this->_datatables[$model]->fields;

    	if($conditions){
    		$modelObject = $modelObject->find()->where($conditions);
    	}else{
    		$modelObject = $modelObject->find();
    	}

    	$recordsTotal = $modelObject->count();

    	if($search = $this->request->getData('search.value')){
    		$orWhere = [];
    		$search = strtolower($search);
    		foreach($this->request->getData('columns') as $i=> $columns){
    			if($columns['searchable']){
    				if($search === 'null'){
    					$orWhere[] = $fields[$i]." IS NULL";
    				}else{
	    				$orWhere["LOWER(".$fields[$i].") LIKE "] = "%$search%";
	    			}
    			}
    		}
    		
    		$modelObject->where(['OR'=>$orWhere]);
    	}


    	$recordsFiltered = $modelObject->count();

    	$limit = $this->request->getData('length')??20;
    	$start = $this->request->getData('start');

    	if($start === 0){
    		$page =  1;
    	}else {
    		$page = ceil($start / $limit);//50 / limit = 50
    		if($page <= 0){
    			$page = 1;
    		}
    	}
    	$orders = $this->request->getData('order');
    	// debug($this->request->getData());
    	// die;
    	$modelOrder = [];
    	
    	foreach($orders as $order){
    		$columnId = intval($order['column']);
    		
    		$modelOrderField = $fields[$columnId];
    		$modelOrderDirection = $order['dir'];
    		$modelOrder[$modelOrderField] = $modelOrderDirection;
    	}
    	
    	$modelObject->select($fields)
    		->limit($limit)
    		->order($modelOrder)
    		->page($page);
    	$modelDatas = $modelObject->toArray();
    	$draw = $this->request->getData('draw');
    	$this->getController()->autoRender = false; // avoid to render view
    	$data = [];
    	foreach($modelDatas as $modelData){
    		$_data = [];
    		foreach($fields as $field){
    			$d = $modelData[$field];
    			if($d === null){
    				$d = "NULL";
    			}
    			$_data[$field] = $d;

    		}
    		$_data["DT_RowId"] = "DT".str_replace(".","",$model).'_id_'.$modelData['id'];
    		$data[] = $_data;
    	}
		
		return $this->response->withType("application/json")->withStringBody(json_encode(compact('data','recordsFiltered','recordsTotal','search')));
    }

    public function use(Table $modelTable,$options = []){
    	$fqn = explode("\\",get_class($modelTable));
    	$plugin = $fqn[0];
    	$modelName = "";
    	if($plugin !== "App"){
    		$modelName = $plugin.".";
    	}

    	$modelName.=$modelTable->getRegistryAlias();
    	
    	$this->_datatables[$modelName] = new \stdClass();

    	$this->_datatables[$modelName]->modelObject = $modelTable;

    	if(!isset($options['fields'])){
    		$fields = $modelTable->schema()->columns();
    	}else{
    		$fields = $options['fields'];
    	}
    	
    	$this->_datatables[$modelName]->fields =$fields;
    	$this->_datatables[$modelName]->conditions =$options['conditions']??[];


    }

    public function beforeRender(Event $event){
    	$this->getController()->viewBuilder()->helpers([
            'DataTables.DataTables'=>[
                'databases'=>$this->_datatables,
                'element'=>$this->config('element')
            ]
    	]);
    }


}
