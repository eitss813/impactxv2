<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $user = $this->user; ?>
<?php $backer = $this->backer; ?>
<?php $reward = $this->reward; ?>

<div class="global_form_popup">
<div class="sitecrowdfunding_popup">
<div class="backers-report-view">
    <div class="backers-report-view-img">
        <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.profile'), array('class' => 'item_photo', 'title' => $this->translate($user->getTitle()), 'target' => '_parent')); ?>
    </div>
    <div class="backers-report-view-details">
        <div class="backers-report-view-row">
            <h3 class="backers-report-view-title"><?php echo $this->htmlLink($user->getHref(), $this->translate(" %s ", $this->translate($user->getTitle()))); ?></h3>

            <span class="backers-report-view-id">
                <strong><?php echo $this->translate("Backer ID : "); ?>&nbsp;&nbsp;</strong># <?php echo $backer->backer_id; ?>
            </span> 
        </div>
        <div class="backers-report-view-second-row">
            <strong><?php echo $this->translate("Backing Date : "); ?>&nbsp;&nbsp;</strong>
            <?php echo $this->translate($this->locale()->toDateTime($backer->creation_date)) ?>
            <?php $fundedAmount = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($backer->amount); ?>
        </div>
        <div class="backers-report-view-pledged">
            <strong><?php echo $this->translate("Backed Amount : "); ?>&nbsp;&nbsp;</strong>
            <?php echo $fundedAmount; ?>
            <?php if ($this->shipping_included): ?>
                <?php echo $this->translate("(Shipping Cost Included)"); ?>
            <?php endif; ?>         
        </div>  
        <div class="backers-report-view-third-row">
            <?php if (!empty($reward)): ?>
                <div class="mtop10">
                    <strong><?php echo $this->translate("Reward Selected : "); ?>&nbsp;&nbsp;</strong>
                    <?php echo $this->translate($reward->getTitle()) ?>
                    <div class="">
                        <?php echo $this->string()->truncate($this->string()->stripTags($reward->getDescription()), 50); ?>
                    </div>
                </div>
                <div class="mtop10">
                    <?php if (!empty($backer->shipping_address1) || !empty($backer->shipping_address2) || !empty($backer->shipping_city) || !empty($backer->shipping_zip)): ?>
                        <strong><?php echo $this->translate("Shipping Location :"); ?>&nbsp;&nbsp;</strong>
                        <?php echo ($backer->shipping_address1) ? $this->translate($backer->shipping_address1) : ''; ?></br>
                        <?php echo ($backer->shipping_address2) ?  $this->translate($backer->shipping_address2): ''; ?>
                        <?php echo ($backer->shipping_city) ?  $this->translate($backer->shipping_city): ''; ?>
                        <?php echo ($backer->shipping_zip) ?  $this->translate($backer->shipping_zip): ''; ?>
                    <?php endif; ?>
                </div>
                <?php if ($backer->shipping_country): ?>
                    <div class="mtop10">
                        <strong><?php echo $this->translate("Shipping Country :"); ?>&nbsp;&nbsp;</strong>
                        <?php $region = Engine_Api::_()->getItem('sitecrowdfunding_region', $backer->shipping_country); ?>
                        <?php echo $this->translate($region->country_name); ?>
                    </div>
                <?php endif; ?>
                <div class="mtop10">
                    <?php if (isset($reward->reward_status) && $reward->reward_status): ?>
                        <?php echo $this->translate("Reward Sent: "); ?> 
                    <?php else: ?>
                        <strong><?php echo $this->translate("Estimated Delivery : "); ?>&nbsp;&nbsp;</strong>
                        <?php echo date('F Y', strtotime($reward->delivery_date)); ?>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <?php echo $this->translate("No Reward Selected"); ?>
            <?php endif; ?>
        </div>
    </div>
    <!--<button onclick='javascript:parent.Smoothbox.close()' style="float:right;"><?php echo 'Close'; ?></button>-->
</div>
<a style="position: fixed;" href="javascript:void(0);" onclick="javascript:parent.Smoothbox.close();" class="popup_close fright"></a>
<?php if (@$this->closeSmoothbox): ?>
    <script type="text/javascript">
        TB_close();
    </script>
<?php endif; ?> 
</div>
</div>
