<iframe src="<?php echo $this->osmIframeUrl; ?>" style="width: 100%; height: 450px;" scrolling="no"></iframe>

<?php if($this->showAddress === true): ?>
<div class='profile_fields clr'>
    <h4><?php echo $this->translate('Location Information') ?></h4>
    <ul>
        <li>
            <span><strong><?php echo $this->translate('Location :'); ?></strong> </span>
            <span><b><?php echo $this->location->location; ?></b></span>
        </li>
        <?php if (!empty($this->location->formatted_address)): ?>
        <li>
            <span><strong><?php echo $this->translate('Formatted Address :'); ?></strong> </span>
            <span><?php echo $this->location->formatted_address; ?> </span>
        </li>
        <?php endif; ?>
        <?php if (!empty($this->location->address)): ?>
        <li>
            <span><strong><?php echo $this->translate('Street Address :'); ?></strong> </span>
            <span><?php echo $this->location->address; ?> </span>
        </li>
        <?php endif; ?>
        <?php if (!empty($this->location->city)): ?>
        <li>
            <span><strong><?php echo $this->translate('City :'); ?></strong></span>
            <span><?php echo $this->location->city; ?> </span>
        </li>
        <?php endif; ?>
        <?php if (!empty($this->location->zipcode)): ?>
        <li>
            <span><strong><?php echo $this->translate('Zipcode :'); ?></strong></span>
            <span><?php echo $this->location->zipcode; ?> </span>
        </li>
        <?php endif; ?>
        <?php if (!empty($this->location->state)): ?>
        <li>
            <span><strong><?php echo $this->translate('State :'); ?></strong></span>
            <span><?php $this->location->state; ?></span>
        </li>
        <?php endif; ?>
        <?php if (!empty($this->location->country)): ?>
        <li>
            <span><strong><?php echo $this->translate('Country :'); ?></strong></span>
            <span><?php echo $this->location->country; ?></span>
        </li>
        <?php endif; ?>
    </ul>
</div>
<?php endif; ?>