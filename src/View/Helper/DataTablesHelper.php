<?php
namespace Ahmyi\DataTables\View\Helper;

use Cake\View\Helper;
use Cake\View\View;
use Cake\Utility\Inflector;
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
        'css'=> "Ahmyi/DataTables./css/dataTables.bootstrap.min.css",
        'actions'=>'Ahmyi/DataTables.actions'
    ];

    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->getView()->Html->script($this->config('scripts'),['block'=>'script']);
        $this->getView()->Html->css($this->config('css'),['block'=>true]);
        
    }

    protected $_scripts = [];

    public function render(string $model,$options = []){
    	$options+=[
            'label'=>Inflector::humanize(Inflector::underscore($model),['.','_']),
            'actions'=>true,
            'callback'=>''
        ];
        

        $header=$options['label'];

        $ModelName = str_replace(".", "", $model);

    	$url = $this->getView()->Url->build("?dt=$model");

        // list($model, $field) = pluginSplit($model);

    	if(!isset($this->config('databases')[$model])){
    		throw new \Exception("Invalid model in datatables. Please set one on controller");
    	}

    	$_fields = $this->config('databases')[$model]->fields;

    	$fields = "";
        $columns = "[";

    	foreach($_fields as $i => $field){
            $ucfield = Inflector::humanize($field,['.','_']);
    		$fields.="<th>$ucfield</th>";
            if(in_array($field,['created','modified'])){
                $columns.="{'data': '$field','searchable':false,'render': function(data){return moment.unix(data).format('DD-MM-YYYY : HH:mm:ss')}},";
            }else{
                $columns.="{'data': '$field'},";
            }    		
    	}

    	$actions = $options['actions'];
        if($actions === true){

        }

        if($actions) {
            $fields.="<th>Actions</th>";
            $buttons = str_replace("[ID]","'+$('<div/>').text(data.id).html()+'",str_replace(PHP_EOL,"",str_replace("'", "\'",$this->getView()->element($this->config('actions')))));
            $columns .="{data: null,'searchable': false,'orderable': false,className: 'text-center','render': function (data, type, full, meta){ return '$buttons';}},";
            
        }
        $columns = rtrim($columns,",");
        $columns.="]";

        $script = "var dataTable$ModelName = $('#DT$ModelName').DataTable(";
        $script .="{'stateSave': true,'columns':$columns,'processing': true,'serverSide': true,'ajax':{ url :'$url', type: 'post',";

    	$script.=" error: function(){
            var dt = dataTable$ModelName;
            var modelName = '$ModelName';
            ".$options['callback']."
        }}});";
        
		$this->getView()->Html->scriptBlock($script,['block'=>'script']);
		return $this->getView()->element($this->config('element'),compact('header','fields','ModelName','model'));
		

    }
}
