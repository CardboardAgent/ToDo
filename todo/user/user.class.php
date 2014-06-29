<?php
/**
 * May needs an update as well as todo.class.php because changes in database.class.php
 */
namespace ToDo\User;

class User {
    protected $id, $username, $passwd, $firstname, $lastname, $result, $exists, $errormsg;
    
    public function __construct($id=NULL) {
        $this->id = intval($id);
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
        
        $query = 'SELECT `id`
                  FROM `td_user` 
                  WHERE `username` = "' . $this->username .'";';
        $database->execute($query);
        $this->id = $database->data[0]['id'];
        return $this->id;
    }
    
    public function getErrorMsg() {
        return $this->errormsg;
    }
    
    public function createUser() {
        global $database;
        
        $this->username = $_POST['username'];
        $this->passwd = $_POST['password'];
        $this->username = $database->real_escape_string($this->username);
        $this->passwd = md5($database->real_escape_string($this->passwd));
        
        $query = 'INSERT INTO `td_user`(`username`, `password`) 
                  VALUES(\''.$this->username.'\', \''.$this->passwd.'\');';
        $database->execute($query);
        
        if ($database->errno == 1062){
            $this->errormsg = 'Benutzername bereits in Verwendung, bitte w&auml;hlen Sie einen neuen.';
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
        
        $result = $database->execute($query);
        
        if (isset($database->data[0])){
            $this->exists = TRUE;
        } else {
            $this->exists = FALSE;
        }
    }
    
    public function editUser($id) {
        global $database;
        
        $this->id = intval($id);
        
        $query = 'SELECT `username`, `first_name`, `last_name` FROM td_user
            WHERE `id` = ' . intval($this->id);
        $database->execute($query);
        var_dump($query);
        return array('username' => utf8_encode($database->data[0]['username']),
                     'firstname' => utf8_encode($database->data[0]['first_name']),
                     'lastname' => utf8_encode($database->data[0]['last_name']));
    }
    
    public function save($id) {
        global $database;
        
        $this->id = intval($id);
        $this->username = filter_input(INPUT_POST, 'username');
        $this->passwd = filter_input(INPUT_POST, 'repassword');
        $this->firstname = filter_input(INPUT_POST, 'firstname');
        $this->lastname = filter_input(INPUT_POST, 'lastname');
        
        $where = ' WHERE `id` = ' . intval($this->id) . '; ';
        $query = 'UPDATE td_user
                    SET `username` = \'' . $this->username . '\'' . $where;
        $query .= 'UPDATE td_user
                    SET `first_name` = \'' . $database->real_escape_string(utf8_decode($this->firstname)) . '\'' . $where;
        $query .= 'UPDATE td_user
                    SET `last_name` = \'' . $database->real_escape_string(utf8_decode($this->lastname)) . '\'' . $where;
        if (!empty($this->passwd)) {
            $query .= 'UPDATE td_user
                        SET `password` = \'' . md5($this->passwd) . '\'' . $where;
        }
        var_dump($query);
        $database->execute($query);
        
    }
}
