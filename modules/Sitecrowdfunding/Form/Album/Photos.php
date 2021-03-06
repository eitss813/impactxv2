<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Photos.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_Album_Photos extends Engine_Form {

    public function init() {

        $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
        $this->addElement('Radio', 'cover', array(
            'label' => 'Album Cover',
        ));

        $this->addElement('Button', 'submit', array(
            'label' => 'Save Photos',
            'type' => 'submit',
        ));
    }

}
