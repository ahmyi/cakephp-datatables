<div class='box box-theme'>
	<div class='box-header'>
		<h3><?=$header;?>
			<?php if($addButton === true)
					printf("<div class='pull-right'><a href='%s' class='btn btn-success'><i class='fa fa-plus'></i> New </a></div>",$this->Url->build(['controller'=>$model,'action'=>'add']));
				else if($addButton !== false) 
					echo $addButton;?>
				
		</h3>
	</div>
	<div class='box-body'>
		<div class='col-sm-12'>
			<table id='DT<?=$ModelName?>'  cellpadding='0' cellspacing='0' border='0' class='display' width='100%''>
				<thead>
					<tr><?=$fields?></tr>
				</thead>
			</table>
		</div>
	</div>
</div>