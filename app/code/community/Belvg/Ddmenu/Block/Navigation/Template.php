<?php
/**
 * BelVG LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 *
/**********************************************
 *        MAGENTO EDITION USAGE NOTICE        *
 **********************************************/
/* This package designed for Magento COMMUNITY edition
 * BelVG does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BelVG does not provide extension support in case of
 * incorrect edition usage.
/**********************************************
 *        DISCLAIMER                          *
 **********************************************/
/* Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 **********************************************
 * @category   Belvg
 * @package    Belvg_DropDownMenu
 * @copyright  Copyright (c) 2010 - 2012 BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */

class Belvg_Ddmenu_Block_Navigation_Template extends Mage_Core_Block_Template
{
    /**
     * Init block caching
     */
    protected function _construct()
    {
        $this->addData(array(
                'cache_lifetime' => FALSE,
                'cache_tags'     => array('belvg_ddmenu_template'),
        ));
    }

    /**
     * HTML Template
     *
     * @return string (or false)
     */
    protected function _toHtml()
    {
        if ($this->template_file) {
            $this->setTemplate($this->template_file);

            return parent::_toHtml();
        }

        return FALSE;
    }

}