<?php
namespace Ahmyi\DataTables\View\Helper;

use Cake\View\Helper;
use Cake\View\View;
use Cake\Utility\Inflector;
use Cake\View\Helper\SecureFieldTokenTrait;
/**
 * DataTables helper
 */
class DataTablesHelper extends Helper
{
    use SecureFieldTokenTrait;
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
            // 'actions'=>$this->config('actions'),
            'callback'=>'',
            'hide'=>[],
            'fields'=>[],
            'addButton'=>true,
            'unsearchable'=>[],
            'unorderable'=>[],
            'render'=>[]
        ];
        
        if(!isset($options['actions']) || $options['actions'] === true){
            $options['actions'] = $this->config('actions');
        }
        $header=$options['label'];

        $ModelName = str_replace(".", "", $model);

    	$url = $this->getView()->Url->build()."?dt=$model";
        
    	if(!isset($this->config('databases')[$model])){
    		throw new \Exception("Invalid model in datatables. Please set one on controller");
    	}

    	$_fields = $this->config('databases')[$model]->fields;

        
    	$fields = "";
        $columns = "[".PHP_EOL;
        $secure_fields = ['draw'];
        $columnDefs = "[";
    	foreach($_fields as $i => $field){
            if(isset($options['fields'][$field])){
                $ucfield = $options['fields'][$field];
            }else{
                $ucfield = Inflector::humanize($field,['.','_']);
            }

    		$fields.="<th>$ucfield</th>";
            $visible = (!in_array($field, $options['hide']))?'true':'false';
            $searchable = (!in_array($field, $options['unsearchable']))?'true':'false';
            $orderable = (!in_array($field, $options['unorderable']))?'true':'false';
            if(in_array($field,['created','modified'])){
                $columns.="                 {
                    data: '$field',
                    searchable:'$searchable',
                    orderable:'$orderable',
                    render: function(data){
                        return moment.unix(data).format('DD-MM-YYYY : HH:mm:ss');
                    }
                },".PHP_EOL;
            }else{
                $columns.="                {
                    data: '$field',
                    searchable:$searchable,
                    orderable:true,
                    orderable:$orderable,
                    name:'$ucfield',
                    visible:$visible";
                if(isset($options['render'][$field])){
                    $columns.=",
                    render: function(data){
                        ".$options['render'][$field]."
                    }";
                }
                $columns.="
                },".PHP_EOL;
            }    	
    	}

    	$actions = $options['actions'];
        $columnDefs = rtrim($columnDefs,",");
        $columnDefs.="]";
        if($actions) {
            $fields.="<th>Actions</th>";
            $buttons = str_replace("[ID]","'+$('<div/>').text(data.id).html()+'",str_replace(PHP_EOL,"",str_replace("'", "\'",$this->getView()->element($options['actions']))));
            $columns .="                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    className: 'text-center',
                    render: function (data, type, full, meta){ 
                        return '$buttons';
                    }
                },";
        }
        
        $columns = rtrim($columns,",");
        $columns.=PHP_EOL."             ]";
        $script ="
        var dataTable$ModelName = $('#DT$ModelName').DataTable({
            'stateSave': false,
            'columns':$columns,
            'processing': true,
            'serverSide': true,
            'data':{},
            'deferRender': true,
            'ajax':{ 
                url :'$url', 
                type: 'post',
                error: function(){
                    var dt = dataTable$ModelName;
                    var modelName = '$ModelName';
                    ".$options['callback']."
                }
            }
        });";
        
		$this->getView()->Html->scriptBlock($script,['block'=>'script']);
        $addButton = $options['addButton'];
		return $this->getView()->element($this->config('element'),compact('addButton','header','fields','ModelName','model'));
    }
}
