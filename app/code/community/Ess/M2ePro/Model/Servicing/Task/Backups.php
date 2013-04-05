<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Servicing_Task_Backups implements Ess_M2ePro_Model_Servicing_Task
{
    // ########################################

    public function getPublicNick()
    {
        return 'backups';
    }

    // ########################################

    public function getRequestData()
    {
        return array();
    }

    public function processResponseData(array $data)
    {

    }

    // ########################################
}