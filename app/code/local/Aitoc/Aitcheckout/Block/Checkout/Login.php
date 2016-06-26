<?php
/**
 * One Step Checkout Manager : One Step Checkout Manager (OPCB Unit)
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitcheckout / Aitoc_Aitcheckout
 * @version      1.0.9 - 1.4.9
 * @license:     Nichj4LUEMsSNLvlLmobwL49OlCNVmKqxOe78SZxGK
 * @copyright:   Copyright (c) 2014 AITOC, Inc. (http://www.aitoc.com)
 */
class Aitoc_Aitcheckout_Block_Checkout_Login extends Mage_Checkout_Block_Onepage_Abstract
{
    public function getCaptchaReloadUrl() {
        if(!$this->helper('aitcheckout/captcha')->checkIfCaptchaEnabled()) {
            return '';
        }
        $blockPath = Mage::helper('captcha')->getCaptcha('user_login')->getBlockName();
        $block = $this->getLayout()->createBlock($blockPath);
        return $block->getRefreshUrl();
    }

}