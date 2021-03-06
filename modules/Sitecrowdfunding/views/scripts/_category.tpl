<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _category.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="form-wrapper" id="subcategory_id-wrapper" style='display:none;'>
    <div class="form-label" id="subcategory_id-label">
        <label class="optional" for="subcategory_id"><?php echo $this->translate('Sub-Category'); ?></label>
    </div>
    <div class="form-element" id="subcategory_id-element">
        <select id="subcategory_id" name="subcategory_id" onchange='addProjectOptions(this.value, "subcat_dependency", "subsubcategory_id", 0);
                setProjectHiddenValues("subcategory_id");'></select>
    </div>
</div>

<div class="form-wrapper" id="subsubcategory_id-wrapper" style='display:none;'>
    <div class="form-label" id="subsubcategory_id-label">
        <label class="optional" for="subsubcategory_id"><?php echo $this->translate('3%s Level Category', "<sup>rd</sup>") ?></label>
    </div>
    <div class="form-element" id="subsubcategory_id-element">
        <select id="subsubcategory_id" name="subsubcategory_id" onchange='setProjectHiddenValues("subsubcategory_id")' ></select>
    </div>
</div>

<script type="text/javascript">

    function setProjectHiddenValues(element_id) {
        $('hidden_project_' + element_id).value = $(element_id).value;
    }

    function addProjectOptions(element_value, element_type, element_updated, domready) {

        var element = $(element_updated);
        if (domready == 0) {
            switch (element_type) {
                case 'cat_dependency':
                    $('subcategory_id' + '-wrapper').style.display = 'none';
                    clear($('subcategory_id'));
                    $('subcategory_id').value = 0;
                    $('hidden_project_subcategory_id').value = 0;
                case 'subcat_dependency':
                    $('subsubcategory_id' + '-wrapper').style.display = 'none';
                    clear($('subsubcategory_id'));
                    $('subsubcategory_id').value = 0;
                    $('hidden_project_subsubcategory_id').value = 0;
            }
        }

        if (element_value <= 0)
            return;

        var url = '<?php echo $this->url(array('module' => 'sitecrowdfunding', 'controller' => 'project', 'action' => 'get-projects-categories'), "default", true); ?>';
        en4.core.request.send(new Request.JSON({
            url: url,
            data: {
                format: 'json',
                element_value: element_value,
                element_type: element_type
            },
            onSuccess: function(responseJSON) {
                var categories = responseJSON.categories;
                var option = document.createElement("OPTION");
                option.text = "";
                option.value = 0;
                element.options.add(option);
                for (i = 0; i < categories.length; i++) {
                    var option = document.createElement("OPTION");
                    option.text = categories[i]['category_name'];
                    option.value = categories[i]['category_id'];
                    element.options.add(option);
                }

                if (categories.length > 0)
                    $(element_updated + '-wrapper').style.display = 'block';
                else
                    $(element_updated + '-wrapper').style.display = 'none';
                if (categories.length <= 0)
                    $('hidden_project_' + element_updated).value = 0;
                if (domready == 1 && $('hidden_project_' + element_updated).value) {
                    $(element_updated).value = $('hidden_project_' + element_updated).value;
                }
            }

        }), {'force': true});
    }

    function clear(element)
    {
        for (var i = (element.options.length - 1); i >= 0; i--) {
            element.options[ i ] = null;
        }
    }

    window.addEvent('domready', function() {
        if($('hidden_project_category_id-wrapper'))
        $('hidden_project_category_id-wrapper').style.display = 'none';
    if($('hidden_project_subcategory_id-wrapper'))
        $('hidden_project_subcategory_id-wrapper').style.display = 'none';
    if($('hidden_project_subsubcategory_id-wrapper'))
        $('hidden_project_subsubcategory_id-wrapper').style.display = 'none';

        if ($("hidden_project_category_id").value) {
            addProjectOptions($("hidden_project_category_id").value, 'cat_dependency', 'subcategory_id', 1);

            if ($("hidden_project_subcategory_id").value) {
                addProjectOptions($("hidden_project_subcategory_id").value, 'subcat_dependency', 'subsubcategory_id', 1);
              //  $('subsubcategory_id').value = $("hidden_project_subcategory_id").value;
            }
            
             if ($("hidden_project_subsubcategory_id").value) {
               // addProjectOptions($("hidden_project_subsubcategory_id").value, 'subcat_dependency', 'subsubcategory_id', 1);
              $('subsubcategory_id').value = $("hidden_project_subsubcategory_id").value;
            }
        }
    });
</script>