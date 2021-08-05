<?php
namespace CreditKey\B2BGateway\Model\Adminhtml\Source;

/**
 * Prices Source
 * This is the source of radio options for marketing product prices filters
 */
class Prices implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $options = [
            ['value' => "0", 'label' => 'All Prices'],
            ['value' => "1000", 'label' => '$1,000 and under'],
            ['value' => "3000", 'label' => '$3,000 and under'],
            ['value' => "5000", 'label' => '$5,000 and under'],
            ['value' => "10000", 'label' => '$10,000 and under']
        ];

        return $options;
    }
}
