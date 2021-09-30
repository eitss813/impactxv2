<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: FieldBirthdate.php 10038 2013-04-11 22:01:49Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @author     John
 */
class Siteapi_View_Helper_Fields_FieldBirthdate extends Siteapi_View_Helper_Fields_FieldAbstract
{
  public function fieldBirthdate($subject, $field, $value, $view)
  {
    if( empty($value) || empty($value->value) ) {
      return false;
    }
    
    $label = date('F j,Y', strtotime($value->value));
    $parts = ($value->value.strstr('/') !== false) ? @explode('-', $value->value) : @explode('-', $value->value);

    // Error if not filled out
    if( count($parts) < 3 || count(array_filter($parts)) < 3 ) {
      return false;
    }

    $value = mktime(0, 0, 0, $parts[1], $parts[2], $parts[0]);

    // Error if too low
    $date = new Zend_Date($value);
    $age = (int)(- $date->sub(time())  / 365 / 86400);


    return $this->encloseInLink($subject, $field, $age, $label, true);
  }
}