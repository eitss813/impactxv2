<?php

class Impactx_Widget_OsmController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        
        $this->view->followerList = $followerList = array();
        $this->view->projectsList = $projectsList = array();
        $this->view->membersList = $membersList = array();

        //GET SUBJECT
        $subject = Engine_Api::_()->core()->getSubject();
        $subjectType = $subject->getType();
        
        if( $subjectType == 'sitepage_page' ) {
            $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
        }else if( $subjectType == 'sitecrowdfunding_project' ) {
            return $this->setNoRender();
//            $resource_id = $subject->project_id;
//            $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($resource_id);
//            if (empty($parentOrganization)) {
//                $parentOrganization = Engine_Api::_()->impactx()->getParentOrganization($resource_id);
//            }
//            
//            if( isset($parentOrganization['page_id']) && !empty($parentOrganization['page_id']) )
//                $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $parentOrganization['page_id']);
        }else {
            return $this->setNoRender();
        }

        // Check about the organization map view
        if( empty($sitepage->organization_map) )
            return $this->setNoRender();

        $value['id'] = $sitepage->getIdentity();
        $this->view->location = $location = Engine_Api::_()->getDbtable('locations', 'sitepage')->getLocation($value);

        // get site-page project
        $page_id = $sitepage->page_id;
        $projectParams['page_id'] = $page_id;
        $this->view->projectsList = $projectsList = Engine_Api::_()->getDbTable('locations', 'sitecrowdfunding')->getProjectsLocation($projectParams);

        // get site-page followers
        $followParams['page_id'] = $page_id;
        $this->view->followerList = $followerList = Engine_Api::_()->getDbTable('locations', 'user')->getOrganisationUsersLocations($followParams);

        // get site-page members
        $this->view->membersList = $membersList = Engine_Api::_()->getDbtable('membership', 'sitepage')->getJoinMemberLocation($sitepage->page_id);

        // get site-page partner org
        $this->view->partnerPages = $partnerPages = Engine_Api::_()->getDbtable('locations', 'sitepage')->getPartnerLocationsByPageId($sitepage->page_id);

        // get manage admins
        $manageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');
        $this->view->manageadmins = $manageadminsTable->getManageAdminUserLocation($page_id);

        if (empty($location) && count($projectsList) < 0 && count($followerList) < 0 && count($membersList) < 0 && count($partnerPages) < 0  ) {
            return $this->setNoRender();
        }
        
        if( empty($page_id) )
            return $this->setNoRender();
        
        $baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $filename = APPLICATION_PATH . '/openstreetmap/kml/' . $page_id . '.kml';
        
//        if(!is_file($file)) {
//            
//        }
        
        $content = '<?xml version="1.0" encoding="UTF-8"?><kml xmlns="http://www.opengis.net/kml/2.2" xmlns:gx="http://www.google.com/kml/ext/2.2" xmlns:kml="http://www.opengis.net/kml/2.2" xmlns:atom="http://www.w3.org/2005/Atom"><Document>';
        $content .= '<name>OpenStreetMap</name>';
        $content .= '<Style id="black_pin">
            <IconStyle>
                <scale>1</scale>
                <Icon>
                    <href>'.$baseurl.'/openstreetmap/images/mic_black_goal_10.png</href>
                </Icon>
                <hotSpot x="20" y="2" xunits="pixels" yunits="pixels"/>
            </IconStyle>
        </Style>        
        <Style id="green_pin">
            <IconStyle>
                <scale>1</scale>
                <Icon>
                    <href>'.$baseurl.'/openstreetmap/images/mic_black_goal_10.png</href>
                </Icon>
                <hotSpot x="20" y="2" xunits="pixels" yunits="pixels"/>
            </IconStyle>
        </Style>';
        
        
        foreach( $projectsList as $project ) {
            $project_obj = Engine_Api::_()->getItem('sitecrowdfunding_project', $project->project_id);
            $title = $project_obj->getTitle();
            
            $projectData = $this->view->partial('application/modules/Impactx/views/scripts/_mapProjectInfoWindowContent.tpl', array(
                                                'project_id' => $project->project_id,
                                                'location_id' => $project->location_id,
                                        ));
            
            if( strstr($projectData, "data-safe-src=") ) {
                $projectData = str_replace(' src=', ' temp-src=', $projectData);
                $projectData = str_replace('data-safe-src=', 'src=', $projectData);
            }
            
            $projectData = str_replace('<a', '<a target="_parent"', $projectData);
            
            $content .= '        <Placemark>
            <name><![CDATA[
<div style="font-family: Roboto,sans-serif; font-weight: bold;">'.$title.'</div>
]]></name>
            <description><![CDATA[
            '.$projectData.'
]]></description>
            <styleUrl>#black_pin</styleUrl>
            <Point>
                <coordinates>'.$project->longitude.', '.$project->latitude.', 0</coordinates>
            </Point>
        </Placemark>';
        }
        
        
        $content .= '</Document></kml>';
        
        $file = fopen($filename,"w+");
        fwrite($file, $content);
        fclose($file);
        chmod($file,0777);
        
        $this->view->osmIframeUrl = $this->view->baseUrl() . '/openstreetmap/map.php?kmlFilePath=kml/' . $page_id . '.kml';
    }

}

?>