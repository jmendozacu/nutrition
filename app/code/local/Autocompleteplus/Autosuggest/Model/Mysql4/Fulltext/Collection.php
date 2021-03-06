<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_CatalogSearch
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Autocompleteplus_Autosuggest_Model_Mysql4_Fulltext_Collection
    extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
{
    /**
     * Retrieve query model object
     *
     * @return Mage_CatalogSearch_Model_Query
     */
    protected function _getQuery()
    {
        return Mage::helper('catalogsearch')->getQuery();
    }

    /**
     * Add search query filter
     *
     * @param   Mage_CatalogSearch_Model_Query $query
     * @return  Mage_CatalogSearch_Model_Mysql4_Search_Collection
     */
    public function addSearchFilter($query)
    {
        $helper=Mage::helper('autocompleteplus_autosuggest');

        $enabledFulltext=$helper->getConfigDataByFullPath('autocompleteplus/config/enabled_fulltext');

        if($enabledFulltext){
            //add if enabled write here call to api to get ids
            $key=$helper->getUUID();

            $storeId = Mage::app()->getStore()->getStoreId();

            $url='http://0-1vk.acp-magento.appspot.com/ma_search?q='.$query.'&p=1&products_per_page=10&v=4.5.44&store_id='.$storeId.'&UUID='.$key;

            $jsonIds=$helper->sendCurl($url);

            $jsonObj=json_decode($jsonIds);

            $totalResults=$jsonObj->total_results;

            if($totalResults){

                $id_list=$jsonObj->id_list;

                $newIdsArr=array();

                //validate received ids
                foreach($id_list as $id){
                    if($id!=null && is_numeric($id)){
                        $newIdsArr[]=$id;
                    }
                }

                if(count($newIdsArr)>0){
                    $idStr=implode(',',$newIdsArr);
                }else{
                    $idStr='0';
                }


            }else{
                $idStr='0';
            }

            $this->getSelect()->where('e.entity_id IN ('.$idStr.')');

        }else{
            //adding if fulltext search disabled then write regular flow
            Mage::getSingleton('catalogsearch/fulltext')->prepareResult();

                    $this->getSelect()->joinInner(
                        array('search_result' => $this->getTable('catalogsearch/result')),
                        $this->getConnection()->quoteInto(
                            'search_result.product_id=e.entity_id AND search_result.query_id=?',
                            $this->_getQuery()->getId()
                        ),
                        array('relevance' => 'relevance')
                    );

        }

        return $this;
    }

    /**
     * Set Order field
     *
     * @param string $attribute
     * @param string $dir
     * @return Mage_CatalogSearch_Model_Mysql4_Fulltext_Collection
     */
    public function setOrder($attribute, $dir='desc')
    {

        $helper=Mage::helper('autocompleteplus_autosuggest');

        $enabledFulltext=$helper->getConfigDataByFullPath('autocompleteplus/config/enabled_fulltext');

        if($enabledFulltext){

        }else{

            if ($attribute == 'relevance') {
                $this->getSelect()->order("relevance {$dir}");
            }
            else {
                parent::setOrder($attribute, $dir);
            }

        }

        return $this;
    }
}
