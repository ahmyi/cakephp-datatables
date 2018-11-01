<?php
namespace Datatables\View\Helper;

use Cake\View\Helper;
use Cake\View\View;

/**
 * DataTables helper
 */
class DataTablesHelper extends Helper
{

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'element'=>'Datatable.adminlte3',
        'scripts'=>[
            "Datatables./js/jquery.dataTables.min.js",
            "Datatables./js/dataTables.bootstrap.min.js"
        ],
        'css'=> "Datatables./css/dataTables.bootstrap.min.css"
    ];

    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->getView()->Html->script($this->config('scripts'),['block'=>'script']);
        
    }

    protected $_scripts = [];

    public function render(string $model,$options = []){
    	$ModelName = str_replace(".", "", $model);
    	$header = str_replace(".", " ", $model);
    	$url = $this->getView()->Url->build('/pages/index?dt='.$model);
    	if(!isset($this->config('databases')[$model])){
    		throw new \Exception("Invalid model in datatables. Please set one on controller");
    	}
    	$_fields = $this->config('databases')[$model]->fields;

    	$fields = "";
        $columns = "[";

    	foreach($_fields as $i => $field){
    		$fields.="<th>$field</th>";
    		$columns.="{'data': '$field'},";
    	}

    	$actions = (isset($options['actions']));
    	if($actions) $thFields.="<th>Actions</th>";

    	$columns = rtrim($columns,",");
    	$columns.="]";

        $callback = $options['callback']??"";

    	$script = "var dataTable$ModelName = $('#DT$ModelName').DataTable(";
        $script .="{'stateSave': true,'columns':$columns,'processing': true,'serverSide': true,'ajax':{ url :'$url', type: 'post',";
    	$script.=" error: function(){
            var dt = dataTable$ModelName;
            var modelName = '$ModelName';
            ".$callback."
        }}});";
        
		$this->getView()->Html->scriptBlock($script,['block'=>'script']);
		return $this->getView()->element($this->config('element'),compact('header','fields','ModelName'));
		

    }
}
