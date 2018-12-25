# DataTables plugin for CakePHP

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```BASH
composer require ahmyi/cakephp-datatables:dev-master
```


## Usage 
### src/Application.php
```PHP

$this->addPlugin('Ahmyi/DataTables');

```
### config/bootstrap.php [prior 3.6]
```PHP

Plugin::load('Ahmyi/DataTables');

```

### Controller

```PHP

public function initialize(){
        parent::initialize();

        $this->loadComponent('Ahmyi/DataTables.DataTables');
}

public function index(){
    

   $this->DataTables->use("Pages");  // $this->DataTables->use($this->Pages);
   
   if($datatables = $this->DataTables->process()){
        return $datatables;
   }
}

```

### Template


```PHP

<div class="row">
	<div class="col-sm-12">
		<?= $this->DataTables->render("Pages");?>
	</div>
</div>

```

## Customizing

### Templating

If you want to customize the template from current at your controller define new element

```PHP

public function initialize(){
        parent::initialize();

        $this->loadComponent('Ahmyi/DataTables.DataTables',[
		'element'=>'your_element'
	]);
}
```

Your element should have 2 major variables $ModelName and $fields here is an example
```PHP

</div><div class='box box-theme'>
	<div class='box-header'>
		<h3><?=$header;?></h3>
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

```

### Use different js or css assets

You can use differnt Css or Js as example below where it can be in string or array for multiple assets
```PHP

public function initialize(){
        parent::initialize();

        $this->loadComponent('Ahmyi/DataTables.DataTables',[
		'css'=>'your.css'
		'js'=>'your.js'
	]);
}
```
