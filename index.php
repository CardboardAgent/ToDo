<?php
// debugging: LOL
ini_set('display_errors', 1);
error_reporting(E_ALL);
/**
 * needs an update, case default shuldn't be login or redirect to backend if 
 * $_SESSION['logedin'] is set. 
 */    

require_once './database/connection/config.php';
require_once './database/connection/database.class.php';
require_once './todo/needs/todo.class.php';
require_once './todo/user/user.class.php';
require_once './todo/template/template.class.php';
require_once './todo/category/category.class.php';

$templateIndex = './todo/templates/index.html';

$database = new \Database\Connection\DatabaseConnection('localhost', 'root', 'toor', 'todo');
$user = new \ToDo\User\User();
$template = new \ToDo\Template\template($templateIndex);

$section = (filter_input(INPUT_GET, 'section') ? filter_input(INPUT_GET, 'section') : 'login');

$do = filter_input(INPUT_GET, 'do');

switch ($section){
    case 'register':
        // content-template:
        $templateContent = file_get_contents('./todo/templates/register.html');
        // array with placeholders and custom content:
        $template->arrPlaceholders = array('[TODOTITLE]' => 'Registrieren', 
                                           '[NAVIGATION]' => $template->navigationContent, 
                                           '[SIDEBAR]' => $template->sidebarContent, 
                                           '[CONTENT]' => $templateContent);
        // magic happens here, replace placeholders with content:
        $template->render();
        
        if ($do == 'createUser') {
            $user->createUser();
            $msg = $user->getErrorMsg();
        }
        
        if (!empty($msg)){
            $template->replace('[TODO_ERROR]', $msg);
        }
        
        echo $template->renderedTemplate;
        break;

    case 'logedin':
        sessionStart(); // start the Session
        
        if(!isset($_SESSION['userId'])){ //
            header("Location: index.php?section=login");
        }
        $_SESSION['logedin'] = true;

        $template->arrPlaceholders = array('[TODOTITLE]' => 'Backend', 
                                           '[NAVIGATION]' => $template->navigationContent, 
                                           '[SIDEBAR]' => $template->sidebarContent, 
                                           '[CONTENT]' => 'Bitte wählen Sie eine Option aus der Navigation links');
        $template->render();
        echo $template->renderedTemplate;
        break;
    
    case 'logout':
        sessionStart(); // start the Session
        session_unset();
        header("Location: index.php?section=login");
        break;
        
    case 'myTodos':
        sessionStart(); // start the Session
        
        $todo = new \ToDo\Needs\Todo(NULL, NULL, NULL, NULL, $_SESSION['userId']);
        $content = $todo->listTodos($_SESSION['userId']);
        $myTodoList = new \ToDo\Template\template('./todo/templates/myToDos.html');
        $myTodoList->arrPlaceholders = array('[TODO_MYTODOS]' => $content);
        $myTodoList->render();
        
        $myTodoDos = $myTodoList->renderedTemplate;
        
        $template->arrPlaceholders = array('[TODOTITLE]' => 'MyToDos',
                                           '[NAVIGATION]' => $template->navigationContent,
                                           '[SIDEBAR]' => $template->sidebarContent,
                                           '[CONTENT]' => $myTodoDos);
        $template->render();
        $myTodoList = NULL;
        echo $template->renderedTemplate;
        if ($do == 'delete') {
            $id = filter_input(INPUT_GET, 'id');
            $todo = new ToDo\Needs\Todo($id);
            $success = $todo->delete();
            if ($success) {
                header("Location: index.php?section=myTodos");
            }
        } elseif ($do == 'edit') {
            $id = filter_input(INPUT_GET, 'id');
            $todo = new ToDo\Needs\Todo($id);
            $todo->editToDo($id, NULL, 1, $_SESSION['userId']);
        }
        break;
    
    case 'openTodos':
        sessionStart();
        
        $todo = new ToDo\Needs\Todo(NULL, NULL, NULL, NULL, $_SESSION['userId']);
        $content = $todo->listOpenTodos();
        
        $templateTodos = new ToDo\Template\template('./todo/templates/myToDos.html');
        $templateTodos->arrPlaceholders = array('[TODO_MYTODOS]' => $content);
        $templateTodos->render();
        
        $template->arrPlaceholders = array('[TODOTITLE]' => 'Offene Todos',
                                           '[NAVIGATION]' => $template->navigationContent,
                                           '[SIDEBAR]' => $template->sidebarContent,
                                           '[CONTENT]' => $templateTodos->renderedTemplate);
        $template->render();
        $templateTodos = null;
        echo $template->renderedTemplate;
        break;
    
    case 'category':
        sessionStart();
        
        $category = new \ToDo\Needs\category('Neue Kategorie', 0);
        $categoryList = $category->getCategoryList();
        
        $categoriesTemplate = new ToDo\Template\template('./todo/templates/categories.html');
        $categoriesTemplate->arrPlaceholders = array('[TODO_CATEGORIES]' => $categoryList);
        $categoriesTemplate->render();

        $template->arrPlaceholders = array('[TODOTITLE]' => 'Kategorien',
                                           '[NAVIGATION]' => $template->navigationContent,
                                           '[SIDEBAR]' => $template->sidebarContent,
                                           '[CONTENT]' => $categoriesTemplate->renderedTemplate);
        $template->render();
        echo $template->renderedTemplate;
        break;
    
    case 'editCategory':
        sessionStart();
        
        $id = filter_input(INPUT_GET, 'id');

        $category = new \ToDo\Needs\category($id);
        $content = $category->editCategory($id);
        
        $templateContent = new ToDo\Template\template('./todo/templates/createCategory.html');
        $templateContent->arrPlaceholders = array('[CATNAME]' => $content['name'],
                                                  '[CATPARENTID]' => $content['parentIdDropDown']);
        $templateContent->render();

        $template->arrPlaceholders = array('[TODOTITLE]' => 'Kategorie bearbeiten',
                                           '[NAVIGATION]' => $template->navigationContent,
                                           '[SIDEBAR]' => $template->sidebarContent,
                                           '[CONTENT]' => $templateContent->renderedTemplate);
        $template->render();
        $templateContent = NULL;
        echo $template->renderedTemplate;
        break;
    
    case 'createCategory':
        sessionStart();
        
        if (empty($do)) {
            $category = new \ToDo\Needs\category(NULL, 'Hier kommt der Name');
            $content = $category->create();

            $templateContent = new ToDo\Template\template('./todo/templates/createCategory.html');
            $templateContent->arrPlaceholders = array('[CATNAME]' => $content['name'],
                                                      '[CATPARENTID]' => $content['parentId']);
            $templateContent->render();
            
            $template->arrPlaceholders = array('[TODOTITLE]' => 'Todo erstellen',
                                               '[NAVIGATION]' => $template->navigationContent,
                                               '[SIDEBAR]' => $template->sidebarContent,
                                               '[CONTENT]' => $templateContent->renderedTemplate);
            $template->render();
            $templateContent = NULL;
            echo $template->renderedTemplate;
        } elseif ($do == 'save') {
            if (filter_input(INPUT_POST, 'submit')) {
                $categoryName = filter_input(INPUT_POST, 'name');
                $categoryParent = filter_input(INPUT_POST, 'parentId');
                $category = new \ToDo\Needs\category(NULL, $categoryName, $categoryParent);
                $category->save($categoryName, $categoryParent);
                //header("Location: index.php?section=logedin");
            } else {
                header("Location: index.php?section=logedin");
            }
        }
        break;
        
    case 'create':
        sessionStart();
        
        if (empty($do)) {
            $todo = new ToDo\Needs\Todo(NULL, 'Hier kommt der Name', 'Hier der Inhalt', 'Bitte Kategorie auswählen', $_SESSION['userId']);
            $content = $todo->create();

            $templateContent = new ToDo\Template\template('./todo/templates/createTodo.html');
            $templateContent->arrPlaceholders = array('[TODONAME]' => $content['name'],
                                                      '[TODOCATEGORY]' => $content['categories'],
                                                      '[TODOUSER]' => $content['users'],
                                                      '[TODOCONTENT]' => $content['content'],
                                                      '[TODOSTATE]' => '<input type="text" name="state" value="1" />');
            $templateContent->render();
            
            $template->arrPlaceholders = array('[TODOTITLE]' => 'Todo erstellen',
                                               '[NAVIGATION]' => $template->navigationContent,
                                               '[SIDEBAR]' => $template->sidebarContent,
                                               '[CONTENT]' => $templateContent->renderedTemplate);
            $template->render();
            $templateContent = NULL;
            echo $template->renderedTemplate;
        } elseif ($do == 'save') {
            if (filter_input(INPUT_POST, 'submit')) {
                $todoName = filter_input(INPUT_POST, 'name');
                $todoCategory = filter_input(INPUT_POST, 'category');
                $todoUser = filter_input(INPUT_POST, 'user');
                $todoContent = filter_input(INPUT_POST, 'content');
                $todo = new ToDo\Needs\Todo(NULL, $todoName, $todoContent, $todoCategory, $todoUser);
                $todo->save();
                header("Location: index.php?section=logedin");
            } else {
                header("Location: index.php?section=logedin");
            }
        }
        break;
        
    case 'editTodo':
        sessionStart();
        
        $id = filter_input(INPUT_GET, 'id');
        $userId = $_SESSION['userId'];
        
        $todo = new ToDo\Needs\Todo($id);
        $content = $todo->editToDo($id, NULL, 1, $userId);
        
        $templateContent = new ToDo\Template\template('./todo/templates/createTodo.html');
        $templateContent->arrPlaceholders = array('[TODONAME]' => $content['name'],
                                                  '[TODOCATEGORY]' => $content['categories'],
                                                  '[TODOUSER]' => $content['users'],
                                                  '[TODOCONTENT]' => $content['content'],
                                                  '[TODOSTATE]' => '<input type="text" name="state" value="1" />');
        $templateContent->render();

        $template->arrPlaceholders = array('[TODOTITLE]' => 'Todo erstellen',
                                           '[NAVIGATION]' => $template->navigationContent,
                                           '[SIDEBAR]' => $template->sidebarContent,
                                           '[CONTENT]' => $templateContent->renderedTemplate);
        $template->render();
        $templateContent = NULL;
        echo $template->renderedTemplate;
        break;
    
    case 'editUser':
        sessionStart();
        if (empty($do)) {
            $userData = $user->editUser($_SESSION['userId']);

            $templateUser = new ToDo\Template\template('./todo/templates/editUser.html');
            $templateUser->arrPlaceholders = array('[USERNAME]' => $userData['username'],
                                                   '[USERFIRST]' => $userData['firstname'],
                                                   '[USERLAST]' => $userData['lastname']);
            $templateUser->render();

            $template->arrPlaceholders = array('[TODOTITLE]' => 'Benutzer bearbeiten',
                                               '[NAVIGATION]' => $template->navigationContent,
                                               '[SIDEBAR]' => $template->sidebarContent,
                                               '[CONTENT]' => $templateUser->renderedTemplate);
            $template->render();
            echo $template->renderedTemplate;
        } elseif ($do == 'save') {
            $user->save($_SESSION['userId']);
            header("Location: index.php?section=logedin");
        }
        break;
    
    case 'login':
    default : // login
        if (isset($_SESSION['userId'])){
            header("Location: index.php?section=logedin"); // authentication already done, redirect to logedin
        }
        // content-template:
        $templateContent = file_get_contents('./todo/templates/login.html');
        // prepare the array with placeholdervalues:
        $template->arrPlaceholders = array('[TODOTITLE]' => 'Login', 
                                           '[NAVIGATION]' => $template->navigationContent , 
                                           '[SIDEBAR]' => $template->sidebarContent, 
                                           '[CONTENT]' => $templateContent);
        // magic happens here, replace placeholders with content:
        $template->render();
        
        if ($do == 'login'){
            $user->checkUser();
            if ($user->getUserExists()){
                session_start(); // start the Session
                $_SESSION['username'] = $user->getUsername();
                $_SESSION['userId'] = $user->getUserId(); // get the UserId and store it in the Session
                header("Location: index.php?section=logedin"); // send to section logedin
            } else {
                $template->replace('[TODO_ERROR]', 'Benutername oder Passwort falsch, bitte &uuml;berpr&uuml;fen Sie die Eingabe');
            }
        }
        
        echo $template->renderedTemplate;
        break;
}

function sessionStart() {
    session_start();

    if (empty($_SESSION['logedin']) && (empty($_SESSION['userId']))) {
        header("Locaion: index.php?section=login");
    }
}