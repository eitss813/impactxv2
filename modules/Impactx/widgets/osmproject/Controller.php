<?php

class Impactx_Widget_OsmprojectController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

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
            if( $project_map == 2 )
                return $this->setNoRender();
        }else if( !empty($project->project_id) ) {
            $parentOrganization = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding')->getParentPages($project->project_id);
            if( isset($parentOrganization['page_id']) && !empty($parentOrganization['page_id']) ) {
                $sitepage = Engine_Api::_()->getItem('sitepage_page', $parentOrganization['page_id']);
                if( empty($sitepage->organization_map) )
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
        $project_id = $value['id'] = $project->getIdentity();

        $this->view->location = $location = Engine_Api::_()->getDbtable('locations', 'sitecrowdfunding')->getLocation($value);

        // get lat and log for followers/member
        $this->view->memberList = Engine_Api::_()->getDbtable('memberships', 'sitecrowdfunding')->listJoinedMemberLocation($project->getIdentity());
        $this->view->followerList = Engine_Api::_()->getApi('favourite', 'seaocore')->favouritePeopleLocation($project->getType(), $project->getIdentity());
        $this->view->adminList = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->getProjectLeadersLocation($project->getIdentity());


        $baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $filename = APPLICATION_PATH . '/openstreetmap/kml/projects/' . $project_id . '.kml';
        
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
        
            $project_obj = Engine_Api::_()->getItem('sitecrowdfunding_project', $location->project_id);
            $title = $project_obj->getTitle();

            $projectData = $this->view->partial('application/modules/Sitecrowdfunding/views/scripts/_mapProjectInfoWindowContent.tpl', array(
                    'project_id' =>  $location->project_id,
                    'location_id' => $location->location_id,
                ));

//            $projectData = $this->view->partial('application/modules/Impactx/views/scripts/_mapProjectInfoWindowContent.tpl', array(
//                                                'project_id' => $location->project_id,
//                                                'location_id' => $location->location_id,
//                                        ));

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
                <coordinates>'.$location->longitude.', '.$location->latitude.', 0</coordinates>
            </Point>
        </Placemark>';
//        }
        
        
        $content .= '</Document></kml>';
        
        $file = fopen($filename,"w+");
        fwrite($file, $content);
        fclose($file);
        chmod($file,0777);
        
        $this->view->osmIframeUrl = $this->view->baseUrl() . '/openstreetmap/map.php?kmlFilePath=kml/projects/' . $project_id . '.kml';
    }

}

?>