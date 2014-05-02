<?php
/**
 * May needs an update as well as todo.class.php because changes in database.class.php
 */
namespace ToDo\User;

class User {
    protected $id, $username, $passwd, $firstname, $lastname, $result, $exists, $errormsg;
    
    public function __construct() {
    }
    
    public function getUserExists() {
        if (!$this->exists){
            return false;
        } else {
            return true;
        }
    }
    
    public function getUsername() {
        return $this->username;
    }
    
    public function getUserId() {
        global $database;
        
        $query = 'SELECT `id` AS id
                  FROM `td_user` 
                  WHERE `username` = "' . $this->username .'";';
        $database->execute($query);
        $this->id = $database->data[0];
        var_dump($database->data);
        $database->freeResult();
        return $this->id;
    }
    
    public function getErrorMsg() {
        return $this->errormsg;
    }
    
    public function createUser() {
        global $database;
        
        $this->username = $_POST['username'];
        $this->passwd = $_POST['password'];
        $this->username = $database->escape_string($_POST['username']);
        $this->passwd = md5($database->escape_string($_POST['password']));
        
        $query = 'INSERT INTO `td_user`(`username`, `password`) 
                  VALUES(\''.$this->username.'\', \''.$this->passwd.'\');';
        $database->execute($query);
        
        if ($database->errno == 1062){
            $errormsg = 'Benutzername bereits in Verwendung, bitte w&auml;hlen Sie einen neuen.';
            $this->errormsg = $errormsg;
        }
    }
    
    public function checkUser() {
        global $database;
        
        $this->username = $_POST['username'];
        $this->passwd = md5($_POST['passwd']);
        
        $query = 'SELECT `username` AS username,
                         `password` AS password
                  FROM `td_user` 
                  WHERE `username` = "' . $this->username . '" 
                      AND password = "' . $this->passwd .'";';
        
        $database->execute($query);
        var_dump($database->data);
        if (array_key_exists(0, $database->data)){
            $this->exists = TRUE;
        } else {
            $this->exists = FALSE;
        }
        var_dump($database->data);
        $database->freeResult();
    }
}
