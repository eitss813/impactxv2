<?php/** * socialnetworking.solutions * * @category   Application_Modules * @package    Sesblog * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd. * @license    https://socialnetworking.solutions/license/ * @version    $Id: index.tpl 2016-07-23 00:00:00 socialnetworking.solutions $ * @author     socialnetworking.solutions */?><?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/scripts/core.js'); ?> <div class="sesbasic_cloud_widget sesbasic_clearfix">  <div class="sesblog_tags_cloud_blog sesbasic_bxs ">    <ul class="sesblog_tags_cloud_list">      <?php foreach($this->paginator as $valueTags):?>        <?php if($valueTags['text'] == '' || empty($valueTags['text'] )) continue; ?>        <li><a href="<?php echo $this->url(array('module' =>'sesblog','controller' => 'index', 'action' => 'browse'),'sesblog_general',true).'?tag_id='.$valueTags['tag_id'].'&tag_name='.$valueTags['text']  ;?>"><?php echo $valueTags['text'] ?></a></li>      <?php endforeach;?>    </ul>  </div></div>