<?php

namespace <Namespace>\<Module>\Block;

use Magento\Store\Model\ScopeInterface;

/**
 * <Module> page content block
 */
class View extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Registry|null
     */
    protected $coreRegistry = null;

    /** @var \<Namespace>\<Module>\Helper\Data */
    protected $dataHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \<Namespace>\<Module>\Helper\Data $dataHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Http\Context $httpContext,
        \<Namespace>\<Module>\Helper\Data $dataHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->httpContext = $httpContext;
        $this->dataHelper = $dataHelper;
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     * Prepare layout
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        $<module> = $this->get<Module>();
        $this->_addBreadcrumbs($<module>);
        $this->pageConfig->addBodyClass('<module>-' . $<module>->getUrlKey());
        $metaTitle = $<module>->getMetaTitle();
        $this->pageConfig->getTitle()->set($metaTitle ? $metaTitle : $<module>->getTitle());
        $this->pageConfig->setKeywords($<module>->getMetaKeywords());
        $this->pageConfig->setDescription($<module>->getMetaDescription());
        $this->getLayout()->getBlock('page.main.title')->setPageTitle($this->_escaper->escapeHtml($<module>->getTitle()));
    }

    /**
     * Prepare breadcrumbs
     *
     * @param \<Namespace>\<Module>\Model\<Module> $<module>
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function _addBreadcrumbs(\<Namespace>\<Module>\Model\<Module> $<module>)
    {
        if ($this->_scopeConfig->getValue('web/default/show_cms_breadcrumbs', ScopeInterface::SCOPE_STORE)
            && ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs'))
        ) {
            $breadcrumbsBlock->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->storeManager->getStore()->getBaseUrl()
                ]
            );
            $breadcrumbsBlock->addCrumb('cms_page', ['label' => $<module>->getTitle(), 'title' => $<module>->getTitle()]);
        }
    }

    /**
     * Retrieve current order model instance
     *
     * @return \<Namespace>\<Module>\Model\<Module>
     */
    public function get<Module>()
    {
        return $this->coreRegistry->registry('current_<module>');
    }

    /**
     * Return back url for logged in and guest users
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('<module>/index/index');
    }

    /**
     * Return back title for logged in and guest users
     *
     * @return string
     */
    public function getBackTitle()
    {
        if ($this->httpContext->getValue(Context::CONTEXT_AUTH)) {
            return __('Back to My Orders');
        }
        return __('View Another Order');
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
