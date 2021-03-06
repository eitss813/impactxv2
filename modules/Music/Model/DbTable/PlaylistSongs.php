<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: PlaylistSongs.php 9747 2012-07-26 02:08:08Z john $
 * @author     Steve
 */

/**
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Music_Model_DbTable_PlaylistSongs extends Engine_Db_Table
{
  protected $_name     = 'music_playlist_songs';
  //protected $_primary  = array('playlist_id', 'file_id');
  protected $_primary  = 'song_id';
  
  protected $_rowClass = 'Music_Model_PlaylistSong';
}
