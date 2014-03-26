<?php
/**
 * May needs an update as well as todo.class.php because changes in database.class.php
 */
namespace ToDo\User;

class User {
    protected $id, $username, $firstname, $lastname, $result, $arrResult, $errormsg;
    
    public function __construct() {
    }
    
    public function getArrResult() {
        if (isset($this->arrResult)){
            return true;
        } else {
            return false;
        }
    }
    
    public function getUsername() {
        return $this->username;
    }
    
    public function getUserId($database) {
        $query = 'SELECT `id` 
                  FROM `td_user` 
                  WHERE `username` = "' . $this->username .'";';
        $this->result = $database->execute($query);
        $this->arrResult = mysqli_fetch_array($this->result);
        return $this->arrResult['id'];
    }
    
    public function getErrorMsg() {
        return $this->errormsg;
    }
    
    public function createUser($database) {
        $this->username = $_POST['username'];
        $this->passwd = $_POST['password'];
        $this->username = mysqli_escape_string($database->link, $username);
        $this->passwd = md5(mysqli_escape_string($database->link, $passwd));
        
        $query = 'INSERT INTO `td_user`(`username`, `password`) 
                  VALUES(\''.$this->username.'\', \''.$this->passwd.'\');';
        $database->execute($query);
        
        if (mysqli_errno($database->link) == 1062){
            $errormsg = 'Benutzername bereits in Verwendung, bitte w&auml;hlen Sie einen neuen.';
            $this->errormsg = $errormsg;
        }
    }
    
    public function checkUser($database) {
        $this->username = $_POST['username'];
        $this->passwd = md5($_POST['passwd']);
        
        $query = 'SELECT `username`, `password` 
                  FROM `td_user` 
                  WHERE `username` = "' . $this->username . '" 
                      AND password = "' . $this->passwd .'";';
        $this->result = $database->execute($query);
        $this->arrResult = mysqli_fetch_array($this->result);
        // print_r($this->arrResult);
    }
}
