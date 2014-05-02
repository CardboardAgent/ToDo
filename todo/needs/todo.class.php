<?php
/**
 * May needs an update as soon as Database gets updated changes and so on..
 */
namespace ToDo\Needs;

class Todo {
    protected $id;
    protected $name;
    protected $description;
    protected $category; // type category-object
    protected $user; // type user-object
    protected $state; // type state-object
    
    public  function __construct($doWhat=NULL){  // $name, $description, $user_id, $state_id
        switch ($doWhat) {
            case 'createTodo':
                self::createToDo();
                break;

            default:
                break;
        }
        
    }
    
//    $query = "INSERT INTO `td_todo` (
//                                            `name`,
//                                            `description`,
//                                            `user_id`,
//                                            `state_id`
//                                        )
//                                 VALUES (
//                                            $name,
//                                            $description,
//                                            $user_id,
//                                            $state_id
//                                        );";

    protected function listToDo($userId) {
        global $database;
        if (!empty($userId)){
            $query = "SELECT `name` AS name, 
                             `description` AS description, 
                             `category_id` AS category_id, 
                             `state_id` AS state_id
                      FROM `td_todo`
                      WHERE `user_id` = $userId;";
        }else {
            $query = "SELECT `name` AS name, 
                             `description` AS description, 
                             `category_id` AS category_id, 
                             `state_id` AS state_id, 
                             `user_id` AS user_id
                      FROM `td_todo`;";
        }
        
        $database->execute($query);
        
        foreach($database->data as $key => $result){
                $todo = array();
                
                $todo[$key]['name'] = $result[$key]['name'];
                $todo[$key]['description'] = $result[$key]['description'];
                $todo[$key]['category'] = $result[$key]['category'];
                $todo[$key]['state'] = $result[$key]['state'];
                
                $content .= '<div class="todo"><h2 class="title">' . $todo[$key]['name'] . '</h2><div class="clearfix"><div class="category">' . $todo[$key]['category'] . '</div><div class="state">' . $todo[$key]['state'] . '</div></div><div class="description">' . $todo[$key]['description'] . '</div></div>';
            }
            if (strpos(file_get_contents($template), $placeholder)){
                str_replace($placeholder, $content, $template);
            }else {
                str_replace('[TODO_ERROR]', '<div id="error-msg">Platzhalter'.$placeholder.'nicht gefunden, bitte &uuml;berpr&uuml;fen Sie das Template</div>', $template);
            }
    }


    protected function getToDo($id){
        $id = intval($id); // make sure we only get integers
        $placeholder = '[TODO]';
        $template = '../templates/list-todo.html';
        
        $query = "SELECT name, description, category, state, user 
                  FROM td_todo 
                  WHERE id = $id;";
        $result = \DatabaseConnection::execute($query);
        if (strpos(file_get_contents($template), $placeholder)){
            $content = '<div class="todo"><h2 class="title">' . $result[0]['name'] . '</h2><div class="clearfix"><div class="category">' . $result[0]['category'] . '</div><div class="state">' . $result[0]['state'] . '</div></div><div class="description">' . $result[0]['description'] . '</div></div>';
            str_replace($placeholder, $content, $template);
        }else {
            str_replace('[TODO_ERROR]', '<div id="error-msg">Platzhalter'.$placeholder.'nicht gefunden, bitte %uuml;berpr&uuml;fen Sie das Template</div>', $template);
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
            $todo[$key]['category'] = \Todo\Category::getCategory($value[$key]['category_id']);
            $todo[$key]['state'] = \Todo\State::getState($value[$key]['state_id']);
            $todo[$key]['user'] = \Todo\User::getUser($value[$key]['user_id']);
        }
        
    }
}
