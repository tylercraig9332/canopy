<?php
/**
 * Category class.
 *
 * @version $Id$
 * @author  Matt McNaney <matt at tux dot appstate dot edu>
 * @package categories
 */

class Category{
  var $id          = NULL;
  var $title       = NULL;
  var $description = NULL;
  var $parent      = NULL;
  var $icon        = NULL;
  var $children    = NULL;


  function Category($id=NULL){
    if (empty($id))
      return;

    $this->setId($id);
    $result = $this->init();
    if (PEAR::isError($result))
      PHPWS_Error::log($result);
  }
  
  function init(){
    $db = & new PHPWS_DB('categories');
    $result = $db->loadObject($this);
    if (PEAR::isError($result))
      return $result;

    $this->loadIcon();
    $this->loadChildren();
  }

  function setId($id){
    $this->id = (int)$id;
  }

  function getId(){
    return $this->id;
  }

  function setTitle($title){
    $this->title = strip_tags($title);
  }

  function getTitle(){
    return $this->title;
  }

  function setDescription($description){
    $this->description = PHPWS_Text::parseInput($description);
  }

  function getDescription(){
    return PHPWS_Text::parseOutput($this->description);
  }

  function setParent($parent){
    $this->parent = (int)$parent;
  }

  function getParent(){
    return $this->parent;
  }

  function getParentTitle(){
    static $parentTitle = array();

    if ($this->parent == 0) {
      return _('Top Level');
    }

    if (isset($parentTitle[$this->parent]))
      return $parentTitle[$this->parent];

    $parent = & new Category($this->parent);
    $parentTitle[$parent->id] = $parent->title;

    return $parent->title;
  }

  function setIcon($icon){
    $this->icon = $icon;

    if (is_numeric($icon))
      $this->loadIcon();
  }

  function getIcon(){
    return $this->icon;
  }

  function loadIcon(){
    PHPWS_Core::initCoreClass('Image.php');
    if (!empty($this->icon))
      $this->icon = new PHPWS_Image($this->icon);
  }

  function loadChildren(){
    $db = & new PHPWS_DB('categories');
    $db->addWhere('parent', $this->id);
    $db->addOrder('title');
    $result = $db->getObjects('Category');
    if (empty($result)) {
      $this->children = NULL;
      return;
    }

    $this->children = Categories::initList($result);
  }

  function setThumbnail($thumbnail){
    $this->thumbnail = $thumbnail;
  }

  function getThumbnail(){
    return $this->thumbnail;
  }
  
  function save(){
    $db = & new PHPWS_DB('categories');

    if (isset($this->icon)) {
      $tmpIcon = $this->icon;
      $this->icon = $this->icon->getId();
    } else {
      $tmpIcon = NULL;
    }

    $result = $db->saveObject($this);
    $this->icon = $tmpIcon;
    return $result;
  }

  function kill(){
    if (empty($this->id))
      return FALSE;
    $db = & new PHPWS_DB('categories');
    $db->addWhere('id', $this->id);
    return $db->delete();
  }

  function getViewLink($module=NULL){
    if (isset($module)) {
      $vars['action']  = 'view';
      $vars['id']      = $this->id;
      $vars['ref_mod'] = $module;
      return PHPWS_Text::moduleLink($this->title, 'categories', $vars);
    } else {
      return PHPWS_Text::rerouteLink($this->title, 'categories', 'view', $this->id);
    }
  }

  function _addParent(&$list, $parent){
    $cat = & new Category($parent);
    $list[$cat->id] = $cat;
    if ($cat->parent != 0) {
      $cat->_addParent($list, $cat->parent);
    }
  }

  function getFamily(){
    $list = array();
    $list[$this->id] = $this;
    if ($this->parent != 0) {
      $this->_addParent($list, $this->parent);
    }
    $list = array_reverse($list, TRUE);
    return $list;
  }
}

?>