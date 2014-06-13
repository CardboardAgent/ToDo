<?php

namespace ToDo\Template;


/**
 * 
 * May needs an update to apply a default value to the placeholders Sidebar and Navigation before I call render
 * 
 */

class template {
    public $navigationContent;
    public $sidebarContent;
    public $arrPlaceholders; // = array([TODOTITLE] => Title, [NAVIGATION] => Navigation, [SIDEBAR] => Sidebar, [CONTENT] => Content);
    public $templatePath;
    public $renderedTemplate;
    
    public function __construct($templatePath) {
        $this->templatePath = $templatePath;
        
        $this->navigationContent = file_get_contents('./todo/templates/navigation.html');
        $this->sidebarContent = file_get_contents('./todo/templates/sidebar.html');
    }
    
    public function render(){
        $template = file_get_contents($this->templatePath);
        foreach($this->arrPlaceholders as $placeholder => $value){
            $template = str_replace($placeholder, $value, $template);
        }
        $this->renderedTemplate = $template;
    }
    
    public function replace($placeholder, $value) {
        $this->renderedTemplate = str_replace($placeholder, $value, $this->renderedTemplate);
    }
}


