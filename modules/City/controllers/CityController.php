<?php

class City_CityController extends Core_Controller_Action_Standard
{
  public function mycityAction()
  {
      echo 'QuestWalk Technologies Pvt Ltd';
      
      $mycity = 'Noida';
      
      $this->view->mycity = $mycity;
      
      $this->view->form = new City_Form_Settings();
      
  }
}
