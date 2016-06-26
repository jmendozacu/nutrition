<?php

class Moogento_Core_Model_Countrytemplate extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('moogento_core/countrytemplate');
    }
    
    protected function _beforeSave()
    {
        if($this->isObjectNew()) {
            
            $table_name = Mage::getSingleton('core/resource')->getTableName('moogento_core/countrytemplate');
            $query = <<<HEREDOC
                SELECT max(sort_number)
                FROM {$table_name}
HEREDOC;
            $read = Mage::getSingleton('core/resource')->getConnection('core_read');
            $data = $read->fetchOne($query);
            $this->setSortNumber((int)$data+1);
        }
        return parent::_beforeSave();
    }
    
} 