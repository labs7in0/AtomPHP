<?php

/**
 * @Category   AtomPHP
 * @Package    me.7in0.atomphp
 * @Author     7IN0SAN9 <me@7in0.me>
 * @License    CC BY 4.0
 * @Version    1.3.2
 * @Website    https://7in0.me/labs/projects/?p=AtomPHP
 */

define('__VERSION__', '1.3.1');

class atomPHP
{
    public $templates;
    private $option, $modules, $variable;
    private $errlist = array(
        '100' => 'AtomPHP: Unknown error',
        '101' => 'AtomPHP: Directory %s not exist',
        '102' => 'AtomPHP: File Loader: File %s not exist',
        '103' => 'AtomPHP: File Loader: Illegal file %s',
        '104' => 'AtomPHP: CallMethod: Requires at least two argument and at most three argument',
        '105' => 'AtomPHP: CallMethod: Method %s not exist',
    );

    // Initialize the framework
    public function __construct($option)
    {
        $this->option = $option;

        date_default_timezone_set($this->option['timeZone']);

        if (is_file('function.templates.inc.php')) {
            require_once 'function.templates.inc.php';
            if (!class_exists('templates')) {
                $this->ccErr('103', 'function.templates.inc.php');
            }

            $this->templates = new templates($this, $this->option['templateDir'], $this->option['compileDir'], $this->option['cacheDir'], $this->option['cache']);
        }

        $this->initModules();
    }

    /**
     *
     * @name addErrlist
     * @param array $errList
     *            An array of your custom error message
     * @return array
     */
    public function regErrlist($errList)
    {
        $this->errlist += $errList;
        return $this->errList;
    }

    /**
     *
     * @name err
     * @param string $err
     *            ID of custom error message
     * @param
     *            optional string $arg1, $arg2, ..., $argN
     *            Parameters of your custom error message
     * @return none
     */
    public function err()
    {
        if (!$this->option['errInfo']) {
            die();
        }

        $numargs = func_num_args();

        if ($numargs == 0 || $this->errlist[func_get_arg(0)] == '') {
            $err = '100';
        } else {
            $err = func_get_arg(0);
        }

        if ($numargs == 1) {
            $info = 'Error #' . $err . ' - ' . $this->errlist[$err];
        } else {
            $info = vsprintf('Error #%s - ' . $this->errlist[$err], func_get_args());
        }

        die($info);
    }

    /**
     *
     * @name callMethod
     * @param string $Module
     *            Module Name
     * @param string $Method
     *            Method Name
     * @param
     *            optional array $args
     *            An array of Arguments for the Method you want to call
     * @return not certain, same as method you called
     */
    public function callMethod()
    {
        $numargs = func_num_args();
        if ($numargs < 2 || $numargs > 3) {
            $this->err('104');
        }

        $module = func_get_arg(0) . 'Module';
        $method = func_get_arg(1);
        if (!method_exists($this->modules[$module], $method)) {
            $this->err('105', $module . '::' . $method);
        } else if ($numargs == 2) {
            $result = $this->modules[$module]->$method;
        } else if ($numargs == 3) {
            if (is_array(func_get_arg(2))) {
                $result = call_user_func_array(array(
                    $this->modules[$module],
                    $method,
                ), func_get_arg(2));
            } else {
                $result = $this->modules[$module]->$method(func_get_arg(2));
            }
        }

        return $result;
    }

    /**
     *
     * @name loadView
     * @param string $viewClass
     * @return object
     */
    public function loadView($viewClass)
    {
        $viewClass .= 'View';
        $path = 'view/' . $viewClass . '.php';
        if (!file_exists($path)) {
            $core->err('102', $path);
        }

        require_once $path;
        if (!class_exists($viewClass)) {
            $this->err('103', $path);
        }

        return new $viewClass($this);
    }

    /**
     *
     * @name loadService
     * @param string $serviceClass
     * @return object
     */
    public function loadService($serviceClass)
    {
        $serviceClass .= 'Service';
        $path = 'service/' . $serviceClass . '.php';
        if (!file_exists($path)) {
            $core->err('102', $path);
        }

        require_once $path;
        if (!class_exists($serviceClass)) {
            $this->err('103', $path);
        }

        return new $serviceClass($this);
    }

    /**
     *
     * @name addVariable
     * @param mixed $para
     * @return none
     */
    public function addVariable($para, $method = 'GET')
    {
        if ($method == 'POST') {
            $this->variable['POST'] = $para;
        } else {
            $object = json_decode($para);
            if (!is_object($object)) {
                $object = $para;
            }

            $this->variable['GET'] = $object;
        }
    }

    /**
     *
     * @name getInput
     * @param string $key
     * @return string
     */
    public function getInput($key, $method = 'GET')
    {
        if (is_object($this->variable[$method])) {
            return $this->variable[$method]->$key;
        } else {
            return $this->variable[$method];
        }

    }

    // Initialize modules.
    private function initModules()
    {
        $dir = 'modules';
        if (is_dir($dir)) {
            if ($dirHandle = opendir($dir)) {
                while ($file = readdir($dirHandle)) {
                    if (pathinfo($file, PATHINFO_EXTENSION) != 'php') {
                        continue;
                    }

                    $tmp = explode('.', $file);
                    if ($tmp[0] != 'module') {
                        continue;
                    }

                    $path = 'modules/' . $file;
                    require_once $path;
                    $module = $tmp[1] . 'Module';
                    if (!class_exists($module)) {
                        $this->err('103', $path);
                    }

                    $this->modules[$module] = new $module($this);
                    $requirement = $this->modules[$module]->requirement;
                    if (is_array($requirement)) {
                        $args = array();
                        if (is_array($requirement)) {
                            foreach ($requirement as $requ) {
                                array_push($args, $this->option[$requ]);
                            }
                        }

                        $this->callMethod($tmp[1], 'initialize', $args);
                    }
                }
                closedir($dirHandle);
            }
        }
    }
}
