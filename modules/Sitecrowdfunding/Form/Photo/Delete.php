<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Delete.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_Photo_Delete extends Engine_Form {

    public function init() {

        $this->setTitle('Delete Photo')
                ->setDescription('Are you sure you want to delete this photo?');

        $this->addElement('Button', 'submit', array(
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper'),
            'label' => 'Delete Photo',
        ));

        $this->addElement('Cancel', 'cancel', array(
            'prependText' => ' or ',
            'label' => 'cancel',
            'link' => true,
            'href' => '',
            'onclick' => 'parent.Smoothbox.close();',
            'decorators' => array(
                'ViewHelper'
            ),
        ));

        $this->addDisplayGroup(array(
            'submit',
            'cancel'
                ), 'buttons');
    }

}
