<?php

namespace <Namespace>\<Module>\Block;

/**
 * <Module> page content block
 */
class <Module> extends \Magento\Framework\View\Element\Template
{
    /**
     * @var null
     */
    protected $_<module>Collection = null;

    /**
     * <Module> factory
     *
     * @var \<Namespace>\<Module>\Model\<Module>Factory
     */
    protected $<module>CollectionFactory;

    /** @var \<Namespace>\<Module>\Helper\Data */
    protected $dataHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \<Namespace>\<Module>\Model\ResourceModel\<Module>\CollectionFactory $<module>CollectionFactory
     * @param \<Namespace>\<Module>\Helper\Data $dataHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \<Namespace>\<Module>\Model\ResourceModel\<Module>\CollectionFactory $<module>CollectionFactory,
        \<Namespace>\<Module>\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this-><module>CollectionFactory = $<module>CollectionFactory;
        $this->dataHelper = $dataHelper;
        parent::__construct(
            $context,
            $data
        );
    }

    /**
     *  Prepare layout
     *
     * @return $this|<Module>
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getCollection()) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                '<module>.pager'
            )->setAvailableLimit([5 => 5, 10 => 10, 15 => 15, 20 => 20])
                ->setShowPerPage(true)->setCollection(
                    $this->getCollection()
                );
            $this->setChild('pager', $pager);
            $this->getCollection()->load();
        }
        return $this;
    }

    /**
     * Get a pager block
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Retrieve <module> collection
     *
     * @return <Namespace>\<Module>\Model\ResourceModel\<Module>\Collection
     */
    protected function _getCollection()
    {
        $collection = $this-><module>CollectionFactory->create();
        return $collection;
    }

    /**
     * Retrieve prepared <module> collection
     *
     * @return <Namespace>\<Module>\Model\ResourceModel\<Module>\Collection
     */
    public function getCollection()
    {
        if ($this->_<module>Collection === null) {
            $this->_<module>Collection = $this->_getCollection();
            $this->_<module>Collection->addStoreFilter($this->dataHelper->getStore());
            $this->_<module>Collection->addFieldToFilter("is_active", 1);
            $this->_<module>Collection->setCurPage($this->getCurrentPage());
            $this->_<module>Collection->setPageSize($this->dataHelper->get<Module>PerPage());
            $this->_<module>Collection->setOrder('creation_time', 'asc');
        }

        return $this->_<module>Collection;
    }

    /**
     * Fetch the current page for the <module> list
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->getData('current_page') ? $this->getData('current_page') : 1;
    }

    /**
     * Return URL to item's view page
     *
     * @param <Namespace>\<Module>\Model\<Module> $<module>Item
     * @return string
     */
    public function getItemUrl($<module>Item)
    {
        return $this->getUrl('<module>/' . $<module>Item->getUrlKey());
        /*return $this->getUrl('//view', ['id' => $<module>Item->getId()]);*/
    }

    /**
     * Return URL for resized <Module> Item image
     *
     * @param <Namespace>\<Module>\Model\<Module> $item
     * @param integer $width
     * @return string|false
     */
    public function getImageUrl($item, $width)
    {
        return $this->dataHelper->resize($item, $width);
    }
}
