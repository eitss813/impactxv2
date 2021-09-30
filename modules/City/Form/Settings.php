<?php

class City_Form_Settings extends Engine_Form {

    public function init() {
        $this->setTitle('City Example Form - Title')
                ->setDescription('City Example Form - Description');
        
        $this->addElement('Text', 'title', array(
            'label' => 'Title',
            'allowEmpty' => false,
            'required' => true,
            'maxlength' => '63'
        ));
        
        $this->addElement('Text', 'description', array(
            'label' => 'Description',
            'allowEmpty' => false,
            'required' => true,
            'maxlength' => '63'
        ));
    }

}
