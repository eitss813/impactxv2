<?php

class Impactx_Api_Core extends Core_Api_Abstract
{
    /*
     * This method is the copy from the Yndynamicform/Entries.php file, and I modified it according to my requirements.
     */
    function getTotalSubmittedEntries($form_id, $params = array()) {
        $table = Engine_Api::_()->getDbtable('entries', 'yndynamicform');
        $rName = $table->info('name');

        $select = $table->select()->from($rName)
            ->where('form_id = ?', $form_id)
            ->where('submission_status = \'submitted\' OR submission_status = \'preview\'')
            ->order('creation_date DESC');
        
        return count($table->fetchAll($select));
      
    }
    
    /*
     * This method is the copy from the Yndynamicform/Entries.php file, and I modified it according to my requirements.
     */
    public function getSubmittedEntries($form_id, $page_no, $params = array(), $total_count = false)
    {
        $table = Engine_Api::_()->getDbtable('entries', 'yndynamicform');
        $rName = $table->info('name');

        $select = $table->select()->from($rName)
            ->setIntegrityCheck(false)
            ->where('form_id = ?', $form_id)
            ->where('submission_status = \'submitted\' OR submission_status = \'preview\'');

       //fetch only submitted result
        if (!empty($params['submission_status']))
            $select->where("$rName.submission_status = ?", $params['submission_status']);

        // project_id
        if (!empty($params['project_id']))
            $select->where("$rName.project_id = ?", $params['project_id']);

        // KEYWORD TO SEARCH FORM TITLE
        if (!empty($params['keyword'])) {
            $searchTable = Engine_Api::_() -> getDbtable('search', 'core');
            $sName = $searchTable -> info('name');
            $select
                -> joinRight($sName, $sName . '.id=' . $rName . '.form_id', null)
                -> where($sName . '.type = ?', 'yndynamicform_form')
                -> where($sName . '.title LIKE ?', "%{$params['keyword']}%");
        }

        // Entry ID ? OR Form ID?
        if (!empty($params['entry_id'])) {
            $userTable = Engine_Api::_() -> getItemTable('user');
            $uName = $userTable -> info('name');
            $select->joinLeft($uName, $uName . '.user_id=' . $rName . '.owner_id', null);
            $keyword = '%'.$params['entry_id'].'%';
            $select->where("$rName.entry_id like ? OR $rName.user_email like ? OR $uName.username like ? OR $uName.displayname like ?" , $keyword, $keyword, $keyword, $keyword, $keyword);
        }

        // Time range
        if (isset($params['start_date']) && !empty($params['start_date'])) {   
            if( isset($params['start_date']['date']) && !empty($params['start_date']['date']) )
                $start_date = date('Y-m-d', strtotime($params['start_date']['date'])) . ' 00:00:00';
            
            $select->where("$rName.creation_date >= ?", $start_date);
        }

        if (isset($params['to_date']) && !empty($params['to_date'])) {
            if( isset($params['to_date']['date']) && !empty($params['to_date']['date']) )
                $to_date = date('Y-m-d', strtotime($params['to_date']['date'])) . ' 00:00:00';
            
            $select->where("$rName.creation_date < ?", $to_date);
        }

        // PROCESS ADVANCED SEARCH
        if (!empty($params['advsearch']) && !empty($params['conditional_logic'])) {
            $valueTable = Engine_Api::_()->fields()->getTable('yndynamicform_entry', 'values');

            $filedArr = $params['conditional_logic']['field_id'];
            $opArr = $params['conditional_logic']['compare'];
            $valueArr = $params['conditional_logic']['value'];
            // TYPE IS USED TO PROCESS FILE UPLOAD FIELDS
            $typeArr = $params['conditional_logic']['type'];
            
            // ADDITIONAL JOIN TO SEARCH CONDITIONALLY
            foreach ($filedArr as $key => $field_id) {
                // PROCESS FILE UPLOAD
                $this->addCondition($select, $valueTable, $rName, $field_id, $opArr[$key], $valueArr[$key], $typeArr[$key]);
            }
        }

        $paginator = Zend_Paginator::factory($select);
        
        if( !empty($total_count) ) {
            $getTotalCount = (int) $paginator->getTotalItemCount();
            return $getTotalCount;
        }
        
        if (!empty($page_no)) {
            $paginator->setCurrentPageNumber($page_no);
        }
        $paginator->setItemCountPerPage(5);
        return $paginator;
    }
    
    public function addCondition(&$select, $valueTable, $rName, $field_id, $operator, $value, $type) {
        
        if( empty($value) )
            return;
        
        
        $vName = $valueTable->info('name');
        // RANDOM STRING TO PAIR EACH COMPARE
        $random = rand();
        try{
            $select->joinLeft("$vName AS t$random$field_id", "t$random$field_id.item_id=$rName.entry_id");
        } catch (Exception $ex) {
            return;
            // Blank exception
        }

        if ($type == 'file_upload') {
            $notEmptyFields = array();
            $fileSelect = $valueTable->select()->where('field_id = ? ', $field_id);
            $fileFields = $valueTable->fetchall($fileSelect);
            foreach ($fileFields as $fileField) {
                $file_ids = json_decode(html_entity_decode($fileField->value))->file_ids;

                if (!empty($file_ids)) {
                    $notEmptyFields[] = $fileField->item_id;
                }
            }
            $in = implode("','", $notEmptyFields);
            if ($value) {
                $select->where("t$random$field_id.item_id IN ('$in')");
            } else {
                $select->where("t$random$field_id.item_id NOT IN ('$in')");
            }
            $select->group("t$random$field_id.item_id");
        }
        else {
            
            $select->where("t$random$field_id.field_id = ?", $field_id);
            switch ($operator) {
                case 'is':
                    $select->where("t$random$field_id.value = ?", $value);
                    break;
                case 'is_not':
                    $select->where("t$random$field_id.value <> ?", $value);
                    break;
                case 'contains':
                    $select->where("t$random$field_id.value LIKE ?", '%' . $value . '%');
                    break;
                case 'starts_with':
                    $select->where("t$random$field_id.value LIKE ?", $value . '%');
                    break;
                case 'ends_with':
                    $select->where("t$random$field_id.value LIKE ?", '%' . $value);
                    break;
                case 'does_not_contain':
                    $select->where("t$random$field_id.value NOT LIKE ?", '%' . $value . '%');
                    break;
                case 'is':
                    $select->where("t$random$field_id.value = ?", $value);
                    break;
                case 'after':
                case 'greater_than':
                    $select->where("t$random$field_id.value > ?", $value);
                    break;
                case 'before':
                case 'less_than':
                    $select->where("t$random$field_id.value < ?", $value);
                    break;
            }
        }
    }
    
    /*
     * This method is the copy from the Yndynamicform/Entries.php file, and I modified it according to my requirements.
     */
    public function getEntriesCountByFormId($form_id){
        $table = Engine_Api::_()->getDbtable('entries', 'yndynamicform');
        $rName = $table->info('name');

        $select = $table->select()->from($rName, 'entry_id');

        $select
            ->where("form_id = ?", $form_id)
            ->where('submission_status = \'submitted\' OR submission_status = \'preview\'');


        return $select->query()->fetchAll();

    }
    
    /*
     * This method is the copy from the Yndynamicform/Entries.php file, and I modified it according to my requirements.
     */
    public function metricsSuggestion($page_id, $text) {
        $metrics_array = Engine_Api::_()->getDbtable('metrics', 'sitepage')->getMetricsDataByOrganisationIdAndText($page_id, $text);
        
        $data = array();
        if( !empty($metrics_array) ) {
            foreach ($metrics_array as $metric) {
                
                $metric->metric_name = str_replace("'", "\'", $metric->metric_name);
                $metric->metric_description = str_replace("'", "\'", $metric->metric_description);
                
                $data[] = array(
                    'id' => $metric->metric_id,
                    'label' => str_replace("'", "\'", $metric->metric_name),
                    'metric_name' => str_replace("'", "\'", $metric->metric_name),
                    'metric_description' => str_replace("'", "\'", $metric->metric_description),
                    'metric_unit' => str_replace("'", "\'", $metric->metric_unit),
                    'metric_id' => str_replace("'", "\'", $metric->metric_id),
                    'photo' => ''
                );
            }
        }
        
        return $data;
    }
    
    public function getEntryIDByOwnerIdAndFormId($form_id, $owner_id){
        $table = Engine_Api::_()->getDbtable('entries', 'yndynamicform');
        $rName = $table->info('name');

        $select = $table->select()-> from($rName, 'entry_id');

        $select
            ->where("$rName.form_id = ?", $form_id)
            ->where("$rName.owner_id = ?", $owner_id);
        $select->order('entry_id DESC');

        return $select-> query()->fetchColumn();

    }
    
    public function getAllSubmittedEntries($form_id) {
        $table = Engine_Api::_()->getDbtable('entries', 'yndynamicform');
        $rName = $table->info('name');

        $select = $table->select()->from($rName)
            ->where('form_id = ?', $form_id)
            ->order('creation_date DESC');

        return $table->fetchAll($select);
    }
    
    /*
     * Update the formula values for the all metrics available in updated form field.
     */
    public function updateFormulaOnEditNumberField($params) {
        $option_id = $params['option_id'];
        
        $tempParams = array();
        $fieldMaps = Engine_Api::_()->fields()->getFieldsMaps('yndynamicform_entry')->getRowsMatching('option_id', $option_id);
        foreach ($fieldMaps as $item) {
            $field = $item->getChild();
            $values = $field->toArray();
            
            if( isset($values['type']) && !empty($values['type']) && ($values['type'] == 'float') ) {
                $tempParams[$values['field_id']] = ($params['field_id'] == $values['field_id'])? $params['label']: $values['label'];
            }
        }
        
        foreach ($fieldMaps as $item) {
            $field = $item->getChild();
            $values = $field->toArray();
            
            if( isset($values['type']) && !empty($values['type']) && ($values['type'] == 'metrics') ) {
                $own_formula_input = $own_actual_formula = $values['config']['own_formula_by_id'];
                if( !empty($tempParams) && !empty($own_actual_formula) && !empty($own_formula_input) ) {
                    foreach($tempParams as $field_id => $label) {
                        $own_formula_input = str_replace("field_id_" . $field_id, $label, $own_formula_input);
                        $own_actual_formula = str_replace("field_id_" . $field_id, '[' . $label . ']', $own_actual_formula);
                    }
                }
                
                
                $values['config']['own_formula_input'] = $own_formula_input;
                $values['config']['own_actual_formula'] = $own_actual_formula;
                
                $db = Engine_Db_Table::getDefaultAdapter();
                $db->update('engine4_yndynamicform_entry_fields_meta', array(
                    'config' => json_encode($values['config']),
                ), array(
                    'field_id = ?' => $values['field_id'],
                ));
            }
        }
        
        return;
    }
    
    /*
     * Validate the metrics formula on number field delete
     */
    public function validateMetricsFormulaOnNumFieldDeletion($map) {
        $option_id = $map->option_id;
        $child_id = $map->child_id;
        
        $fieldMaps = Engine_Api::_()->fields()->getFieldsMaps('yndynamicform_entry')->getRowsMatching('option_id', $option_id);
        foreach ($fieldMaps as $item) {
            $field = $item->getChild();
            $values = $field->toArray();
            
            if( isset($values['type']) && !empty($values['type']) && ($values['type'] == 'metrics') && isset($values['config']['metric_aggregate_fields']) && !empty($values['config']['metric_aggregate_fields']) && in_array($child_id, $values['config']['metric_aggregate_fields']) )
                return true;
        }

        return false;
    }
    
    public function getProjectSideVisibility($metric_id = 0) {
        $metric = Engine_Api::_()->getItem('sitepage_metric', $metric_id);
        
        return (isset($metric->project_side_visibility) && !empty($metric->project_side_visibility))? $metric->project_side_visibility: false;
    }
    
    public function getParentOrganization($projectId){
        $table = Engine_Api::_()->getDbtable('organizations', 'sitecrowdfunding');
        $rName = $table->info('name');
        
       //MAKE QUERY
       $select = $table->select()
           ->where('organization_type = ?', 'parent')
           ->where('project_id = ?', $projectId);
       //RETURN RESULTS
       $result =  $select->query()->fetchAll();

       if(!empty($result) && count($result) > 0){
           $orgId = $result[0]['organization_id'];
           $orgItem = Engine_Api::_()->getItem('sitecrowdfunding_organization', $orgId);
           if(!empty($orgItem)){
               return array( 'page_id' => $orgItem['organization_id'], 'title' => $orgItem['title'], 'logo' => $orgItem->getLogoUrl(), 'link' => $orgItem['link']);
           }
           return null;
       }
       return null;
   }
}

