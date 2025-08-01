<?php

namespace <Namespace>\<Module>\Controller;

use Magento\Framework\App\RouterInterface;

/**
 * <Module> Router Controller
 */
class Router implements RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @var \<Namespace>\<Module>\Model\<Module>Factory
     */
    protected $<module>Factory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $response;

    /**
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\UrlInterface $url
     * @param \<Namespace>\<Module>\Model\<Module>Factory $<module>Factory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\ResponseInterface $response
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\UrlInterface $url,
        \<Namespace>\<Module>\Model\<Module>Factory $<module>Factory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResponseInterface $response
    ) {
        $this->actionFactory = $actionFactory;
        $this->eventManager = $eventManager;
        $this->url = $url;
        $this-><module>Factory = $<module>Factory;
        $this->storeManager = $storeManager;
        $this->response = $response;
    }

    /**
     * Validate and Match <Module> and modify request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ActionInterface|null
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $identifier = trim($request->getPathInfo(), "/");
        $identifier = explode('/', $identifier);
        $identifier = array_pop($identifier);

        $condition = new \Magento\Framework\DataObject(['identifier' => $identifier, 'continue' => true]);
        $identifier = $condition->getIdentifier();

        $<module> = $this-><module>Factory->create();
        $<module>Id = $<module>->checkIdentifier($identifier, $this->storeManager->getStore()->getId());
        if (!$<module>Id) {
            return null;
        }
        $request->setModuleName('<module>')->setControllerName('index')->setActionName('view')->setParam('id', $<module>Id);
        $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);

        return $this->actionFactory->create(\Magento\Framework\App\Action\Forward::class);
    }
}
