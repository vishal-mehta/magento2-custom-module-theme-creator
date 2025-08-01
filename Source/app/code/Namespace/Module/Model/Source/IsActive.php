<?php

namespace <Namespace>\<Module>\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * IsActive option for <Module>
 */
class IsActive implements OptionSourceInterface
{
    /**
     * @var \<Namespace>\<Module>\Model\<Module>
     */
    protected $<module>;

    /**
     * Constructor
     *
     * @param \<Namespace>\<Module>\Model\<Module> $<module>
     */
    public function __construct(\<Namespace>\<Module>\Model\<Module> $<module>)
    {
        $this-><module> = $<module>;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = $this-><module>->getAvailableStatuses();
        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
