<?php

class Impactx_MetricsController extends Core_Controller_Action_Standard {

    public function toggleVisibilityAction() {
        if (empty($_POST) || !isset($_POST['metric_id'])) {
            echo 'Error';
            exit;
        }

        $values = $_POST;

        if(empty($values)){
            echo 'Error';
            exit;
        }
        $metric_id = $values['metric_id'];

        $metric = Engine_Api::_()->getItem('sitepage_metric', $metric_id);

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            if ($metric->project_side_visibility) {
                $metric->project_side_visibility = 0;
            }
            else {
                $metric->project_side_visibility = 1;
            }
            $metric->save();
            $db->commit();

        }catch (Exception $e){
            $db->rollBack();
            throw $e;
        }
        
        echo 'Success';
    }

}
