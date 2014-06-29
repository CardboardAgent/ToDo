<?php
/**
 * 
 */
namespace ToDo\Needs;

class Todo {
    protected $id;
    protected $name;
    protected $description;
    protected $categoryId; // type category-object
    protected $userId; // type user-object
    protected $stateId; // type state-object
    protected $content;
    
    public  function __construct($id=NULL, $name=NULL, $description=NULL, $categoryId=NULL, $userId=NULL, $stateId=NULL){  // $name, $description, $user_id, $state_id
        $this->id = intval($id);
        $this->name = $name;
        $this->description = $description;
        $this->categoryId = $categoryId;
        $this->userId = (empty($userId) ? $_SESSION['userId'] : $userId);
        $this->stateId = $stateId;
    }
    
    public function getContent(){
        return $this->content;
    }
    
    /**
     * Creates Select-List which contains all the existing Categories
     * @global object $database
     * @return string
     */
    private function getCategoryList() {
        global $database;
        
        $query = "SELECT `name`, `id` FROM td_category";
        $database->execute($query);
        $categories = '<select name="category">';
        if ($database->data !== FALSE) {
            $categories .= '<option value="0">Bitte Kategorie auswählen</option>';
            foreach ($database->data as $key=>$category) {
                if (empty($category)) {
                    continue;
                }
                if (isset($this->categoryId) && $category['id']) {
                    $selected = 'selected="selected"';
                } else {
                    $selected = '';
                }
                $categories .= '<option '. $selected . ' value="' . $category['id'] .'">' . $category['name'] . '</option>';       
            }
        } else {
            $categories .= '<option value="0">Keine Kategorien vorhanden bitte erstellen</option>';
        }
        $categories .= '</select>';
        return $categories;
    }
    
    /**
     * Creates Select-List which contains all the existing Users
     * @global object $database
     * @return string
     */
    private function getUserList() {
        global $database;
        $query = "SELECT `username`, `id` FROM td_user";
        $database->execute($query);
        $usres = '<select name="user" id="user">';
        $usres .= '<option value="0">Bitte Benutzer auswählen</option>';
        foreach ($database->data as $key=>$user) {
            if (empty($user)) {
                    continue;
                }
            if ($user['id'] == $_SESSION['userId']) {
                $selected = 'selected="selected"';
            } else {
                $selected = NULL;
            }
            $usres .= '<option ' . $selected . ' value="' . $user['id'] .'">' . $user['username'] . '</option>';       
        }
        
        $usres .= '</select>';
        return $usres;
        
    }
    
    public function listTodos($userId) {
        global $database;
        
        if (!empty($userId)){
            $query = "SELECT `id`,
                             `name`, 
                             `description`, 
                             `category_id`, 
                             `state_id`
                      FROM `td_todo`
                      WHERE `user_id` = $this->userId;";
        }else {
            $query = "SELECT `id`
                             `name`, 
                             `description`, 
                             `category_id`, 
                             `state_id`, 
                             `user_id`
                      FROM `td_todo`;";
        }
        
        $result = $database->execute($query);
        
        if(empty($result)) {
            $this->content = '<li class="todo">Keine ToDos vorhanden</li>';
        } else {
            $content = '';
            foreach($database->data as $key => $todo){
                if (empty($todo)) {
                    continue;
                }
                $content .= '<li class="todo"><h2 class="todotitle">' 
                                . $todo['name'] . '</h2>
                                <div class="category">Kategorie: ' . $todo['category_id']
                                . '</div><div class="state">Status: ' 
                                . $todo['state_id'] . '</div><div class="tools">
                                <a class="delete" href="index.php?section=myTodos&do=delete&id=' . $todo['id'] .'" title="Todo löschen">Löschen</a>
                                <a class="edit" href="index.php?section=editTodo&id=' . $todo['id'] . '" title="Todo bearbeiten">Bearbeiten</a></li>';
            }
            $this->content .= $content;
        }
        return $this->content;
    }
    
    public function listOpenTodos() {
        global $database;
        
        $query = "SELECT `name`, `category_id`, `id`, `state_id` FROM td_todo
                    WHERE `state_id` <> 0";
        $database->execute($query);
        $content = '';
        foreach($database->data as $key => $todo){
            if (empty($todo)) {
                continue;
            }
            $content .= '<li class="todo"><h2 class="todotitle">' 
                            . $todo['name'] . '</h2>
                            <div class="category">Kategorie: ' . $todo['category_id']
                            . '</div><div class="state">Status: ' 
                            . $todo['state_id'] . '</div><div class="tools">
                            <a class="delete" href="index.php?section=myTodos&do=delete&id=' . $todo['id'] .'" title="Todo löschen">Löschen</a>
                            <a class="edit" href="index.php?section=editTodo&id=' . $todo['id'] . '" title="Todo bearbeiten">Bearbeiten</a></li>';
        }
        $this->content = $content;
        return $this->content;
    }
    
    public function delete() {
        global $database;
        if (!empty($this->id)) {
            $query = "DELETE FROM td_todo WHERE `id` = " . $this->id;
            $database->execute($query);
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function editToDo($id){
        global $database;
        $query = 'SELECT name, description, category_id, state_id, user_id 
                  FROM td_todo 
                  WHERE id = ' . $id . '
                    LIMIT 1';
        $database->execute($query);
        $this->name = $database->data[0]['name'];
        $this->description = $database->data[0]['description'];
        $this->categoryId = $database->data[0]['category_id'];
        $this->stateId = $database->data[0]['state_id'];
        $this->userId = $database->data[0]['user_id'];
        $todo = $this->create();
        return $todo;
    }
    
    public function create() {
        $categoryList = $this->getCategoryList();
        $userList = $this->getUserList();
        
        return array('name' => $this->name,
                     'categories' => $categoryList,
                     'users' => $userList,
                     'content' => $this->description);
        
    }
    
    public function save($id=NULL) {
        global $database;
        if (isset($id)) {
            // update entry
        } else {
            $query = 'INSERT INTO `td_todo`(`name`, 
                                            `description`, 
                                            `user_id`, 
                                            `category_id`, 
                                            `state_id`)
                        VALUES(\'' . $database->real_escape_string($this->name) . '\''
                              .', \'' . $database->real_escape_string($this->description) . '\''
                              .', ' . intval($this->userId) . ''
                              .', ' . intval($this->categoryId) . ''
                              .', ' . intval($this->stateId) . ')';
            $database->execute($query);
        }
    }
}
