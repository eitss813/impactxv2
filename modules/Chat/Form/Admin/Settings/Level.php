<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Chat
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Level.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Chat
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @author     John
 */
class Chat_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract
{
  public function init()
  {
    parent::init();

    // My stuff
    $this
      ->setTitle('Member Level Settings')
      ->setDescription('CHAT_FORM_ADMIN_SETTINGS_LEVEL_DESCRIPTION')
      ;

    if( !$this->isPublic() ) {

      // Element: chat
      $this->addElement('Radio', 'chat', array(
        'label' => 'Enable chat?',
        'description' => 'Do you want to let users chat in the chat room?',
        'multiOptions' => array(
          '1' => 'Yes, enable chat.',
          '0' => 'No, do not enable chat.',
        ),
      ));

      // Element: im
      $this->addElement('Radio', 'im', array(
        'label' => 'Enable IM?',
        'description' => 'Do you want to let users have private conversations (IM)?',
        'multiOptions' => array(
          '1' => 'Yes, enable IM.',
          '0' => 'No, do not enable IM.',
        ),
      ));

    }
  }
}
