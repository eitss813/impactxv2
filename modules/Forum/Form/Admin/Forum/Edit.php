<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Edit.php 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Forum_Form_Admin_Forum_Edit extends Forum_Form_Forum_Edit
{
  public function init()
  {
    parent::init();

    // Element: levels
    $levels = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll();
    $multiOptions = array();
    foreach( $levels as $level ) {
      $multiOptions[$level->getIdentity()] = $level->getTitle();
    }
    reset($multiOptions);
    $this->addElement('Multiselect', 'levels', array(
      'label' => 'Member Levels',
      'order' => 5,
      'multiOptions' => $multiOptions,
      'value' => array_keys($multiOptions),
      'required' => true,
      'allowEmpty' => false,
    ));
  }
}
