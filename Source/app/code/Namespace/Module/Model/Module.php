<?php

namespace <Namespace>\<Module>\Model;

use Magento\Framework\Exception\LocalizedException;

/**
 * <Module> Model
 *
 * @method \<Namespace>\<Module>\Model\Resource\Page _getResource()
 * @method \<Namespace>\<Module>\Model\Resource\Page getResource()
 */
class <Module> extends \Magento\Framework\Model\AbstractModel
{
    public const STATUS_ENABLED = 1;

    public const STATUS_DISABLED = 0;

    public const <MODULE>_STORE_TABLE = '<module>_store';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\<Namespace>\<Module>\Model\ResourceModel\<Module>::class);
    }

    /**
     * Prepare <module>'s statuses
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }

    /**
     * Check for <module> identifier
     *
     * @param string $identifier
     * @param int $storeId
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function checkIdentifier($identifier, $storeId)
    {
        return $this->_getResource()->checkIdentifier($identifier, $storeId);
    }

    /**
     * Validate identifier before saving the entity.
     *
     * @return void
     * @throws LocalizedException
     */
    private function validateNewIdentifier(): void
    {
        $originalIdentifier = $this->getOrigData('url_key');
        $currentIdentifier = $this->getUrlKey();

        if ($this->getId() && $originalIdentifier !== $currentIdentifier) {
            $<module>Id = $this->_getResource()->checkExistingIdentifier($currentIdentifier);
            if ($<module>Id) {
                throw new LocalizedException(
                    __('This url key is reserved for some other <module>.')
                );
            }
        }
    }

    /**
     * Generate url key
     *
     * @return void
     */
    public function generateUrlKey()
    {
        $string = strtolower($this->getData("title"));
        $string = preg_replace("/[^A-Za-z0-9 ]/", '', $string);
        $string = preg_replace('/\s+/', '-', $string);
        $this->setData("url_key", $string);
    }

    /**
     * Process page data before saving
     *
     * @return <Module>
     * @throws LocalizedException
     */
    public function beforeSave()
    {
        if (empty($this->getData("url_key"))) {
            $this->generateUrlKey();
        }
        $this->validateNewIdentifier();
        return parent::beforeSave();
    }
}
