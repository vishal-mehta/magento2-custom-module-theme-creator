<?php

namespace <Namespace>\<Module>\Model;

use <Namespace>\<Module>\Model\ResourceModel\<Module>\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;

/**
 * DataProvider for <Module>
 */
class DataProvider extends \Magento\Ui\DataProvider\ModifierPoolDataProvider
{
    /**
     * @var \<Namespace>\<Module>\Model\ResourceModel\<Module>\Collection
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $<module>CollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param StoreManagerInterface $storeManager
     * @param array $meta
     * @param array $data
     * @param PoolInterface|null $pool
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $<module>CollectionFactory,
        DataPersistorInterface $dataPersistor,
        StoreManagerInterface $storeManager,
        array $meta = [],
        array $data = [],
        PoolInterface $pool = null
    ) {
        $this->collection = $<module>CollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->storeManager = $storeManager;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data, $pool);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var \<Namespace>\<Module>\Model\<Module> $<module> */
        foreach ($items as $<module>) {
            $<module>Data = $<module>->getData();
            if ($<module>Data['image']) {
                $baseUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                $img = [];
                $img[0]['image'] = $<module>Data['image'];
                $img[0]['url'] = $baseUrl.'<module>/'. $<module>Data['image'];
                $<module>Data['<module>_image'] = $img;
            }
            $this->loadedData[$<module>->getId()] = $<module>Data;
        }

        $data = $this->dataPersistor->get('<module>');
        if (!empty($data)) {
            $<module> = $this->collection->getNewEmptyItem();
            $<module>->setData($data);
            $this->loadedData[$<module>->getId()] = $<module>->getData();
            $this->dataPersistor->clear('<module>');
        }

        return $this->loadedData;
    }
}
