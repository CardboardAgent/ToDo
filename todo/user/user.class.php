<?php
namespace ToDo\User;

class User {
    protected $id, $username, $firstname, $lastname, $result;
    
    public function __construct() {
    }
    
    
    public function createUser($database) {
        
        $username = $_POST['username'];
        $passwd = $_POST['password'];
        $username = mysqli_escape_string($database->link, $username);
        $passwd = md5(mysqli_escape_string($database->link, $passwd));
        
        $query = 'INSERT INTO `td_user`(`username`, `password`) VALUES(\''.$username.'\', \''.$passwd.'\');';
        $database->execute($query);
        
        var_dump(mysqli_errno($database->link));
        
        if (mysqli_errno($database->link) == 1062){
            
        }
        
        $this->result = mysqli_use_result($database->link);
        
        // echo mysqli_errno($database->link) . ':' . mysqli_error($database->link);
        //header("Location: ./index.php?section=login");
    }
}
