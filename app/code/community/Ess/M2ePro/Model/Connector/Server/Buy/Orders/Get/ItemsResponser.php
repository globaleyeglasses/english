<?php

/*
 * @copyright  Copyright (c) 2012 by  ESS-UA.
 */

class Ess_M2ePro_Model_Connector_Server_Buy_Orders_Get_ItemsResponser
    extends Ess_M2ePro_Model_Connector_Server_Buy_Responser
{
    // ########################################

    protected function unsetLocks($fail = false, $message = NULL) {}

    // ########################################

    protected function validateResponseData($response)
    {
        if (!isset($response['orders'])) {
            return false;
        }

        return true;
    }

    protected function processResponseData($response)
    {
        return $response['orders'];
    }

    // ########################################
}