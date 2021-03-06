<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formSubcategory.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$defaultProfileFieldId = Engine_Api::_()->getDbTable('metas', 'sitecrowdfunding')->defaultProfileId();
$defaultProfileFieldId = "0_0_$defaultProfileFieldId";
$project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', 0);
$profile_type = 0;
if (!empty($project_id)) {
    $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
    $profile_type = Engine_Api::_()->getDbTable('categories', 'sitecrowdfunding')->getProfileType(null, $project->category_id, 'profile_type');
}

$cateDependencyArray = Engine_Api::_()->getDbTable('categories', 'sitecrowdfunding')->getCatDependancyArray();
?>

<?php
echo "
	<div id='subcategory_backgroundimage' class='form-wrapper'></div>
	<div id='subcategory_id-wrapper' class='form-wrapper' style='display:none;'>
		<div id='subcategory_id-label' class='form-label'>
		 	<label for='subcategory_id' class='optional'>" . $this->translate('Subcategory') . "</label>
		</div>
		<div id='subcategory_id-element' class='form-element'>
			<select name='subcategory_id' id='subcategory_id' onchange='showFields($(this).value, 2); changesubcategory(this.value);'>
			</select>
		</div>
	</div>";
?>
<?php
echo "
	<div id='subsubcategory_backgroundimage' class='form-wrapper'> </div>
	<div id='subsubcategory_id-wrapper' class='form-wrapper' style='display:none;'>
		<div id='subsubcategory_id-label' class='form-label'>
			<label for='subsubcategory_id' class='optional'>" . $this->translate('3%s Level Category', "<sup>rd</sup>") . "</label>
		</div>
		<div id='subsubcategory_id-element' class='form-element'>
			<select name='subsubcategory_id' id='subsubcategory_id' onchange='showFields($(this).value, 3)'>
			</select>
		</div>
	</div>";
?>
<script type="text/javascript">
    var cateDependencyArray = '<?php echo json_encode($cateDependencyArray); ?>';

    var defaultProfileFieldId = '<?php echo $defaultProfileFieldId; ?>';
    var actionType = '<?php echo $project_id; ?>';
    var profile_type = '<?php echo $profile_type ?>';
    var previous_mapped_level = 0;

    function showFields(cat_value, cat_level) {

        if (cat_level == 1 || (previous_mapped_level >= cat_level && previous_mapped_level != 1) || (profile_type == null || profile_type == '' || profile_type == 0)) {
            profile_type = getProfileType(cat_value);
            if (profile_type == 0) {
                profile_type = '';
            } else {
                previous_mapped_level = cat_level;
            }
            $(defaultProfileFieldId).value = profile_type;
            changeFields($(defaultProfileFieldId));
        }

        if (actionType != 0) {
            prefieldForm();
        }
    }

    var sub = '';
    var subcatname = '';
    var show_subcat = 1;
<?php if (!empty($this->sitecrowdfunding->category_id)) : ?>
        show_subcat = 0;
<?php endif; ?>

    var subcategories = function(category_id, sub, subcatname)
    {
var aQuick = document.getElementById('sitecrowdfundings_create_quick');
var content = document;
if(aQuick){
content =aQuick;
} 
        content.getElementById('subcategory_id-wrapper').style.display = 'none';
        content.getElementById('subsubcategory_id-wrapper').style.display = 'none';

        if (cateDependencyArray.indexOf(category_id) == -1 || category_id == 0) {
            return;
        }
        content.getElementById('subcategory_id-wrapper').style.display = 'block';
        if (content.getElementById('buttons-wrapper')) {
            content.getElementById('buttons-wrapper').style.display = 'none';
        }
        var url = '<?php echo $this->url(array('action' => 'sub-category'), "sitecrowdfunding_general", true); ?>';

        content.getElementById('subcategory_backgroundimage').style.display = 'block';
        content.getElementById('subcategory_id').style.display = 'none';
        content.getElementById('subsubcategory_id').style.display = 'none';
        if (content.getElementById('subcategory_id-label'))
            content.getElementById('subcategory_id-label').style.display = 'none';
        if (content.getElementById('subsubcategory_id-label'))
            content.getElementById('subsubcategory_id-label').style.display = 'none';
        content.getElementById('subcategory_backgroundimage').innerHTML = '<div class="form-label"></div><div class="form-element"><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif" alt="" /></div>';

        en4.core.request.send(new Request.JSON({
            url: url,
            data: {
                format: 'json',
                category_id_temp: category_id
            },
            onSuccess: function(responseJSON) {
                if (content.getElementById('buttons-wrapper')) {
                    content.getElementById('buttons-wrapper').style.display = 'block';
                }
                content.getElementById('subcategory_backgroundimage').style.display = 'none';
                clear(content.getElementById('subcategory_id'));
                var subcatss = responseJSON.subcats;

                addOption(content.getElementById('subcategory_id'), " ", '0');
                for (i = 0; i < subcatss.length; i++) {
                    addOption(content.getElementById('subcategory_id'), subcatss[i]['category_name'], subcatss[i]['category_id']);
                    if (show_subcat == 0) {
                        content.getElementById('subcategory_id').disabled = 'disabled';
                        if (content.getElementById('subsubcategory_id'))
                            content.getElementById('subsubcategory_id').disabled = 'disabled';
                    }
                    content.getElementById('subcategory_id').value = sub;
                }

                if (category_id == 0) {
                    clear(content.getElementById('subcategory_id'));
                    content.getElementById('subcategory_id').style.display = 'none';
                    if (content.getElementById('subcategory_id-label'))
                        content.getElementById('subcategory_id-label').style.display = 'none';
                    if (content.getElementById('subsubcategory_id-label'))
                        content.getElementById('subsubcategory_id-label').style.display = 'none';
                }
            }
        }), {
          "force":true
        });
    };

    function clear(ddName)
    {

        if(ddName) {
			   for (var i = (ddName.options.length-1); i >= 0; i--) 
			   { 
			      ddName.options[ i ]=null; 			      
			   } 
			}
    }

    function addOption(selectbox, text, value)
    {

var aQuick = document.getElementById('sitecrowdfundings_create_quick');
var content = document;
if(aQuick){
content =aQuick;
} 
        var optn = document.createElement("OPTION");
        optn.text = text;
        optn.value = value;

        if (optn.text != '' && optn.value != '') {
            content.getElementById('subcategory_id').style.display = 'block';
            if (content.getElementById('subcategory_id-wrapper'))
                content.getElementById('subcategory_id-wrapper').style.display = 'block';
            if (content.getElementById('subcategory_id-label'))
                content.getElementById('subcategory_id-label').style.display = 'block';
            selectbox.options.add(optn);
        } else {
            content.getElementById('subcategory_id').style.display = 'none';
            if (content.getElementById('subcategory_id-wrapper'))
                content.getElementById('subcategory_id-wrapper').style.display = 'none';
            if (content.getElementById('subcategory_id-label'))
                content.getElementById('subcategory_id-label').style.display = 'none';
            selectbox.options.add(optn);
        }
    }

    function addSubOption(selectbox, text, value)
    {
        var aQuick = document.getElementById('sitecrowdfundings_create_quick');
var content = document;
if(aQuick){
content =aQuick;
} 
        var optn = document.createElement("OPTION");
        optn.text = text;
        optn.value = value;
        if (optn.text != '' && optn.value != '') {
            content.getElementById('subsubcategory_id').style.display = 'block';
            if (content.getElementById('subsubcategory_id-wrapper'))
                content.getElementById('subsubcategory_id-wrapper').style.display = 'block';
            if (content.getElementById('subsubcategory_id-label'))
                content.getElementById('subsubcategory_id-label').style.display = 'block';
            selectbox.options.add(optn);
        } else {
            content.getElementById('subsubcategory_id').style.display = 'none';
            if (content.getElementById('subsubcategory_id-wrapper'))
                content.getElementById('subsubcategory_id-wrapper').style.display = 'none';
            if (content.getElementById('subsubcategory_id-label'))
                content.getElementById('subsubcategory_id-label').style.display = 'none';
            selectbox.options.add(optn);
        }
    }

    var cat = '<?php echo $this->category_id ?>';
    if (cat != '') {
        sub = '<?php echo $this->subcategory_id; ?>';
        subcatname = '<?php echo $this->subcategory_name; ?>';
        subcategories(cat, sub, subcatname);
    }

    function changesubcategory(subcatid) {
var aQuick = document.getElementById('sitecrowdfundings_create_quick');
var content = document;
if(aQuick){
content =aQuick;
} 
        content.getElementById('subsubcategory_id-wrapper').style.display = 'none';
        if (cateDependencyArray.indexOf(subcatid) == -1 || subcatid == 0)
            return;
        content.getElementById('subsubcategory_backgroundimage').style.display = 'block';
        content.getElementById('subsubcategory_id-wrapper').style.display = 'none';
        if (content.getElementById('buttons-wrapper')) {
            content.getElementById('buttons-wrapper').style.display = 'none';
        }
        var url = '<?php echo $this->url(array('action' => 'subsub-category'), "sitecrowdfunding_general", true); ?>';

        content.getElementById('subsubcategory_id').style.display = 'none';
        if (content.getElementById('subsubcategory_id-label'))
            content.getElementById('subsubcategory_id-label').style.display = 'none';
        content.getElementById('subsubcategory_backgroundimage').innerHTML = '<div class="form-label"></div><div  class="form-element"><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif" /></center></div>';
        en4.core.request.send(new Request.JSON({
            url: url,
            data: {
                format: 'json',
                subcategory_id_temp: subcatid
            },
            onSuccess: function(responseJSON) {
                if (content.getElementById('buttons-wrapper')) {
                    content.getElementById('buttons-wrapper').style.display = 'block';
                }
                content.getElementById('subsubcategory_backgroundimage').style.display = 'none';
                clear(content.getElementById('subsubcategory_id'));
                var subsubcatss = responseJSON.subsubcats;

                addSubOption(content.getElementById('subsubcategory_id'), " ", '0');
                for (i = 0; i < subsubcatss.length; i++) {
                    addSubOption(content.getElementById('subsubcategory_id'), subsubcatss[i]['category_name'], subsubcatss[i]['category_id']);
                }
            }
        }));
    }
</script>
