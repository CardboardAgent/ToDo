<?php

/**
 * check: http://ch1.php.net/manual/en/class.pdo.php
 * also : http://code.tutsplus.com/tutorials/pdo-vs-mysqli-which-should-you-use--net-24059
 * Needs an update: ! 
 */


namespace Database\Connection;

class DatabaseConnection {
    protected $databaseHost = "localhost"; //default localhost
    protected $databaseUser = "root";
    protected $databasePassword = "toor";
    protected $databaseName = "todo";
    public $link;
    
    
//    public function __construct() { // $databaseHost, $databaseName, $databaseUser, $databasePassword
//        // connect to server:
//        $link = mysqli_init();
//        $this->link = $link;
//        $connection = mysqli_real_connect($link, $this->databaseHost, $this->databaseUser, $this->databasePassword);
//        // check if connection was successfully
//        if ($connection === false) {
//            echo '<p class="db-error">Konnte keine Verbindung zum Datenbankserver herstellen. Bitte Konfiguration &uuml;berpr&uuml;fen. SQL-Fehlermeldung: ' .  mysqli_errno($this->link).': ' .mysqli_error($this->link) .'</p>';
//        }else {
//            // connect to the database
//            $connected = mysqli_select_db($link, $this->databaseName);// $connected = boolean True on success
//            // check if connected succesfully
//            if ($connected === true){
//                return true;
//            }else {
//                echo '<p class="select-db-error>Konnte keine Verbindung zur angegebenen Datenbank herstellen. Bitte Konfiguration &uuml;berpr&uuml;fen. SQL-Fehlermeldung: ' .mysqli_errno($this->link).': ' .mysqli_error($this->link) .'</p>';
//                return false;
//            }
//        }
//   }
//   
//    public function execute($query){
//        // $query = mysqli_real_escape_string($this->link, $query);
//        mysqli_real_query($this->link, $query);
//        return mysqli_store_result($this->link);
//    }
//    
//    public function __destruct() {
//        // mysqli_close($this->link);
//    }
}


