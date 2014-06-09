AtomPHP
=======

An open-source mini PHP MVC framework.

How it works
---
When anyone access your WebApp, the entry (index.php) will load the core of AtomPHP and your custom modules first.

And then, the entry will try to run the applaction controller (located in controller). It calls the method in an controller class file appointed by visiter with a string of parameters.

For example, you visit /index.php?app=index&act=index&p=index, the entry will try to run `$index->index('index')`

Start a new application
---
To start your application, make a copy of AtomPHP_core and following this wizard.

The directory will look like:

    app <dir>
      |-config.php
      |-controller <dir>
      |-function.core.inc.php
      |-index.php
      |-service <dir>
      |-static <dir>
      |-view <dir>
      =

Config
---
To config this framework, edit config.php

    <?php
    /**
      * @Category   Category of Your Application
      * @Package    Package Name of Your Application
      * @Framework  AtomPHP V1.3.2 created by 7IN0SAN9 <me@7in0.me>
      * @Author     Your name here <Your-Email-Here>
      * @License    Choose a License
      * @Copyright  Your Name or Organization Name Here
      * @Version    Version of Your Application
      * @Website    Your Website Here
      */
    $option = array(
        /* Basic Config */
          'server'     =>  'localhost', // Server Name ( or Domain ) or IP Address
          'directory'  =>  '/var/www', // Where Your Application Located on
          'appName'    =>  '', // Name of Your Application
          'errInfo'    =>  TRUE, // Show Custom Error Message When Break Down
          'timeZone'   =>  'Asia/Chongqing', // Choose the Time Zone Your Server used. For More Information, visit http://www.php.net/manual/en/timezones.php
        /* Add your configuration here */
          'configA' => 'a', // A Simple Example of Custom Configuration
    );
    
    /* End of file config.php */
    
    ?>

Create a controller
---
First, create a php file named indexController.php in directory 'controller'.

    <?php
    /**
      * @Category   
      * @Package    
      * @Framework  AtomPHP V1.3.2 created by 7IN0SAN9 <me@7in0.me>
      * @Author     
      * @License    
      * @Copyright  
      * @Version    
      * @Website    
      */
    class indexController {
        private $core;
        public function __construct($core) {
            $this->core = $core;
            // Put somethind here if you need...
        }
    
        /* Add your functions here */
        public function index() {
            echo 'Hello World!';
        }
    }
    ?>

It'll print 'Hello World!' when you open your applaction on a broswer.

Use View Controllers
---
First, create a php file named indexView.php in directory 'view'.

    <?php
    /**
      * @Category   
      * @Package    
      * @Framework  AtomPHP V1.3.2 created by 7IN0SAN9 <me@7in0.me>
      * @Author     
      * @License    
      * @Copyright  
      * @Version    
      * @Website    
      */
    class indexView {
        private $core;
        public function __construct($core) {
            $this->core = $core;
            // Put somethind here if you need...
        }
    
        /* Add your functions here */
        public function index() {
            echo 'Hello World!';
        }
    }
    ?>

And use `$this->core->loadView('index')->index();` in your controller file to call method `indexView::index()`.

Use Background Services
---
First, create a php file named demoService.php in directory 'service'.

    <?php
    /**
      * @Category   
      * @Package    
      * @Framework  AtomPHP V1.3.2 created by 7IN0SAN9 <me@7in0.me>
      * @Author     
      * @License    
      * @Copyright  
      * @Version    
      * @Website    
      */
    class demoService {
        private $core;
        public function __construct($core) {
            $this->core = $core;
            // Put somethind here if you need...
        }
    
        /* Add your functions here */
    }
    ?>

And use `$this->core->loadService('index');` in your controller file to create an instance of the service.

Use templates
---
To use the official template engine ,copy all files in AtomPHP_Templates into your appllication directory.

The directory will look like:

    app <dir>
      |-cache <dir>
      |-config.php
      |-controller <dir>
      |-function.core.inc.php
      |-function.templates.inc.php
      |-index.php
      |-service <dir>
      |-static <dir>
      |-templates <dir>
      |-templates_c <dir>
      |-view <dir>
      =

And add these configurations into config.php.

    /* Template Engine Config */
      'templateDir' => 'templates',
      'compileDir' => 'templates_c',
      'cacheDir' => 'cache',
      'cache' => FALSE,

Then, change the method 'index' in indexController.php

    function index() {
        $title = '这是一个测试标题';
        $content = '这是一段测试内容';
        
        $this->core->templates->assign('title', $title);
        $this->core->templates->assign('content', $content);
        
        $this->core->templates->display('demo.tpl');
    }

Now, put the demo.tpl into directory 'templates' and reflush page on broswer.

    <!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
    <html>
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>{$title}</title>
    </head>
    <body>
        <div>
        <h3>{$title} </h3>
        <p>{$content}</p>
        </div>
    </body>
    </html>

Use modules
---
To use modules, create a directory named 'modules' in your application directory and copy module.*.php files into this directory.

Add some configurations into config.php if needed.

Then use the following codes to run a method of modules in your controllers.

    $this->core->callMethod ( 'ModuleName', 'MethodName'[, array (
        'Arg1',
        'Arg2',
         .... ,
        'ArgN' 
    )] );