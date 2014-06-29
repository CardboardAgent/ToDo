<?php

/**
 * check: http://ch1.php.net/manual/en/class.pdo.php
 * also : http://code.tutsplus.com/tutorials/pdo-vs-mysqli-which-should-you-use--net-24059
 */


namespace Database\Connection;

class DatabaseConnection extends \mysqli{
    //default values will be overriden when calling constructor obsolete? yeah probably..
    protected $databaseHost = "localhost";
    protected $databaseUser = "root"; 
    protected $databasePassword = "toor";
    protected $databaseName = "todo";
    protected $databasePort = null;
    protected $result = null;
    public $data;

    
    public function __construct($dbHost, $dbUser, $dbPasswd, $dbName, $dbPort=null){
        $this->databaseHost = $dbHost;
        $this->databaseUser = $dbUser;
        $this->databasePassword = $dbPasswd;
        $this->databaseName = $dbName;
        if (isset($dbPort)) {
            $this->databasePort = $dbPort;
        }
        
        /**
         *  connect to server
         *  Database name left empty to ensure we can connect to the server
         *  and don't fail because the database doesn't exist 
         *  I check that later on
         */
        try {
            parent::__construct($this->databaseHost, $this->databaseUser, $this->databasePassword, $this->databaseName, $this->databasePort);
        } catch (Exception $e) {
            echo 'Fehler aufgetreten: ' . $e->getMessage();
        }
   }
   
    public function execute($query){
        $this->data = array();

        $result = $this->query($query);
        
        if($result !== FALSE && $result !== TRUE) {
            for ($i = 0; $i <= $result->num_rows; $i++){
                $this->data[] = $result->fetch_assoc();
            }
            $result->free();
            return $this->data;
        }
        else {
            return FALSE;
        }
    }
}
