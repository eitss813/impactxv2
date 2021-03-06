<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: User.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class User_View_Helper_Friends extends Zend_View_Helper_Abstract
{
  public function friends($includeSelf = false)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer->getIdentity() ) {
      $data = null;
    } else {
      $data = array();
      $table = Engine_Api::_()->getItemTable('user');
      $select = Engine_Api::_()->getDbtable('users', 'user')->select()->where('user_id <> ?', $viewer->user_id);
      $blockedUserIds = !$viewer->isAdmin() ? $viewer->getAllBlockedUserIds() : array();
      if( $blockedUserIds ) {
        $select->where('user_id NOT IN(?)', (array) $blockedUserIds);
      }
      $select->limit(20);
      /* A user should be able to mention self using @ in Feed and comments #1602 */
      if($includeSelf){
        $data[] = array(
          'type'  => 'user',
          'id'    => $viewer->getIdentity(),
          'guid'  => $viewer->getGuid(),
          'label' => $viewer->getTitle(). ' (you)',
          'photo' => $this->view->itemPhoto($viewer, 'thumb.icon'),
          'url'   => $viewer->getHref(),
        );
      }

      $ids = array();
      foreach( $select->getTable()->fetchAll($select) as $friend ) {
        if(!$friend->authorization()->isAllowed($viewer, 'mention') && !$viewer->isAdmin())
          continue;
        $data[] = array(
          'type'  => 'user',
          'id'    => $friend->getIdentity(),
          'guid'  => $friend->getGuid(),
          'label' => $friend->getTitle(),
          'photo' => $this->view->itemPhoto($friend, 'thumb.icon'),
          'url'   => $friend->getHref(),
        );
        $ids[] = $friend->getIdentity();
        $friend_data[$friend->getIdentity()] = $friend->getTitle();
      }

      // first get friend lists created by the user
      $listTable = Engine_Api::_()->getItemTable('user_list');
      $lists = $listTable->fetchAll($listTable->select()->where('owner_id = ?', $viewer->getIdentity()));
      $listIds = array();
      foreach( $lists as $list ) {
        $listIds[] = $list->list_id;
        $listArray[$list->list_id] = $list->title;
      }

      // check if user has friend lists
      if( $listIds ) {
        // get list of friend list + friends in the list
        $listItemTable = Engine_Api::_()->getItemTable('user_list_item');
        $uName = Engine_Api::_()->getDbtable('users', 'user')->info('name');
        $iName  = $listItemTable->info('name');

        $listItemSelect = $listItemTable->select()
          ->setIntegrityCheck(false)
          ->from($iName, array($iName.'.listitem_id', $iName.'.list_id', $iName.'.child_id',$uName.'.displayname'))
          ->joinLeft($uName, "$iName.child_id = $uName.user_id")
          //->group("$iName.child_id")
          ->where('list_id IN(?)', $listIds);

        $listItems = $listItemTable->fetchAll($listItemSelect);

        $listsByUser = array();
        foreach( $listItems as $listItem ) {
          $listsByUser[$listItem->list_id][$listItem->user_id]= $listItem->displayname ;
        }
        
        foreach ($listArray as $key => $value){
          if (!empty($listsByUser[$key])){
            $data[] = array(
              'type' => 'list',
              'friends' => $listsByUser[$key],
              'label' => $value,
            );
          }
        }
      }
    }
    return $data;
  }
}
