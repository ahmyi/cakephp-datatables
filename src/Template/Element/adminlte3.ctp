<div class='box box-theme'>
	<div class='box-header'>
		<h3><?=$header;?>
			<div class="pull-right"><a href='<?=$this->Url->build(['action'=>'add']);?>/>' class='btn btn-success'><i class="fa fa-plus"></i> New</a></div>
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