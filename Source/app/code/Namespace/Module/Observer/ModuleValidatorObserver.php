<?php

namespace <Namespace>\<Module>\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * <Module> before save observer
 */
class <Module>ValidatorObserver implements ObserverInterface
{
    /**
     * @inheritDoc
     *
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        $<module> = $observer->getEvent()->getData('<module>');
        $<module>->beforeSave();
    }
}
