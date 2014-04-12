<?php

/**
 * check: http://ch1.php.net/manual/en/class.pdo.php
 * also : http://code.tutsplus.com/tutorials/pdo-vs-mysqli-which-should-you-use--net-24059
 * Needs an update: ! 
 */


namespace Database\Connection;

class DatabaseConnection {
    //default values will be overriden when calling constructor obsolete? yeah probably..
    protected $databaseHost = "localhost";
    protected $databaseUser = "root"; 
    protected $databasePassword = "toor";
    protected $databaseName = "todo";
    protected $databasePort = null;
    public $link; // might be obsolete now..

    
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
        $connection = new \mysqli($this->databaseHost, 
                                  $this->databaseUser, 
                                  $this->databasePassword, 
                                  null,
                                  $this->databasePort);
        // check if connection was successfully
        if ($connection === false) {
            echo '<p class="db-error">Konnte keine Verbindung zum 
                Datenbankserver herstellen. 
                Bitte Konfiguration &uuml;berpr&uuml;fen.
                SQL-Fehlermeldung: ' .  $connection->errno  . ': ' 
                    . $connection->error . '</p>';
        }else {
            // connect to the database boolean True on success
            $connected = $connection::select_db($this->databaseName);
            // check if connected succesfully
            if ($connected === true){
                return true;
            } else {
                echo '<p class="select-db-error>Konnte keine Verbindung zur
                    angegebenen Datenbank herstellen. Bitte Konfiguration
                    &uuml;berpr&uuml;fen. SQL-Fehlermeldung: ' . 
                        $connection->errno . ': ' . 
                        $connection->error . '</p>';
                return false;
                // maybe ask if database shall be created? Might do this with 
                // an option in the config file which doesn't exist at this
                // point...
                // 
                // if ($arrConfig['createDB'] === true) {
                //     $query = "CREATE DATABASE $arrConfig['dbname'];";
                //     $connection::real_query($query);
                //     $connection::select_db($arrConfig['dbname']);
                //     create the tables using the script:
                //     $query = requier_once ./todo.sql;
                //     $connection::real_query($query);
                //     print("Datenbank erfolgreich erstellt!");
                // }
            }
        }
   }
   
    public function execute($query){
        // $query = mysqli_real_escape_string($this->link, $query);
        $connection::real_query($query);
        return $connection::store_result($this->link);
    }
    
    public function __destruct() {
        $connection::close();
    }
}


