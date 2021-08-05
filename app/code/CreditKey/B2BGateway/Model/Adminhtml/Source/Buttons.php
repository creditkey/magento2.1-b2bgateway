<?php
namespace CreditKey\B2BGateway\Model\Adminhtml\Source;

/**
 * Buttons Source
 * This is the source of radio options for marketing types
 * The labels will be replaced in JS by the visual representation of the option
 */
class Buttons implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $options = [
            ['value' => "text", 'label' => 'Text'],
            ['value' => "button", 'label' => 'Button']
        ];

        return $options;
    }
}
