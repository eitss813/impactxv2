<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
  include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/Adintegration.tpl';
?>
<?php 
	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitepagevideo/externals/styles/style_sitepagevideo.css')
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>
<div class="sitepage_viewpages_head">
  <?php echo $this->htmlLink($this->sitepage->getHref(), $this->itemPhoto($this->sitepage, 'thumb.icon', '', array('align' => 'left'))) ?>
  <?php if(!empty($this->can_edit)):?>
    <div class="fright">
			<a href='<?php echo $this->url(array('page_id' => $this->sitepage->page_id), 'sitepage_edit', true) ?>' class='buttonlink icon_sitepages_dashboard'><?php echo $this->translate('Dashboard');?></a>
    </div>
	<?php endif;?>
  <h2>	
    <?php echo $this->sitepage->__toString() ?>	
    <?php echo $this->translate('&raquo; '); ?>
    <?php echo $this->htmlLink($this->sitepage->getHref(array('tab'=>$this->tab_selected_id)), $this->translate('Videos')) ?>

  </h2>
</div>
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>
<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.advideocreate', 3) && $page_communityad_integration && Engine_Api::_()->sitepage()->showAdWithPackage($this->sitepage)): ?>
  <div class="layout_right" id="communityad_videocreate">

<?php
			echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.advideocreate', 3),"loaded_by_ajax"=>0,'widgetId'=>"page_videocreate")); 			 
		?>
  </div>
<?php endif; ?>
<div class="layout_middle">

  <script type="text/javascript">
    var current_code;
    var ignoreValidation = function(){
      $('upload-wrapper').style.display = "block";
      $('validation').style.display = "none";
      $('code').value = current_code;
      $('ignore').value = true;
    }
    var updateTextFields = function()
    {
      var video_element = document.getElementById("type");
      var url_element = document.getElementById("url-wrapper");
      var file_element = document.getElementById("file-wrapper");
      var submit_element = document.getElementById("upload-wrapper");

      // clear url if input field on change
      //$('code').value = "";
      $('upload-wrapper').style.display = "none";

      // If video source is empty
      if (video_element.value == 0)
      {
        $('url').value = "";
        file_element.style.display = "none";
        url_element.style.display = "none";
        return;
      }

      if ($('code').value && $('url').value)
      {
        $('type-wrapper').style.display = "none";
        file_element.style.display = "none";
        $('upload-wrapper').style.display = "block";
        return;
      }

      // If video source is youtube or vimeo
      if (video_element.value == 1 || video_element.value == 2)
      {
        $('url').value = "";
        $('code').value = "";
        file_element.style.display = "none";
        url_element.style.display = "block";
        return;
      }

      // If video source is from computer
      if (video_element.value == 3)
      {
        $('url').value = "";
        $('code').value = "";
        file_element.style.display = "block";
        url_element.style.display = "none";
        return;
      }

      // if there is video_id that means this form is returned from uploading because some other required field
      if ($('id').value)
      {
        $('type-wrapper').style.display = "none";
        file_element.style.display = "none";
        $('upload-wrapper').style.display = "block";
        return;
      }

    }
    var video = {
      active : false,

      debug : false,

      currentUrl : null,

      currentTitle : null,

      currentDescription : null,

      currentImage : 0,

      currentImageSrc : null,

      imagesLoading : 0,

      images : [],

      maxAspect : (10 / 3), //(5 / 2), //3.1,

      minAspect : (3 / 10), //(2 / 5), //(1 / 3.1),

      minSize : 50,

      maxPixels : 500000,

      monitorInterval: null,

      monitorLastActivity : false,

      monitorDelay : 500,

      maxImageLoading : 5000,
    
      attach : function()
      {
        var bind = this;
        $('url').addEvent('keyup', function()
        {
          bind.monitorLastActivity = (new Date).valueOf();
        });

        var url_element = document.getElementById("url-element");
        var myElement = new Element("p");
        myElement.innerHTML = "test";
        myElement.addClass("description");
        myElement.id = "validation";
        myElement.style.display = "none";
        url_element.appendChild(myElement);

        var body = $('url');
        var lastBody = '';
        var lastMatch = '';
        var video_element = $('type');
        (function()
        {
          // Ignore if no change or url matches
          if( body.value == lastBody || bind.currentUrl )
          {
            return;
          }

          // Ignore if delay not met yet
          if( (new Date).valueOf() < bind.monitorLastActivity + bind.monitorDelay )
          {
            return;
          }

          // Check for link
          var m = body.value.match(/https?:\/\/([-\w\.]+)+(:\d+)?(\/([-#:\w/_\.]*(\?\S+)?)?)?/);
          if( $type(m) && $type(m[0]) && lastMatch != m[0] )
          {
            if (video_element.value == 1){
              video.youtube(body.value);
            }
            else video.vimeo(body.value);
          }
          else{
         
          }

          lastBody = body.value;
        }).periodical(250);
      },

      youtube : function(url){
        // extract v from url
        var myURI = new URI(url);
        var youtube_code = myURI.get('data')['v'];
         if( youtube_code === undefined ) {
          youtube_code = myURI.get('file');
        }

        if (youtube_code){
          (new Request.HTML({
            'format': 'html',
            'url' : '<?php echo $this->url(array('module' => 'sitepagevideo', 'controller' => 'index', 'action' => 'validation'), 'default', true) ?>',
            'data' : {
              'ajax' : true,
              'code' : youtube_code,
              'type' : 'youtube'
            },
            'onRequest' : function(){
              $('validation').style.display = "block";
              $('validation').innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate('Checking URL...')); ?>';
              $('upload-wrapper').style.display = "none";
            },
            'onSuccess' : function(responseTree, responseElements, responseHTML, responseJavaScript)
            {
              if (valid){
                $('upload-wrapper').style.display = "block";
                $('validation').style.display = "none";
                $('code').value = youtube_code;
              }
              else{
                $('upload-wrapper').style.display = "none";
                current_code = youtube_code;
                $('validation').innerHTML = '<?php echo addslashes($this->translate("We could not find a video there - please check the URL and try again. If you are sure that the URL is valid, please click %s to continue.", $this->imageUpload)); ?>';
              }
            }
          })).send();
        }
      },

      vimeo :function(url){
        var myURI = new URI(url);
        var vimeo_code = myURI.get('file');
        if (vimeo_code.length > 0){
          (new Request.HTML({
            'format': 'html',
            'url' : '<?php echo $this->url(array('module' => 'sitepagevideo', 'controller' => 'index', 'action' => 'validation'), 'default', true) ?>',
            'data' : {
              'ajax' : true,
              'code' : vimeo_code,
              'type' : 'vimeo'
            },
            'onRequest' : function(){
              $('validation').style.display = "block";
              $('validation').innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate('Checking URL...')); ?>';
              $('upload-wrapper').style.display = "none";
            },
            'onSuccess' : function(responseTree, responseElements, responseHTML, responseJavaScript)
            {
              if (valid){
                $('upload-wrapper').style.display = "block";
                $('validation').style.display = "none";
                $('code').value = vimeo_code;
              }
              else{
                $('upload-wrapper').style.display = "none";
                current_code = vimeo_code;
                $('validation').innerHTML = "<?php echo $this->translate("We could not find a video there - please check the URL and try again. If you are sure that the URL is valid, please click %s to continue.", $this->imageUpload); ?>";
              }
            }
          })).send();
        }
      }
    }

    en4.core.runonce.add(updateTextFields);
    en4.core.runonce.add(video.attach);
    en4.core.runonce.add(function()
    {
     cN('tags', '<?php echo $this->url(array('controller' => 'tag', 'action' => 'suggest'), 'default', true) ?>', {
        'postVar' : 'text',
        'minLength': 1,
        'selectMode': 'pick',
        'autocompleteType': 'tag',
        'className': 'tag-autosuggest',
        'customChoices' : true,
        'filterSubset' : true,
        'multiple' : true,
        'injectChoice': function(token){
          var choice = new Element('li', {'class': 'autocompleter-choices', 'value':token.label, 'id':token.id});
          new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice'}).inject(choice);
          choice.inputValue = token;
          this.addChoiceEvents(choice).inject(this.choices);
          choice.store('autocompleteChoice', token);
        }
      });
    });
  </script>

  <?php echo $this->form->render($this); ?>
</div>
