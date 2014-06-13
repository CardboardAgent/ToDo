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

$templateIndex = './todo/templates/index.html';

$database = new \Database\Connection\DatabaseConnection('localhost', 'root', 'toor', 'todo');
$user = new \ToDo\User\User();
$template = new \ToDo\Template\template($templateIndex);

// check if section is given, else set default value so we don't get any warnings:
if (array_key_exists('section', $_GET)){
    $section = $_GET['section'];
} else {
    $section = 'login';
}

// check if do is given, else unset it so we don't get any warnings:
if (array_key_exists('do', $_GET)){
    $do = $_GET['do'];
} else {
    $do = null;
}

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
        session_start(); // start the Session
        
        if(!isset($_SESSION['userid'])){ //
            header("Location: index.php?section=login");
        }
        
        $_SESSION['logedin'] = true;
        // http://stackoverflow.com/questions/520237/how-do-i-expire-a-php-session-after-30-minutes
        
        $templateContent = file_get_contents('./todo/templates/backend.html');
        $template->arrPlaceholders = array('[TODOTITLE]' => 'Backend', 
                                           '[NAVIGATION]' => $template->navigationContent, 
                                           '[SIDEBAR]' => $template->sidebarContent, 
                                           '[CONTENT]' => $templateContent);
        $template->render();
        echo $template->renderedTemplate;
        break;
    
    case 'logout':
        session_start(); // start the Session
        unset($_SESSION['userid']);
        unset($_SESSION['logedin']);
        header("Location: index.php?section=login");
        break;
        
    case 'myToDos':
        session_start(); // start the Session
        
        $todo = new \ToDo\Needs\Todo('listMyToDos');
        $myTodoList = new \ToDo\Template\template('./todo/templates/myToDos.html');
        $myTodoList->arrPlaceholders = array('[TODO_MYTODOS]' => $todo->getContent());
        $myTodoList->render();
        
        $myTodoDos = $myTodoList->renderedTemplate;
        $myTodoList = NULL;
        
        $template->arrPlaceholders = array('[TODOTITLE]' => 'MyToDos',
                                           '[NAVIGATION]' => $template->navigationContent,
                                           '[SIDEBAR]' => $template->sidebarContent,
                                           '[CONTENT]' => $myTodoDos);
        $template->render();
        echo $template->renderedTemplate;
        break;
        
    case 'login':
    default : // login
        if (isset($_SESSION['userid'])){
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
                $_SESSION['userid'] = $user->getUserId(); // get the UserId and store it in the Session
                header("Location: index.php?section=logedin"); // send to section logedin
            } else {
                $template->replace('[TODO_ERROR]', 'Benutername oder Passwort falsch, bitte &uuml;berpr&uuml;fen Sie die Eingabe');
            }
        }
        
        echo $template->renderedTemplate;
        break;
}
// $database = null;