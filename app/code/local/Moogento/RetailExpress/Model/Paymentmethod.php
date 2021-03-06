<?php
/**
 * Moogento_RetailExpress extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Moogento
 * @package        Moogento_RetailExpress
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * RetailExpress Payment method model
 *
 * @category    Moogento
 * @package     Moogento_RetailExpress
 * @author      Ultimate Module Creator
 */
class Moogento_RetailExpress_Model_Paymentmethod extends Mage_Core_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'moogento_retailexpress_paymentmethod';
    const CACHE_TAG = 'moogento_retailexpress_paymentmethod';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'moogento_retailexpress_paymentmethod';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'paymentmethod';

    /**
     * constructor
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('moogento_retailexpress/paymentmethod');
    }

    /**
     * before save retailexpress payment method
     *
     * @access protected
     * @return Moogento_RetailExpress_Model_Paymentmethod
     * @author Ultimate Module Creator
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $now = Mage::getSingleton('core/date')->gmtDate();
        if ($this->isObjectNew()) {
            $this->setCreatedAt($now);
        }
        $this->setUpdatedAt($now);
        return $this;
    }

    /**
     * save retailexpress payment method relation
     *
     * @access public
     * @return Moogento_RetailExpress_Model_Paymentmethod
     * @author Ultimate Module Creator
     */
    protected function _afterSave()
    {
        return parent::_afterSave();
    }

    /**
     * get default values
     *
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function getDefaultValues()
    {
        $values = array();
        $values['status'] = 1;
        return $values;
    }
    
}
