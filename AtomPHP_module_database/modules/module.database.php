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
            'dbType',
            'dbHost',
            'dbFile',
            'dbUser',
            'dbPwd',
            'database',
            'dbPrefix' 
    );
    private $core, $option, $dbHandle;
    private $errList = array (
            '121' => 'database: Cannot connect to database',
            '122' => 'database: Must bind columns',
            '123' => 'database: Cannot prepare statement for execution',
            '124' => 'database: Cannot fetch result',
            '125' => 'database: Database return error # %s' 
    );
    public function __construct($core) {
        $this->core = $core;
        $this->core->addErrlist ( $this->errList );
    }
    public function initialize($type, $host, $file, $user, $pwd, $db, $prefix) {
        $this->option = array (
                'dbType' => $type,
                'dbHost' => $host,
                'dbFile' => $file,
                'dbUser' => $user,
                'dbPwd' => $pwd,
                'database' => $db,
                'dbPrefix' => $prefix 
        );
        
        $this->core->addErrlist ( $this->errList );
        
        $dsn = $this->option ['dbType'] . ':host=' . $this->option ['dbHost'] . ';dbname=' . $this->option ['database'] . ';charset=utf8';
        
        try {
            $this->dbHandle = new PDO ( $dsn, $this->option ['dbUser'], $this->option ['dbPwd'] );
            $this->dbHandle->setAttribute ( PDO::ATTR_EMULATE_PREPARES, false );
        } catch ( PDOException $e ) {
            $this->core->err ( '121' );
        }
    }
    public function query($sql, $bind) {
        $sth = @$this->dbHandle->prepare ( $sql ) or $this->core->err ( '123' );
        @$sth->execute ( $bind ) or $this->core->err ( '124' );
        if ($sth->errorCode == 0) {
            $result = @$sth->fetchAll ( PDO::FETCH_ASSOC );
            if (empty ( $result ) || empty ( $result [0] ))
                return NULL;
            else
                return $result;
        } else {
            $this->core->err ( '125', $sth->errorCode );
        }
    }
    public function exec($sql, $bind) {
        $sth = @$this->dbHandle->prepare ( $sql ) or $this->core->err ( '123' );
        @$sth->execute ( $bind ) or $this->core->err ( '124' );
        if ($sth->errorCode == 0)
            return $sth->rowCount ();
        else
            $this->core->err ( '125', $sth->errorCode );
    }
}
?>
