<?php

namespace <Namespace>\<Module>\Controller\Adminhtml\Index;

use <Namespace>\<Module>\Model\<Module>;
use <Namespace>\<Module>\Model\<Module>Factory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\LocalizedException;

/**
 * <Module> Save Controller
 */
class Save extends \Magento\Backend\App\Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = '<Namespace>_<Module>::save';

    /**
     * @var <Module>Factory
     */
    protected $<module>Factory;

    /**
     * @var \<Namespace>\<Module>\<Module>ImageUpload
     */
    protected $imageUploader;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @param Context $context
     * @param <Module>Factory $<module>Factory
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        Context $context,
        <Module>Factory $<module>Factory,
        ResourceConnection $resourceConnection
    ) {
        parent::__construct($context);
        $this->resourceConnection = $resourceConnection;
        $this-><module>Factory = $<module>Factory;
    }

    /**
     * Check admin permissions for this controller
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }

    /**
     * Execute action
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface|mixed
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if (!$this->getRequest()->isPost()) {
            return $resultRedirect->setPath('*/*/index');
        }

        $params = $this->getRequest()->getPostValue();
        if (isset($params['<module>_image']) && isset($params['<module>_image'][0]['tmp_name'])) {
            $params['image'] = $params['<module>_image'][0]['name'];
            $this->imageUploader = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \<Namespace>\<Module>\<Module>ImageUpload::class
            );
            $this->imageUploader->moveFileFromTmp($params['image']);
        } elseif (isset($params['<module>_image'][0]['image']) && !isset($params['<module>_image'][0]['tmp_name'])) {
            $params['image'] = $params['<module>_image'][0]['image'];
        } else {
            $params['image'] = null;
        }

        if (empty($params['<module>_id'])) {
            $params['<module>_id'] = null;
        }

        try {
            $model = $this-><module>Factory->create()->load($params['<module>_id']);
            $model->setData($params);

            $this->_eventManager->dispatch(
                '<module>_prepare_save',
                ['<module>' => $model, 'request' => $this->getRequest()]
            );

            $model->save();

            if (isset($params['store_id']) && is_array($params['store_id'])) {
                $connection = $this->resourceConnection->getConnection();
                $connection->delete(<Module>::<MODULE>_STORE_TABLE, ["<module>_id = ?" => $model['<module>_id']]);

                $params['store'] = array_unique($params['store_id']);
                $data = [];
                foreach ($params['store'] as $key => $value) {
                    $data[] = [
                        "<module>_id" => $model->getId(),
                        "store_id" => $value
                    ];
                }
                $connection->insertMultiple(<Module>::<MODULE>_STORE_TABLE, $data);
            }

            $this->messageManager->addSuccessMessage(__('You saved the data.'));
            return $this->processResultRedirect($model, $resultRedirect, $params);
        } catch (LocalizedException $e) {
            $this->messageManager->addExceptionMessage($e->getPrevious() ?: $e);
        } catch (\Throwable $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong while saving the data.'));
        }

        return $resultRedirect->setPath('*/*/edit', ['<module>_id' => $this->getRequest()->getParam('<module>_id')]);
    }

    /**
     * Process result redirect
     *
     * @param <Module> $model
     * @param Redirect $resultRedirect
     * @param array $params
     * @return mixed
     * @throws \Exception
     */
    private function processResultRedirect($model, $resultRedirect, $params)
    {
        $redirect = $params['back'];

        if ($redirect == "continue") {
            $resultRedirect->setPath('*/*/edit', ['<module>_id' => $model->getId(), '_current' => true]);
        } elseif ($redirect == "close") {
            $resultRedirect->setPath('*/*/');
        } elseif ($redirect == "duplicate") {
            $newModel = $this-><module>Factory->create(['data' => $params]);
            $newModel->setId(null);
            $identifier = $model->getUrlKey() . '-' . uniqid();
            $newModel->setUrlKey($identifier);
            $newModel->setIsActive(false);
            $newModel->save();

            $this->messageManager->addSuccessMessage(__('You duplicated the data.'));
            return $resultRedirect->setPath(
                '*/*/edit',
                [
                    '<module>_id' => $newModel->getId(),
                    '_current' => true,
                ]
            );
        }
        return $resultRedirect;
    }
}
