<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Widget_ProjectLocationController extends Seaocore_Content_Widget_Abstract
{

    public function indexAction()
    {

        //DONT RENDER IF NOT AUTHORIZED
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            return $this->setNoRender();
        }

        //GET SUBJECT
        $this->view->project = $project = Engine_Api::_()->core()->getSubject('sitecrowdfunding_project');

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.location', 1)) {
            return $this->setNoRender();
        }
        
        // Check about the organization map view
        $project_map = !empty($project->project_map)? $project->project_map: 0;
        if( !empty($project_map) && ($project_map > 1) ){
            if( $project_map == 3 )
                return $this->setNoRender();
        }else if( !empty($project->project_id) ) {
            $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project->project_id);
            if( isset($parentOrganization['page_id']) && !empty($parentOrganization['page_id']) ) {
                $sitepage = Engine_Api::_()->getItem('sitepage_page', $parentOrganization['page_id']);
                if( !empty($sitepage->organization_map) )
                    return $this->setNoRender();
            }
        }

        $params = $this->_getAllParams();
        $showAddress = true;
        if ($params && $params['hideAddress'] === true) {
            $showAddress = false;
        }
        $this->view->showAddress = $showAddress;
        //GET LOCATION
        $value['id'] = $project->getIdentity();

        $this->view->location = $location = Engine_Api::_()->getDbtable('locations', 'sitecrowdfunding')->getLocation($value);

        // get lat and log for followers/member
        $this->view->memberList = Engine_Api::_()->getDbtable('memberships', 'sitecrowdfunding')->listJoinedMemberLocation($project->getIdentity());
        $this->view->followerList = Engine_Api::_()->getApi('favourite', 'seaocore')->favouritePeopleLocation($project->getType(), $project->getIdentity());
        $this->view->adminList = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->getProjectLeadersLocation($project->getIdentity());


    }

}
