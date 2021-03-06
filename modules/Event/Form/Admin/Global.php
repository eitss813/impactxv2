<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Global.php 9802 2012-10-20 16:56:13Z pamela $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Event_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.');

    $this->addElement('Text', 'event_page', array(
      'label' => 'Events Per Page',
      'description' => 'How many events will be shown per page?',
      'value' => 12,
    ));
    
   // Create Elements
    $bbcode = new Engine_Form_Element_Radio('bbcode');
    $bbcode
      ->addMultiOptions(array(
        1 => 'Yes, members can use BBCode tags.',
        0 => 'No, do not let members use BBCode.'
      ));
    $bbcode->setValue(1);
    $bbcode->setLabel("Enable BBCode");

    $html = new Engine_Form_Element_Radio('html');

    $html
      ->addMultiOptions(array(
        1 => 'Yes, members can use HTML in their posts.',
        0 => 'No, strip HTML from posts.'
      ));
    $html->setValue(0);
    $html->setLabel("Enable HTML");

    // Add elements
    $this->addElements(array(
      $bbcode,
      $html
    ));
      $this->addElement('Radio', 'event_allow_unauthorized', array(
          'label' => 'Make unauthorized events searchable?',
          'description' => 'Do you want to make a unauthorized events searchable? (If set to no, events that are not authorized for the current user will not be displayed in the event search results and widgets.)',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('event.allow.unauthorized',0),
          'multiOptions' => array(
              '1' => 'Yes',
              '0' => 'No',
          ),
      ));
    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}
