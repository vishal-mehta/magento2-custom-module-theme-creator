<?php

namespace <Namespace>\<Module>\Controller\Index;

use <Namespace>\<Module>\Model\<Module>Factory;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\App\Action;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

/**
 * <Module> View Controller
 */
class View extends Action\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var <Module>Factory
     */
    protected $<module>Factory;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var FilterProvider
     */
    protected $filterProvider;

    /**
     * @param Action\Context $context
     * @param <Module>Factory $<module>Factory
     * @param PageFactory $resultPageFactory
     * @param Registry $registry
     * @param FilterProvider $filterProvider
     */
    public function __construct(
        Action\Context $context,
        <Module>Factory $<module>Factory,
        PageFactory $resultPageFactory,
        Registry $registry,
        FilterProvider $filterProvider
    ) {
        $this-><module>Factory = $<module>Factory;
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
        $this->filterProvider = $filterProvider;
        parent::__construct($context);
    }

    /**
     * <Module> Detail page
     *
     * @return \Magento\Framework\View\Result\Page|void
     */
    public function execute()
    {
        $id = (int) $this->_request->getParam('id');
        if (!$<module> = $this-><module>Factory->create()->load($id)) {
            return;
        }
        $<module>->setContent($this->filterProvider->getPageFilter()->filter($<module>->getContent()));
        $this->registry->register('current_<module>', $<module>);

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }
}
