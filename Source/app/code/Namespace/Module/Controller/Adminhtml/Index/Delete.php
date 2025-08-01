<?php

namespace <Namespace>\<Module>\Controller\Adminhtml\Index;

use <Namespace>\<Module>\Model\<Module>;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;

/**
 * <Module >Delete Controller
 */
class Delete extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = '<Namespace>_<Module>::<module>_delete';

    /**
     * @var <Module>
     */
    protected $<module>;

    /**
     * @param Action\Context $context
     * @param <Module> $<module>
     * @return void
     */
    public function __construct(
        Action\Context $context,
        <Module> $<module>,
    ) {
        $this-><module> = $<module>;
        parent::__construct($context);
    }

    /**
     * Delete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('<module>_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($id) {
            try {
                $model = $this-><module>;
                $model->load($id);
                $model->delete();

                $this->messageManager->addSuccessMessage(__('The <module> has been deleted.'));

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }

        $this->messageManager->addErrorMessage(__('We can\'t find a <module> to delete.'));

        return $resultRedirect->setPath('*/*/');
    }
}
