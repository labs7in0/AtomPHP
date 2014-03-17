<?php
/**
  * @Category   AtomPHP
  * @Package    me.7in0.atomphp
  * @Author     7IN0SAN9 <me@7in0.me>
  * @License    CC BY-ND 4.0
  * @Version    1.2.1
  * @Website    https://7in0.me/studio/projects/AtomPHP
  */
class databaseModule {
    public $requirement = array (
            'DBType',
            'DBHost',
            'DBFile',
            'DBUser',
            'DBPwd',
            'Database',
            'dbPrefix' 
    );
    private $core, $option, $dbHandle;
    private $errList = array (
            '121' => 'Database: Cannot connect to database',
            '122' => 'Database: Must bind columns',
            '123' => 'Database: Cannot prepare statement for execution',
            '123' => 'Database: Cannot fetch result' 
    );
    public function __construct($core) {
        $this->core = $core;
        $this->core->addErrlist ( $this->errList );
    }
    public function initialize($type, $host, $file, $user, $pwd, $db, $prefix) {
        $this->option = array (
                'DBType' => $type,
                'DBHost' => $host,
                'DBFile' => $file,
                'DBUser' => $user,
                'DBPwd' => $pwd,
                'Database' => $db,
                'dbPrefix' => $prefix 
        );
        
        $this->core->addErrlist ( $this->errList );
        
        $dsn = $this->option ['DBType'] . ':host=' . $this->option ['DBHost'] . ';dbname=' . $this->option ['Database'];
        
        try {
            $this->dbHandle = new PDO ( $dsn, $this->option ['DBUser'], $this->option ['DBPwd'] );
        } catch ( PDOException $e ) {
            $this->core->err ( '121' );
        }
    }
    public function query($sql) {
        $sth = @$this->dbHandle->prepare ( $sql ) or $this->core->err ( '123' );
        @$sth->execute ( $req ) or $this->core->err ( '123' );
        $result = @$sth->fetchAll () or $this->core->err ( '123' );
        return $result;
    }
}
?>
