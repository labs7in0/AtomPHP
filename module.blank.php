<?php
/**
* @Category   Category of Your Module
* @Package    Package Name of Your Module
* @Author     Your Name Here <Your-Email-Here>
* @License    Choose a License
* @Version    Version of Your Module
* @Website    Your Website Here
*/

class blankModule {
    public $requirement = array( 'module_blank_a', 'module_blank_b', ..., 'module_blank_n' );
    private $core, $option;
    private $errList = array (
        // '200' => 'AtomPHP: Example',
        // Do not use array index 100 ~ 199, that's reserved for official modules
        /* Start editing here */
        '999' => 'Some Message',
    /* Stop editing here */
    );

    public function __construct( $core ) {
        $this->core = $core;
        $this->core->AddErrlist ( $this->errList );
        // Put somethind here if you need...
    }

    public function initialize( $module_blank_a, $module_blank_b, ..., $module_blank_n ) {
        $this->option = array (
            'module_blank_a' => $module_blank_a,
            'module_blank_b' => $module_blank_b,
            'module_blank_c' => $module_blank_c,
            ... ,
            'module_blank_n' => $module_blank_d 
        );
    }

    /* Add your functions here */

}
?>