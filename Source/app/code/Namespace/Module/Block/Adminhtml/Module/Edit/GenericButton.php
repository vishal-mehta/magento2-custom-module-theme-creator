<?php

namespace <Namespace>\<Module>\Block\Adminhtml\<Module>\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * GenericButton block
 */
class GenericButton
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        $this->context = $context;
    }

    /**
     * Return CMS block ID
     *
     * @return int|null
     */
    public function get<Module>Id()
    {
        try {
            if ($this->context->getRequest()->getParam('<module>_id')) {
                return $this->context->getRequest()->getParam('<module>_id');
            } else {
                return null;
            }
        } catch (NoSuchEntityException $e) {
            throw new Exception(__('Unable to find requested entity.'));
        }
        return null;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
