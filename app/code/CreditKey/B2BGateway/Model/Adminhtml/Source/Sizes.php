<?php
namespace CreditKey\B2BGateway\Model\Adminhtml\Source;

/**
 * Sizes Source
 * This is the source of radio options for marketing content sizes
 */
class Sizes implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $options = [
            ['value' => "small", 'label' => 'Small'],
            ['value' => "medium", 'label' => 'Medium'],
            ['value' => "large", 'label' => 'Large']
        ];

        return $options;
    }
}
