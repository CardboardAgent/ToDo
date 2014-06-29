<?php
/**
 * Description of category
 *
 * @author Nick
 */
namespace ToDo\Needs;
class category {
    private $name;
    private $id;
    private $parentId;
    
    public function __construct($id=NULL, $name=NULL, $parentId=NULL) {
        $this->name = $name;
        $this->parentId = $parentId;
        $this->id = (!empty($id) ? intval($id) : NULL);
    }
    
    public function getCategoryList() {
        global $database;
        
        $query = "SELECT `name`, `id` FROM td_category ORDER BY `id`";
        $result = $database->execute($query);
        $categories = '';
        if ($database->data !== FALSE) {
            foreach ($database->data as $key=>$category) {
                if (empty($category)) {
                    continue;
                }
                $categories .= '<li><a href="index.php?section=editCategory&id='
                        . $category['id'] . '" title="Kategorie '. $category['name'] 
                        . ' bearbeiten">' . $category['name'] . '</a></li>';       
            }
        } else {
            $categories .= '<li>Keine Kategorien vorhanden bitte erstellen</li>';
        }
        return $categories;
    }
    
    public function editCategory($id, $name=null) {
        global $database;
        
        if (!empty($name)) {
            $where = ' AND `name` = ' . $database->real_escape_string($name);
        } else {
            $where = '';
        }
        $query = 'SELECT `id`, `name`, `subcategory_of` FROM td_category 
                    WHERE `id` = ' . intval($id) . $where;
        $database->execute($query);
        
        $this->name = $database->data[0]['name'];
        $this->parentId = $database->data[0]['subcategory_of'];
        $this->id = $database->data[0]['id'];
        return array('id' => $this->id, 'name' => $this->name, 'parentId' => $this->parentId,
            'parentIdDropDown' => $this->getCategoryDropDown());
    }
    
    public function create() {
        $categoryList = $this->getCategoryDropDown();
        
        return array('name' => $this->name,
                     'parentId' => $categoryList);
    }

    public function save($name, $parentId=NULL) {
        global $database;
        $parentId = (!empty($parentId) ? $parentId : 0);
        $query = 'INSERT INTO td_category(`name`, `subcategory_of`) VALUES
                        (\'' . $database->real_escape_string($name) . '\', ' . intval($parentId) . ')';
        var_dump($query);
        $database->execute($query);
        return TRUE;
    }
    
    private function getCategoryDropDown() {
        global $database;
        
        $query = "SELECT `name`, `id` FROM td_category";
        $database->execute($query);
        $categories = '<select name="parentId">';
        if ($database->data !== FALSE) {
            $categories .= '<option value="0">Haupkategorie</option>';
            foreach ($database->data as $key=>$category) {
                if (empty($category)) {
                    continue;
                }
                if (isset($this->categoryId) == $category['id']) {
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
}