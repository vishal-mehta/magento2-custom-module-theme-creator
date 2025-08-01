<?php

namespace <Namespace>\<Module>\Controller\Index;

use Magento\Framework\View\Result\PageFactory;

/**
 * <Module> List Controller
 */
class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * <Module> Index page
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->getPage()->getConfig()->getTitle()->set(__('<Module>'));
        $listBlock = $this->_view->getLayout()->getBlock('<module>.list');

        if ($listBlock) {
            $currentPage = abs((int)$this->getRequest()->getParam('p'));
            if ($currentPage < 1) {
                $currentPage = 1;
            }

            $listBlock->setCurrentPage($currentPage);
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }
}
