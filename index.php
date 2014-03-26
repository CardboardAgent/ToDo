<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);    

require_once './database/connection/config.php';
require_once './database/connection/database.class.php';
$database = new \Database\Connection\DatabaseConnection();
require_once './todo/needs/todo.class.php';
require_once './todo/user/user.class.php';
require_once './todo/template/template.class.php';



$todo = new \ToDo\Needs\Todo();

$user = new \ToDo\User\User();

$templateIndex = './todo/templates/index.html';
$template = new \ToDo\Template\template($templateIndex);

// do the magic:
if (array_key_exists('section', $_GET)){
    $section = $_GET['section'];
} else {
    $section = 'login';
}
switch ($section){
    case 'register':
        // content-template:
        $templateContent = file_get_contents('./todo/templates/register.html');
        // array with placeholders and custom content:
        $template->arrPlaceholders = array('[TODOTITLE]' => 'Registrieren', '[NAVIGATION]' => $template->navigationContent, '[SIDEBAR]' => $template->sidebarContent, '[CONTENT]' => $templateContent);
        // magic happens here, replace placeholders with content:
        $template->render();

        echo $template->renderedTemplate;

        break;

    case 'createuser':
        $user->createUser($database);
        break;

    default : // login
        // content-template:
        $templateContent = file_get_contents('./todo/templates/login.html');
        // prepare the array with placeholdervalues:
        $template->arrPlaceholders = array('[TODOTITLE]' => 'Login', '[NAVIGATION]' => $template->navigationContent , '[SIDEBAR]' => $template->sidebarContent, '[CONTENT]' => $templateContent);
        // magic happens here, replace placeholders with content:
        $template->render();

        echo $template->renderedTemplate;

        break;
}
// $database->exit();