<?php

/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 * 
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Customsmtp
 * @copyright  Copyright (c) 2010-2011 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */class AW_Customsmtp_Model_Email_Template extends Mage_Core_Model_Email_Template {

    private $_saveRange = array();

    protected function _saveMail($email, $name=null, array $variables = array()) {
        Mage::getModel('customsmtp/mail')
        ->setSubject()
        ->setIsPlain()
        ->setBody()
        ->setFromEmail()
        ->setFromName()
        ->setToEmail()
        ->setToName()
        ->save();

        return $this;
    }

    public function sendMail(AW_Customsmtp_Model_Mail $Mail) {
        if (Mage::getStoreConfig('customsmtp/general/mode') == AW_Customsmtp_Model_Source_Mode::OFF) {
            return $this;
        }
        $config = array(
            'port' => Mage::getStoreConfig(AW_Customsmtp_Helper_Config::XML_PATH_SMTP_PORT), //optional - default 25
            'auth' => Mage::getStoreConfig(AW_Customsmtp_Helper_Config::XML_PATH_SMTP_AUTH),
            'username' => Mage::getStoreConfig(AW_Customsmtp_Helper_Config::XML_PATH_SMTP_LOGIN),
            'password' => Mage::getStoreConfig(AW_Customsmtp_Helper_Config::XML_PATH_SMTP_PASSWORD)
        );

        $needSSL = Mage::getStoreConfig(AW_Customsmtp_Helper_Config::XML_PATH_SMTP_SSL);
        if (!empty($needSSL)) {
            $config['ssl'] = Mage::getStoreConfig(AW_Customsmtp_Helper_Config::XML_PATH_SMTP_SSL);
        }

        $transport = new Zend_Mail_Transport_Smtp(Mage::getStoreConfig(AW_Customsmtp_Helper_Config::XML_PATH_SMTP_HOST), $config);
        ini_set('SMTP', Mage::getStoreConfig('system/smtp/host'));
        ini_set('smtp_port', Mage::getStoreConfig('system/smtp/port'));

        $mail = $this->getMail();

        $mail->setSubject('=?utf-8?B?' . base64_encode($Mail->getSubject()) . '?=');

         /* Starts from 1.10.1.1 version "TO" holds array values */
        if(!empty($this->_saveRange)) {
            foreach($this->_saveRange as $range) {
                $mail->addTo($range['email'], '=?utf-8?B?' . base64_encode($range['name']) . '?=');
            }
        }

        else {
            $mail->addTo($Mail->getToEmail(), '=?utf-8?B?' . base64_encode($Mail->getToName()) . '?=');
        }
        /***/
        
        $mail->setFrom($Mail->getFromEmail(), $Mail->getFromName());

        if ($Mail->getIsPlain()) {
            $mail->setBodyText($Mail->getBody());
        } else {
            $mail->setBodyHTML($Mail->getBody());
        }

        $this->setUseAbsoluteLinks(true);

        try {
            $mail->send($transport); //add $transport object as parameter
            $this->_mail = null;
        } catch (Exception $e) {
            //print_r($e); die();
            $Mail
            ->setBody($e->getMessage())
            ->setStatus('failed')
            ->save();
            throw($e);
            return false;
        }
        return true;
    }

    public function send($email, $name=null, array $variables = array()) {

        if (!Mage::getStoreConfig(AW_Customsmtp_Helper_Config::XML_PATH_ENABLED)) {
            return parent::send($email, $name, $variables);
        }
        
        /**
        *   If existing order is edited from admin panel, send function is called twice
        *  and for the first time $email VAR has no value. Log exception is created every time
        *  send function is called with $email VAR === NULL
        * 'Zend_Mail_Transport_Exception' with message 'Unable to send mail'
        */

        if (!$this->isValidForSend() || !$email) {
            return false;
        }

        $Mail = Mage::getModel('customsmtp/mail');

        if (is_null($name)) {
            $name = substr($email, 0, strpos($email, '@'));
        }

        $variables['email'] = $email;
        $variables['name'] = $name;

        $Mail->setBody($this->getProcessedTemplate($variables, true));
        $Mail->setIsPlain($this->isPlain());
        $Mail->setSubject($this->getProcessedTemplateSubject($variables));


        $Mail
        ->setFromName($this->getSenderName())
        ->setFromEmail($this->getSenderEmail())
        ->setReplyTo($this->getReplyTo())
        ->setToName($name)
        ->setToEmail($email)
        ->setTemplateId($this->getTemplateId())
        ->setStoreId(Mage::app()->getStore()->getId());



        if (Mage::getStoreConfig('customsmtp/general/log')) {
                if(is_array($email)) {
                    $mailData = $Mail->getData();
                    $this->_saveRange = $this->_getToData($email,$name);
                       for($i=0;$i<count($this->_saveRange);$i++) {
                           Mage::getModel('customsmtp/mail')
                                 ->setData($mailData)
                                 ->setToName($this->_saveRange[$i]['name'])
                                 ->setToEmail($this->_saveRange[$i]['email'])
                                 ->save();
                        }
                 }

                 else { $Mail->save(); }
        }

        if (Mage::getStoreConfig('customsmtp/general/mode') == AW_Customsmtp_Model_Source_Mode::ON) {
            $this->sendMail($Mail);
        } elseif (Mage::getStoreConfig('customsmtp/general/mode') == AW_Customsmtp_Model_Source_Mode::CORE) {
            try {
                return parent::send($email, $name, $variables);
            } catch (Exception $e) {
                if (Mage::getStoreConfig('customsmtp/general/log')) {
                    $Mail
                    ->setBody($e->getMessage())
                    ->setStatus('failed')
                    ->save();
                } else {
                    throw $e;
                }
            }
        }
        return true;
    }

    private function _getToData($email,$name) {

        $range = array();

         if(!is_array($name)) {
             $name = (array) $name;
         }

         for($i=(count($email)-1);$i>=0;$i--) {

            if (!isset($name[$i])) {
                $name[$i] = substr($email[$i], 0, strpos($email[$i], '@'));
            }

            if(isset($name[$i]) && !is_array($name[$i]) && empty($name[$i])) {
                $name[$i] = substr($email[$i], 0, strpos($email[$i], '@'));
            }

            $range[$i]['email'] = $email[$i];
            $range[$i]['name'] = $name[$i];

         }

         return $range;
    }




}
