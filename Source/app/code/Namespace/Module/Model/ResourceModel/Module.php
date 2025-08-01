<?php

namespace <Namespace>\<Module>\Model\ResourceModel;

use Magento\Store\Model\Store;
use Magento\Framework\DB\Select;

/**
 * <Module> Resource Model
 */
class <Module> extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('<module>', '<module>_id');
    }

    /**
     * Retrieve load select with filter by identifier, store and activity
     *
     * @param string $identifier
     * @param int|array $store
     * @param int $isActive
     * @return Select
     */
    protected function _getLoadByIdentifierSelect($identifier, $store, $isActive = null)
    {
        $select = $this->getConnection()->select()
            ->from(['cp' => $this->getMainTable()])
            ->join(
                ['cps' => $this->getTable('<module>_store')],
                'cp.<module>_id' . ' = cps.<module>_id',
                []
            )
            ->where('cp.url_key = ?', $identifier)
            ->where('cps.store_id IN (?)', $store);

        if ($isActive !== null) {
            $select->where('cp.is_active = ?', $isActive);
        }

        return $select;
    }

    /**
     * Check if page identifier exists for specific store, return <module> id if <module> exists
     *
     * @param string $identifier
     * @param int $storeId
     * @return int
     */
    public function checkIdentifier($identifier, $storeId)
    {
        $stores = [Store::DEFAULT_STORE_ID, $storeId];
        $select = $this->_getLoadByIdentifierSelect($identifier, $stores, 1);
        $select->reset(Select::COLUMNS)
            ->columns('cp.<module>_id')
            ->order('cps.store_id DESC')
            ->limit(1);

        return $this->getConnection()->fetchOne($select);
    }

    /**
     * Check if <module> identifier exists, return <module> id if <module> exists
     *
     * @param string $identifier
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function checkExistingIdentifier($identifier)
    {
        $select = $this->getConnection()->select()
            ->from(['cp' => $this->getMainTable()])
            ->where('cp.url_key = ?', $identifier);
        $select->reset(Select::COLUMNS)
            ->columns('cp.<module>_id')
            ->limit(1);

        return $this->getConnection()->fetchOne($select);
    }
}
