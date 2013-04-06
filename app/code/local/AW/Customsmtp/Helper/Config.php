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
 */
class AW_Customsmtp_Helper_Config extends Mage_Core_Helper_Abstract{
	
	const XML_PATH_ENABLED = 'customsmtp/general/enabled';
	const XML_PATH_SMTP_HOST = 'customsmtp/smtp/host';
	const XML_PATH_SMTP_PORT = 'customsmtp/smtp/port';
	const XML_PATH_SMTP_SSL = 'customsmtp/smtp/ssl';
	const XML_PATH_SMTP_LOGIN = 'customsmtp/smtp/login';
	const XML_PATH_SMTP_AUTH = 'customsmtp/smtp/auth';
	const XML_PATH_SMTP_PASSWORD = 'customsmtp/smtp/password';
}