<?php

class Impactx_IndexController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
    $this->view->someVar = 'someVal';
  }
}
