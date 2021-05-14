<?php

    $DB_NAME='dbnu3test';
    $USR='test';
    $PWD='test';
    final class Database {

        protected static $instance = null;
        public $link;
        
        protected function __construct($server='127.0.0.1', $database, $usrnm, $pswrd) {
            $this->link = mysqli_connect($server, $usrnm, $pswrd, $database);
            mysqli_set_charset($this->link, "utf8");
        }
        public static function getInstance($server='127.0.0.1') {
            global $DB_NAME;
            global $USR;
            global $PWD;
            if(self::$instance==null)
                self::$instance = new self($server, $DB_NAME, $USR, $PWD);
            return self::$instance;
        }
        public function execute($sql)
        {
            return mysqli_query($this->link, $sql);
        }
        public function getLastInsertId()
        {
            return mysqli_insert_id($this->link);
        }
        public function updatedRowsCount()
        {
            $n=mysqli_affected_rows($this->link);
            return $n==-1?false:$n;
        }
        public function getRow($res)
        {
            return mysqli_fetch_assoc($res);
        }
        public function cleanUp($res)
        {
            mysqli_free_result($res);
        }
        public function cleanData(&$str)
        {
            $str=mysqli_real_escape_string($this->link, $str);
        }
        public function getLastError() {
            return mysqli_error($this->link);
        }
        public function getAffectedRows() {
            $rows=$this->execute("SELECT ROW_COUNT() AS CNT;");
            $ret=intval($this->getRow($rows)['CNT']);
            $this->cleanUp($rows);
            return $ret;
        }
    };