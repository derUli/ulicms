<?php
namespace GDPR\PersonalData;

class Query
{

    public function getData($query)
    {
        $result = array();
        $modules = getAllModules();
        foreach ($modules as $module) {
            $personal_data_query_responder = getModuleMeta($module, "personal_data_query_responder");
            if ($personal_data_query_responder and class_exists($personal_data_query_responder)) {
                $responder = new $personal_data_query_responder();
                $data = $responder->getData($query);
                $result[] = $data;
            }
        }
        return $result;
    }
}