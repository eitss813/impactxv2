<?php/** * socialnetworking.solutions * * @category   Application_Modules * @package    Sesblog * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd. * @license    https://socialnetworking.solutions/license/ * @version    $Id: index.tpl 2016-07-23 00:00:00 socialnetworking.solutions $ * @author     socialnetworking.solutions */?><div id='profile_options' class="sesblog-profile_option_review sesblog_profile_options_<?php echo $this->viewType; ?>">  <?php echo $this->navigation()->menu()->setContainer($this->navigation)->setPartial(array('_navIcons.tpl', 'core'))->render(); ?></div>