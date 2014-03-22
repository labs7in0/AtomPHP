<?php
/**
  * @Category   AtomPHP
  * @Package    me.7in0.atomphp
  * @Author     7IN0SAN9 <me@7in0.me>
  * @License    CC BY 4.0
  * @Version    1.3.1
  * @Website    https://7in0.me/labs/AtomPHP
  */
class templates {
    private $core, $_tpl_var;
    private $errList = array (
            '111' => 'Template: Cannot Complie This Template File',
            '112' => 'Template: Cannot Build Cache File' 
    );
    public function __construct($core, $templateDir, $compileDir, $cacheDir, $cache) {
        $this->core = $core;
        $this->option = array (
                'templateDir' => $templateDir,
                'compileDir' => $compileDir,
                'cacheDir' => $cacheDir,
                'cache' => $cache 
        );
        
        $this->core->addErrlist ( $this->errList );
        
        $this->checkDir ();
    }
    
    // Variable injection
    public function assign($tpl_var, $var = null) {
        $this->_tpl_var [$tpl_var] = $var;
        if (isset ( $this->_tpl_var [$tpl_var] ) && ! empty ( $this->_tpl_var [$tpl_var] ))
            return TRUE;
        else
            return FALSE;
    }
    
    // Complie and display a page
    public function display($file) {
        $tpl_file = $this->option ['templateDir'] . '/' . $file;
        if (! file_exists ( $tpl_file ))
            $this->core->err ( '102', $tpl_file );
        
        $parse_file = $this->option ['compileDir'] . '/' . sha1 ( $file ) . $file . '.php';
        
        if (! file_exists ( $parse_file ) || filemtime ( $parse_file ) < filemtime ( $tpl_file )) {
            $compile = new templatesModuleCompiler ( $this->core, $this->option, $tpl_file );
            $compile->parse ( $parse_file );
        }
        
        if ($this->option ['cache']) {
            $cache_file = $this->option ['cacheDir'] . '/' . sha1 ( $file ) . $file . '.html';
            
            // Create cache file if needed
            if (! file_exists ( $cache_file ) || filemtime ( $cache_file ) < filemtime ( $parse_file )) {
                include $parse_file;
                $content = ob_get_clean ();
                if (! file_put_contents ( $cache_file, $content ))
                    $this->core->err ( '112' );
            }
            
            include $cache_file;
        } else {
            include $parse_file;
        }
    }
    
    // Reflush cache
    public function reflush() {
        $dirHandle = opendir ( $this->option ['cacheDir'] );
        while ( $file = readdir ( $dirHandle ) ) {
            if ($file != '.' && $file != '..') {
                $path = $this->option ['cacheDir'] . '/' . $file;
                if (! is_dir ( $path )) {
                    if (@unlink ( $path ))
                        return FALSE;
                } else {
                    if (@deldir ( $path ))
                        return FALSE;
                }
            }
        }
        
        closedir ( $dirHandle );
    }
    
    // Check if all directory exist
    private function checkDir() {
        if (! is_dir ( $this->option ['templateDir'] ))
            $this->core->err ( '101', $this->option ['templateDir'] );
        
        if (! is_dir ( $this->option ['compileDir'] ))
            $this->core->err ( '101', $this->option ['compileDir'] );
        
        if (! is_dir ( $this->option ['cacheDir'] ))
            $this->core->err ( '101', $this->option ['cacheDir'] );
    }
}

// Compiler
class templatesModuleCompiler {
    private $core, $option, $content;
    public function __construct($core, $option, $tpl_file) {
        $this->core = $core;
        $this->option = $option;
        $this->content = file_get_contents ( $tpl_file );
    }
    
    // Template paeser
    public function parse($parse_file) {
        $this->parseInc ();
        $this->parseVar ();
        
        if (! @file_put_contents ( $parse_file, $this->content ))
            $this->core->err ( '111' );
    }
    
    // Include parser
    private function parseInc() {
        $pattern = '/\{\{([\w\d]+).tpl\}\}/';
        if (preg_match_all ( $pattern, $this->content, $arr )) {
            
            foreach ( $arr [1] as $file ) {
                $parse_file = $this->option ['compileDir'] . '/' . sha1 ( $file ) . $file . '.tpl.php';
                $incParser = new self ( $this->core, $this->option, $this->option ['templateDir'] . '/' . $file . '.tpl' );
                $incParser->parse ( $parse_file );
                
                $this->content = preg_replace ( $pattern, str_replace ( "\xEF\xBB\xBF", '', file_get_contents ( $parse_file ) ), $this->content, 1 );
            }
        }
    }
    
    // Variable parser
    private function parseVar() {
        $pattern = '/\{\$([\w\d]+)\}/';
        if (preg_match ( $pattern, $this->content ))
            $this->content = preg_replace ( $pattern, '<?php echo \$this->_tpl_var["$1"]?>', $this->content );
    }
}
?>
