<?php

namespace ToDo\Template;


/*
 * 
 *  */

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

// if render wont work out..:    
//    public function replace($arrPlaceholders,$arrPlaceholdersValues) {
//        $template = str_replace($arrPlaceholders, $arrPlaceholdersValues, $templateIndex);
//        return $template;
//    }
    
    public function render(){
        $template = file_get_contents($this->templatePath);
        foreach($this->arrPlaceholders as $placeholder => $value){
            $template = str_replace($placeholder, $value, $template);
        }
        $this->renderedTemplate = $template;
    }
}


