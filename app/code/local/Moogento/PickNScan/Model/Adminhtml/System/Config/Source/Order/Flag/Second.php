<?php

class Moogento_PickNScan_Model_Adminhtml_System_Config_Source_Order_Flag_Second
{
    public function toOptionArray()
    {
        $options = array();
        foreach (Mage::helper('moogento_pickscan')->getCustomPreset(2) as $value => $label) {
            $options[] = array(
                'value' => $value,
                'label' => $label,
            );
        }

        return $options;
    }
} 