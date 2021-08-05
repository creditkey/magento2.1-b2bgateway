<?php
namespace CreditKey\B2BGateway\Controller\Order;

use Magento\Framework\Controller\ResultFactory;

/**
 * Create Order Controller
 */
class Create extends \CreditKey\B2BGateway\Controller\AbstractCreditKeyController
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * Construct
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \CreditKey\B2BGateway\Helper\Api $creditKeyApi
     * @param \CreditKey\B2BGateway\Helper\Data $creditKeyData
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \CreditKey\B2BGateway\Helper\Api $creditKeyApi,
        \CreditKey\B2BGateway\Helper\Data $creditKeyData,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->urlBuilder = $context->getUrl();
        $this->request = $request;

        parent::__construct(
            $context,
            $creditKeyApi,
            $creditKeyData,
            $customerUrl,
            $checkoutSession,
            $customerSession,
            $logger
        );
    }

    /**
     * Execute Create order action
     *
     * @return \Magento\Framework\Controller\Result\Redirect|$this
     */
    public function execute()
    {
        $quote = $this->checkoutSession->getQuote();

        $cartContents = $this->creditKeyData->buildCartContents($quote);
        $billingAddress = $this->creditKeyData->buildAddress($quote->getBillingAddress());
        $shippingAddress = $this->creditKeyData->buildAddress($quote->getShippingAddress());
        $charges = $this->creditKeyData->buildCharges($quote);

        // need to use this id to reference the quote when completing the order
        $remoteId = $quote->getId();
        $customerId = null;
        if ($this->customerSession->isLoggedIn()) {
            $customerId = $this->customerSession->getCustomer()->getId();
        }

        $returnUrl = $this->urlBuilder->getUrl(
            'creditkey_gateway/order/complete',
            [
                'ref' => $remoteId,
                'key' => '%CKKEY%',
                '_secure' => true
            ]
        );
        $cancelUrl = $this->urlBuilder->getUrl('creditkey_gateway/order/cancel');

        $this->creditKeyApi->configure();

        $mode = 'redirect';
        if ($this->request->getParam('modal')) {
            $mode = 'modal';
        }

        try {
            $redirectTo = \CreditKey\Checkout::beginCheckout(
                $cartContents,
                $billingAddress,
                $shippingAddress,
                $charges,
                $remoteId,
                $customerId,
                $returnUrl,
                $cancelUrl,
                $mode
            );

            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($redirectTo);
            return $resultRedirect;
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $this->messageManager->addErrorMessage(__('CREDIT_KEY_UNAVAILABLE'));
            $this->redirect('checkout');
            return $this;
        }
    }
}
