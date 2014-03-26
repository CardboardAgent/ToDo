<?php
// debugging: LOL
ini_set('display_errors', 1);
error_reporting(E_ALL);
/**
 * needs an update, case default shuldn't be login or redirect to backend if $_SESSION['logedin'] is set. 
 */    

require_once './database/connection/config.php';
require_once './database/connection/database.class.php';
require_once './todo/needs/todo.class.php';
require_once './todo/user/user.class.php';
require_once './todo/template/template.class.php';

$templateIndex = './todo/templates/index.html';

$todo = new \ToDo\Needs\Todo();
$user = new \ToDo\User\User();
$template = new \ToDo\Template\template($templateIndex);
$database = new \Database\Connection\DatabaseConnection();

// check if session is given, else set default value so we don't get any warnings:
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
// session_destroy gets called allthough the cookie logedin isset:
if (array_key_exists('logedin', $_COOKIE)){
    session_unset();
    session_destroy();
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
        $_SESSION['logedin'] = true; // Session does need to expire after maybe 30? I don't know, have a look at: http://stackoverflow.com/questions/520237/how-do-i-expire-a-php-session-after-30-minutes
        var_dump($_SESSION);
        
        $templateContent = file_get_contents('./todo/templates/backend.html');
        
        $template->arrPlaceholders = array('[TODOTITLE]' => 'Backend', 
                                           '[NAVIGATION]' => $template->navigationContent, 
                                           '[SIDEBAR]' => $template->sidebarContent, 
                                           '[CONTENT]' => $templateContent);
        $template->render();
        echo $template->renderedTemplate;
        break;
    
    case 'logout':
        //session_unset();
        //session_destroy();
        if (array_key_exists('logedin', $_COOKIE)){
            unset($_COOKIE['logedin']);
            setcookie('logedin', 'true', 1);
        }
        header("Location: index.php?section=login");
        break;
        
    case 'login':
    default : // login
        if (array_key_exists('logedin', $_COOKIE)){
            header("Location: index.php?section=logedin"); // authentication already done, further to logedin
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
            if ($user->getArrResult()){
                setcookie('logedin', 'true', (time()+1800)); // set cookie I use to delete the session once 30 Minutes passed..
                session_start(); // start the Session
                $_SESSION['userid'] = $user->getUserId($database); // get the UserId and store it in the Session
                var_dump($_SESSION);
                //header("Location: index.php?section=logedin"); // send to section logedin
            } else {
                $template->replace('[TODO_ERROR]', 'Benutername oder Passwort falsch, bitte &uuml;berpr&uuml;fen Sie die Eingabe');
            }
        }
        
        echo $template->renderedTemplate;
        break;
}
// $database = null;