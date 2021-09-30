<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Invite
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Invite.php 10180 2014-04-28 21:02:01Z lucas $
 * @author     Steve
 */

/**
 * @category   Application_Extensions
 * @package    Invite
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitecrowdfunding_Form_Project_InviteExternal extends Engine_Form
{
    public $invalid_emails = array();

    public $already_members = array();

    public $emails_sent = 0;

    public function init()
    {
        // Init settings object
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $translate = Zend_Registry::get('Zend_Translate');

        // Init form
        $this
            ->setTitle('Invite Your Friends')
            ->setAttrib('id', 'invite_external')
            ->setLegend('');

        // Init recipients
        $this->addElement('Text', 'recipients', array(
            'label' => 'Recipient Mail Address',
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(
                new Engine_Validate_Callback(array($this, 'validateEmails')),
            ),
        ));
        // Init recipients
        $this->addElement('Text', 'recipient_name', array(
            'label' => 'Recipient Name',
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(
            ),
        ));

        $this->recipients->getValidator('Engine_Validate_Callback')->setMessage('Please enter only valid email addresses.');
//        $this->recipients->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));

        // Init custom message
        if( $settings->getSetting('invite.allowCustomMessage', 1) > 0 ) {
            $this->addElement('Textarea', 'message', array(
                'label' => 'Custom Message',
                'required' => false,
                'allowEmpty' => true,
                //'description' => 'Use %invite_url% to add a link to our sign up page.',
                'value' => $translate->_($settings->getSetting('invite.message', '')),
                'filters' => array(
                    new Engine_Filter_Censor(),
                )
            ));
            $this->message->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));
        }

        $this->addElement('Checkbox', 'friendship', array(
            'label' => "Send a friend request if the user(s) join(s) the network",
            //'attribs' => array ('style' => 'margin-left: 167px;margin-bottom: 10px;'),
            'decorators' => array(
                'ViewHelper',
                array('Label', array('placement' => 'APPEND')),
            ),
        ));

        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id');
        $roles = Engine_Api::_()->getDbtable('roles', 'sitecrowdfunding')->getRolesAssoc($project_id);
        if (!empty($roles)) {
            $roleKey = array();
            foreach ($roles as $k => $role) {
                $role_name[$k] = $role;
                $roleKey[] = $k;
            }
            reset($role_name);

            $this->addElement('Multiselect', 'role_id', array(
                'label' => 'ROLE',
                'multiOptions' => $role_name,
                'value' => $roleKey,
            ));
        }
        $this->addElement('hidden', 'project_id');

        // Init captcha
        if( $settings->core_spam_invite ) {
            $this->addElement('captcha', 'captcha', Engine_Api::_()->core()->getCaptchaOptions());
        }

        // Init submit
        $this->addElement('button', 'submit', array(
            'type' => 'submit',
            'label' => 'Send Invites',
        ));
    }

    public function validateEmails($value)
    {
        // Not string?
        if( !is_string($value) || empty($value) ) {
            return false;
        }

        // Validate emails
        $validate = new Zend_Validate_EmailAddress();
        $validate->getHostnameValidator()->setValidateTld(false);

        $emails = array_unique(array_filter(array_map('trim', preg_split("/[\s,]+/", $value))));

        if( empty($emails) ) {
            return false;
        }

        foreach( $emails as $email ) {
            if( !$validate->isValid($email) ) {
                return false;
            }
        }

        return true;
    }
}
