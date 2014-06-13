<?php
/**
 * May needs an update as soon as Database gets updated changes and so on..
 */
namespace ToDo\Needs;

class Todo {
    protected $id;
    protected $name;
    protected $description;
    protected $category_id; // type category-object
    protected $user_id; // type user-object
    protected $state_id; // type state-object
    protected $content;
    
    public  function __construct($doWhat=NULL, $name=NULL, $description=NULL, $category_id=NULL, $state_id=NULL){  // $name, $description, $user_id, $state_id
        $this->name = $name;
        $this->description = $description;
        $this->category_id = $category_id;
        $this->user_id = $_SESSION['userid'];
        $this->state_id = $state_id;
        
        switch ($doWhat) {
            case 'createTodo':
                self::createToDo();
                break;
            case 'listMyToDos':
                self::listToDo($this->user_id);
                break;
            case 'listAllToDos':
                self::listToDo();
                break;
            case '':
                break;
            default:
                self::listToDo($this->user_id);
                break;
        }
        
    }
    
    public function getContent(){
        return $this->content;
    }
    protected function listToDo($userId) {
        global $database;
        if (!empty($userId)){
            $query = "SELECT `name` AS name, 
                             `description` AS description, 
                             `category_id` AS category_id, 
                             `state_id` AS state_id
                      FROM `td_todo`
                      WHERE `user_id` = $this->user_id;";
        }else {
            $query = "SELECT `name` AS name, 
                             `description` AS description, 
                             `category_id` AS category_id, 
                             `state_id` AS state_id, 
                             `user_id` AS user_id
                      FROM `td_todo`;";
        }
        
        $result = $database->execute($query);
        var_dump('$resutl: ' . $result);
        if(empty($result)) {
            $this->content = '<li class="todo">Keine ToDos vorhanden</li>';
        } else {
            foreach($database->data as $key => $result){
                    $todo = array();

                    $todo[$key]['name'] = $result[$key]['name'];
                    $todo[$key]['description'] = $result[$key]['description'];
                    $todo[$key]['category'] = $result[$key]['category'];
                    $todo[$key]['state'] = $result[$key]['state'];

                    $content .= '<li class="todo"><h2 class="todotitle">' 
                                    . $todo[$key]['name'] . '</h2>
                                    <div class="category">' .$todo[$key]['category']
                                    . '</div><div class="state">' 
                                    . $todo[$key]['state'] . '</div></li>';
            }
            $this->content = $content;
        }
    }
    
    protected function editToDo($id, $category, $state, $user){
        global $database;
        $query = "SELECT name, description, category, state, user 
                  FROM td_todo 
                  WHERE id = $id 
                      AND user_id = $user;";
        $database->execute($query);
        
        foreach($result as $key => $value){
            $todo = array();
            
            $todo[$key]['name'] = $value[$key]['name'];
            $todo[$key]['description'] = $value[$key]['description'];
            $todo[$key]['category'] = $$category->getCategory($value[$key]['category_id']);
            $todo[$key]['state'] = $state->getState($value[$key]['state_id']);
            $todo[$key]['user'] = $user->getUser($value[$key]['user_id']);
        }
    }
    
    protected function createToDo() {
        global $database;
        $query = 'INSERT INTO `td_todo`(`name`, 
                                        `description`, 
                                        `user_id`, 
                                        `category_id`, 
                                        `state_id`)
                                 VALUES(' . $database->mysqli->real_escape_string($this->name);
    }
}
