<?php
namespace Ahmyi\DataTables\View\Helper;

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
            "Ahmyi/DataTables./js/jquery.dataTables.min.js",
            "Ahmyi/DataTables./js/dataTables.bootstrap.min.js"
        ],
        'css'=> "Ahmyi/DataTables./css/dataTables.bootstrap.min.css"
    ];

    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->getView()->Html->script($this->config('scripts'),['block'=>'script']);
        $this->getView()->Html->css($this->config('css'),['block'=>'script']);
        
    }

    protected $_scripts = [];

    public function render(string $model,$options = []){
    	$ModelName = str_replace(".", "", $model);
    	$header = str_replace(".", " ", $model);
    	$url = $this->getView()->Url->build("?dt=$model");
    	if(!isset($this->config('databases')[$model])){
    		throw new \Exception("Invalid model in datatables. Please set one on controller");
    	}
    	$_fields = $this->config('databases')[$model]->fields;

    	$fields = "";
        $columns = "[";

    	foreach($_fields as $i => $field){
            $ucfield = ucwords($field);
    		$fields.="<th>$ucfield</th>";
    		$columns.="{'data': '$field'},";
    	}

    	$actions = (isset($options['actions']));



    	$columns = rtrim($columns,",");
    	$columns.="]";

        $callback = $options['callback']??"";

    	$script = "var dataTable$ModelName = $('#DT$ModelName').DataTable(";
        $script .="{'stateSave': true,'columns':$columns,'processing': true,'serverSide': true,'ajax':{ url :'$url', type: 'post',";

        if($actions) {
            $fields.="<th>Actions</th>";
            $lastField = count($_fields)-1;
            $script.='"aoColumnDefs": [{"aTargets": ['.$lastField.'],"mData": "id","mRender": function (data, type, full) { return "<td><button href=\"#\"  + id=\'"+ data + "\'>Edit</button></td>";}}],';
                
        }

    	$script.=" error: function(){
            var dt = dataTable$ModelName;
            var modelName = '$ModelName';
            ".$callback."
        }}});";
        
		$this->getView()->Html->scriptBlock($script,['block'=>'script']);
		return $this->getView()->element($this->config('element'),compact('header','fields','ModelName'));
		

    }
}
