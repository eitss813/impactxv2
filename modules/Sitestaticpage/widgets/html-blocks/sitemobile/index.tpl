<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestaticpage
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2014-02-16 5:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
	  /* Include the common user-end field switching javascript */
	  echo $this->partial('_jsSwitch.tpl', 'fields', array(
	      'topLevelId' => (int) @$this->topLevelId,
	      'topLevelValue' => (int) @$this->topLevelValue
	    ))
	?>
<?php foreach($this->form_ids as $form_id):?>
<?php if($this->success && ($form_id == $this->profile_id)):?>
<div class="tip"><span><?php echo $this->translate('Thank you, your form has been submitted successfully.');?></span></div>
  <?php endif;?>
  
   <?php  if(!empty($this->error_message) && ($form_id == $this->profile_id)):?>
<div class="tip"><span><?php echo $this->translate('Your form can not be saved. Please contact Site Administrator for this issue.');?></span></div>
<br />
<?php endif; ?>

<?php if(!empty($this->fields_error_message) && ($form_id == $this->profile_id)):?>
<div class="tip"><span><?php echo $this->translate('Your form contained errors and could not be submitted. Please fill all the required fields.');?></span></div>
<br />
<?php endif; ?>
<?php endforeach; ?>
