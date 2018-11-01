<?php
namespace DataTables\View\Helper;

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
    protected $_defaultConfig = [];
    protected $_scripts = [];

    public function render(string $model,$options = []){
    	$ModelName = str_replace(".", "", $model);
    	$header = str_replace(".", " ", $model);
    	$url = $this->getView()->Url->build('/pages/index?dt=Pages');
    	if(!isset($this->_config['databases'][$model])){
    		throw new \Exception("Invalid model in datatables. Please set one on controller");
    	}
    	$fields = $this->_config['databases'][$model]->fields;
    	$thFields = "";
    	$columns = "[";
    	foreach($fields as $i => $field){
    		$thFields.="<th>$field</th>";
    		$columns.="{'data': '$field'},";
    	}
    	$actions = (isset($options['actions']));
    	if($actions) $thFields.="<th>Actions</th>";
    	$columns = rtrim($columns,",");
    	$columns.="]";
    	$script = "<script>var dataTable$ModelName = $('#DT$ModelName').DataTable( {'stateSave': true,'columns':$columns,'processing': true,'serverSide': true,'ajax':{ url :'$url', type: 'post',";

    	$script.=" error: function(){}}});</script>";
		$this->getView()->append('script',$script);
		return "<div class='box box-theme'><div class='box-header'><h3>$header</h3></div><div class='box-body'><div class='col-sm-12'><table id='DT$ModelName'  cellpadding='0' cellspacing='0' border='0' class='display' width='100%''><thead><tr>$thFields</tr></thead></table></div></div></div>";
		

    }
}
