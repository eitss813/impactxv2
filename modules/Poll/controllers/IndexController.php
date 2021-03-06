<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Poll
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: IndexController.php 10114 2013-11-05 19:53:47Z guido $
 * @author     Steve
 */

/**
 * @category   Application_Extensions
 * @package    Poll
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Poll_IndexController extends Core_Controller_Action_Standard
{
    public function init()
    {
        // Get subject
        $poll = null;
        if( null !== ($pollIdentity = $this->_getParam('poll_id')) ) {
            $poll = Engine_Api::_()->getItem('poll', $pollIdentity);
            if( null !== $poll ) {
                Engine_Api::_()->core()->setSubject($poll);
            }
        }

        // Get viewer
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        // only show polls if authorized
        $resource = ( $poll ? $poll : 'poll' );
        $viewer = ( $viewer && $viewer->getIdentity() ? $viewer : null );
        if( !$this->_helper->requireAuth()->setAuthParams($resource, $viewer, 'view')->isValid() ) {
            return;
        }
    }

    public function browseAction()
    {
        // Prepare
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('poll', null, 'create');

        // Get form
        $this->view->form = $form = new Poll_Form_Search();

        // Process form
        $values = array();
        if( $form->isValid($this->_getAllParams()) ) {
            $values = $form->getValues();
        }
        $values['browse'] = 1;

        $this->view->formValues = array_filter($values);

        if( @$values['show'] == 2 && $viewer->getIdentity() ) {
            // Get an array of friend ids
            $values['users'] = $viewer->membership()->getMembershipsOfIds();
        }
        unset($values['show']);

        // Make paginator
        $currentPageNumber = $this->_getParam('page', 1);
        $itemCountPerPage = Engine_Api::_()->getApi('settings', 'core')->getSetting('poll.perpage', 10);

        $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('poll')->getPollsPaginator($values);
        $paginator
            ->setItemCountPerPage($itemCountPerPage)
            ->setCurrentPageNumber($currentPageNumber)
        ;

        // Render
        $this->_helper->content
            //->setNoRender()
            ->setEnabled()
        ;
    }

    public function manageAction()
    {
        // Check auth
        if( !$this->_helper->requireUser()->isValid() ) {
            return;
        }
        if( !$this->_helper->requireAuth()->setAuthParams('poll', null, 'create')->isValid() ) {
            return;
        }

        // Render
        $this->_helper->content
            //->setNoRender()
            ->setEnabled()
        ;

        // Get form
        $this->view->form = $form = new Poll_Form_Search();
        $form->removeElement('show');

        // Process form
        $this->view->owner = $owner = Engine_Api::_()->user()->getViewer();
        $this->view->user_id = $owner->getIdentity();
        $values = array();
        if( $form->isValid($this->_getAllParams()) ) {
            $values = $form->getValues();
        }
        $this->view->formValues = array_filter($values);
        $values['user_id'] = $owner->getIdentity();

        // Make paginator
        $currentPageNumber = $this->_getParam('page', 1);
        $itemCountPerPage = Engine_Api::_()->getApi('settings', 'core')->getSetting('poll.perpage', 10);

        $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('poll')->getPollsPaginator($values);
        $paginator
            ->setItemCountPerPage($itemCountPerPage)
            ->setCurrentPageNumber($currentPageNumber)
        ;

        // Check create
        $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('poll', null, 'create');
    }

    public function createAction()
    {
        if( !$this->_helper->requireUser()->isValid() ) {
            return;
        }
        if( !$this->_helper->requireAuth()->setAuthParams('poll', null, 'create')->isValid() ) {
            return;
        }

        // Render
        $this->_helper->content
            //->setNoRender()
            ->setEnabled()
        ;

        $this->view->options = array();
        $this->view->maxOptions = $max_options = Engine_Api::_()->getApi('settings', 'core')->getSetting('poll.maxoptions', 15);

        $viewer = Engine_Api::_()->user()->getViewer();
        $parent_type = $this->_getParam('parent_type');
        $parent_id = $this->_getParam('parent_id', $this->_getParam('subject_id'));
        if( $parent_type == 'group' && Engine_Api::_()->hasItemType('group') ) {
            $this->view->group = $group = Engine_Api::_()->getItem('group', $parent_id);
            if( !Engine_Api::_()->authorization()->isAllowed('group', $viewer, 'poll') ) {
                return;
            }
        } else {
            $parent_type = 'user';
            $parent_id = $viewer->getIdentity();
        }
        $this->view->parent_type = $parent_type;
        $this->view->form = $form = new Poll_Form_Create(array(
            'parent_type' => $parent_type,
            'parent_id' => $parent_id
        ));

        if( !$this->getRequest()->isPost() ) {
            return;
        }

        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }
        $itemFlood = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('poll', $this->view->viewer()->level_id, 'flood');
        if(!empty($itemFlood[0])){
            //get last activity
            $tableFlood = Engine_Api::_()->getDbTable("polls",'poll');
            $select = $tableFlood->select()->where("user_id = ?",$this->view->viewer()->getIdentity())->order("creation_date DESC");
            if($itemFlood[1] == "minute"){
                $select->where("creation_date >= DATE_SUB(NOW(),INTERVAL 1 MINUTE)");
            }else if($itemFlood[1] == "day"){
                $select->where("creation_date >= DATE_SUB(NOW(),INTERVAL 1 DAY)");
            }else{
                $select->where("creation_date >= DATE_SUB(NOW(),INTERVAL 1 HOUR)");
            }
            $floodItem = $tableFlood->fetchAll($select);
            if(count($floodItem) && $itemFlood[0] <= count($floodItem)){
                $message = Engine_Api::_()->core()->floodCheckMessage($itemFlood,$this->view);
                $form->addError($message);
                return;
            }
        }
        // Check options
        $options = (array) $this->_getParam('optionsArray');
        $options = array_filter(array_map('trim', $options));
        $options = array_slice($options, 0, $max_options);
        $this->view->options = $options;
        if( empty($options) || !is_array($options) || count($options) < 2 ) {
            return $form->addError('You must provide at least two possible answers.');
        }
        foreach( $options as $index => $option ) {
            if( strlen($option) > 300 ) {
                $options[$index] = Engine_String::substr($option, 0, 300);
            }
        }

        // Process
        $pollTable = Engine_Api::_()->getItemTable('poll');
        $pollOptionsTable = Engine_Api::_()->getDbtable('options', 'poll');
        $db = $pollTable->getAdapter();
        $db->beginTransaction();

        try {
            $values = $form->getValues();
            $values['user_id'] = $viewer->getIdentity();

            if (isset($values['networks'])) {
                $network_privacy = 'network_'. implode(',network_', $values['networks']);
                $values['networks'] = implode(',', $values['networks']);
            }

            if( empty($values['auth_view']) ) {
                $values['auth_view'] = 'everyone';
            }
            if( empty($values['auth_comment']) ) {
                $values['auth_comment'] = 'everyone';
            }

            $values['view_privacy'] = $values['auth_view'];

            $values['parent_type'] = $parent_type;
            $values['parent_id'] =  $parent_id;

            // Create poll
            $poll = $pollTable->createRow();
            $poll->setFromArray($values);
            $poll->save();

            // Create options
            $censor = new Engine_Filter_Censor();
            $html = new Engine_Filter_Html(array('AllowedTags'=> array('a')));
            foreach( $options as $option ) {
                $option = $censor->filter($html->filter($option));
                $pollOptionsTable->insert(array(
                    'poll_id' => $poll->getIdentity(),
                    'poll_option' => $option,
                ));
            }

            // Privacy
            $auth = Engine_Api::_()->authorization()->context;

            if( $values['parent_type'] == 'group' ) {
                $roles = array('owner', 'member', 'parent_member', 'registered', 'everyone');
            } else {
                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            }

            $viewMax = array_search($values['auth_view'], $roles);
            $commentMax = array_search($values['auth_comment'], $roles);

            foreach( $roles as $i => $role ) {
                $auth->setAllowed($poll, $role, 'view', ($i <= $viewMax));
                $auth->setAllowed($poll, $role, 'comment', ($i <= $commentMax));
            }

            $auth->setAllowed($poll, 'registered', 'vote', true);

            $db->commit();
        } catch( Exception $e ) {
            $db->rollback();
            throw $e;
        }

        // Process activity
        $db = Engine_Api::_()->getDbTable('polls', 'poll')->getAdapter();
        $db->beginTransaction();
        try {
            $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity(Engine_Api::_()->user()->getViewer(), $poll, 'poll_new', '', array('privacy' => isset($values['networks'])? $network_privacy : null));
            if( $action ) {
                Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $poll);
            }
            $db->commit();
        } catch( Exception $e ) {
            $db->rollback();
            throw $e;
        }

        // Redirect
        return $this->_helper->redirector->gotoUrl($poll->getHref(), array('prependBase' => false));
    }
}    
