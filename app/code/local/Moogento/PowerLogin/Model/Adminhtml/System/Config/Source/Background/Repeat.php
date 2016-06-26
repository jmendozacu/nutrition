<?php


class Moogento_PowerLogin_Model_Adminhtml_System_Config_Source_Background_Repeat
{
    public function toOptionArray()
    {
        $options = array(
            array(
                'value' => 'no-repeat',
                'label' => Mage::helper('moogento_powerlogin')->__('No Repeat')
            ),
            array(
                'value' => 'repeat-x',
                'label' => Mage::helper('moogento_powerlogin')->__('Repeat horizontally')
            ),
            array(
                'value' => 'repeat-y',
                'label' => Mage::helper('moogento_powerlogin')->__('Repeat vertically')
            ),
            array(
                'value' => 'repeat',
                'label' => Mage::helper('moogento_powerlogin')->__('Repeat both')
            ),
        );

        return $options;
    }
} 