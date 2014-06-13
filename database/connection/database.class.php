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
    public $mysqli;
    public $data; // result of the query against the database

    
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
        $this->mysqli = new \mysqli($this->databaseHost, 
                                  $this->databaseUser, 
                                  $this->databasePassword, 
                                  null,
                                  $this->databasePort);
        // check if connection was successfully
        if ($this->mysqli === false) {
            echo '<p class="db-error">Konnte keine Verbindung zum 
                Datenbankserver herstellen. 
                Bitte Konfiguration &uuml;berpr&uuml;fen.
                SQL-Fehlermeldung: ' .  $this->mysqli->errno  . ': ' 
                    . $this->mysqli->error . '</p>';
        } else {
            // connect to the database boolean True on success
            $connected = $this->mysqli->select_db($this->databaseName);
            // check if connected succesfully
            if ($connected === true){
                return TRUE;
            } else {
                echo '<p class="select-db-error>Konnte keine Verbindung zur
                    angegebenen Datenbank herstellen. Bitte Konfiguration
                    &uuml;berpr&uuml;fen. SQL-Fehlermeldung: ' . 
                        $this->mysqli->errno . ': ' . 
                        $this->mysqli->error . '</p>';
                return FALSE;
                // maybe ask if database shall be created? Might do this with 
                // an option in the config file which doesn't exist at this
                // point...
                // 
                // if ($arrConfig['createDB'] === true) {
                //     $query = "CREATE DATABASE $arrConfig['dbname'];";
                //     $mysqli->real_query($query);
                //     $mysqli->select_db($arrConfig['dbname']);
                //     create the tables using the script:
                //     $query = requier_once ./todo.sql;
                //     $mysqli->real_query($query);
                //     print("Datenbank erfolgreich erstellt!");
                //     sleep(30);
                //     header(Location: "/");
                // }
            }
        }
   }
   
    public function execute($query){
        $this->data = array();
        // execute query and store the result even if the query isn't a select
        // query but there won't be any content:
        $this->mysqli->real_query($query);
        $this->result = $this->mysqli->store_result();
        // fetch the result-object into an array if the executed query was a 
        // query that does return something e.g. SELECT *...:
        if($this->result === TRUE) {
            foreach($this->result->fetch_array() as $data){
                $this->data[] = $data;
            }
            return $this->data;
        }
        else {
            return FALSE;
        }
    }
    
    public function freeResult() {
        $this->result->free();
        return TRUE;
    }
    
    public function __destruct() {
        //$mysqli::close();
    }
}


