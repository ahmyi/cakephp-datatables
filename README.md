# DataTables plugin for CakePHP

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```BASH
composer require ahmyi/cakephp-datatables
```


## Usage 

### config/bootstrap.php
```PHP

Plugin::load('Datatables');

```

### Controller

```PHP

public function initialize(){
        parent::initialize();

        $this->loadComponent('Datatables.DataTables');
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

