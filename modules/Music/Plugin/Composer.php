<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Composer.php 9747 2012-07-26 02:08:08Z john $
 * @author     Steve
 */

/**
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Music_Plugin_Composer extends Core_Plugin_Abstract
{
  public function onAttachMusic($data)
  {
    if( !is_array($data) || empty($data['song_id']) ) 
      return;

    $song = Engine_Api::_()->getItem('music_playlist_song', $data['song_id']);
    if( !($song instanceof Core_Model_Item_Abstract) || !$song->getIdentity() )
      return;
    
    return $song;
  }
}
