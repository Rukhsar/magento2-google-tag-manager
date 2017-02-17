<?php

namespace Rukhsar\GoogleTagManager\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Cookie\Helper\Cookie as CookieHelper;
use Rukhsar\GoogleTagManager\Helper\Data as GoogleTagManagerHelper;

/**
 * Google Tag Manager Block
 */
class TagManager extends Template
{
    /**
     * Google Tag Manager Helper
     *
     * @var \Rukhsar\GoogleTagManager\Helper\Data
     */
    protected $_googleTagManagerHelper = null;

    /**
     * Cookie Helper
     *
     * @var \Magento\Cookie\Helper\Cookie
     */
    protected $_cookieHelper = null;

    /**
     * Cookie Helper
     *
     * @var \Rukhsar\GoogleTagManager\Model\DataLayer
     */
    protected $_dataLayerModel = null;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $_salesOrderCollection;

    private $_orderCollection;

    protected $_customVariables = array();

    /**
     * @param Context $context
     * @param CookieHelper $cookieHelper
     * @param GoogleTagManagerHelper $googleTagManagerHelper
     * @param \Rukhsar\GoogleTagManager\Model\DataLayer $dataLayer
     * @param array $data
     */
    public function __construct(
        Context $context,
        GoogleTagManagerHelper $googleTagManagerHelper,
        CookieHelper $cookieHelper,
        \Rukhsar\GoogleTagManager\Model\DataLayer $dataLayer,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollection,
        array $data = []
    )
    {
        $this->_cookieHelper = $cookieHelper;
        $this->_googleTagManagerHelper = $googleTagManagerHelper;
        $this->_dataLayerModel = $dataLayer;
        $this->_salesOrderCollection = $salesOrderCollection;
        $this->_isScopePrivate = true;
        parent::__construct($context, $data);
    }

    /**
     * Render information about specified orders and their items
     *
     * @return void|string
     */
    public function getOrdersTrackingCode()
    {
        $collection = $this->getOrderCollection();

        if (!$collection)
        {
            return;
        }

        $result = [];

        foreach ($collection as $order)
        {
            foreach ($order->getAllVisibleItems() as $item)
            {
                $product[] = array(
                    'sku'       =>  $item->getSku(),
                    'name'      =>  $item->getName(),
                    'price'     =>  $item->getBasePrice(),
                    'quantity'  =>  $item->getQtyOrdered()
                );
            }

            $transaction = array(
                'transactionId' => $order->getIncrementId(),
                'transactionAffiliation' => $this->escapeJsQuote($this->_storeManager->getStore()->getFrontendName()),
                'transactionTotal' => $order->getBaseGrandTotal(),
                'transactionSubTotal' => $order->getBaseSubtotal(),
                'transactionShipping' => $order->getBaseShippingAmount(),
                'transactionTax' => $order->getTaxAmount(),
                'transactionCouponCode' => $order->getCouponCode(),
                'transactionDiscount' => $order->getDiscountAmount(),
                'transactionProducts' => $product
            );

            $result[] = sprintf("dataLayer.push(%s);", json_encode($transaction));

        }

        return implode("\n", $result) . "\n";
    }

    /**
     * Render tag manager script
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->_cookieHelper->isUserNotAllowSaveCookie() || !$this->_googleTagManagerHelper->isEnabled())
        {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * Return data layer json
     *
     * @return json
     */
    public function getGtmTrackingCode()
    {

        $this->_eventManager->dispatch('rukhsar_datalayer',['dataLayer' => $this]);
        $result = [];
        $result[] = sprintf("dataLayer.push(%s);\n", json_encode($this->_dataLayerModel->getVariables()));

        if(!empty($this->_customVariables) && is_array($this->_customVariables)){

            foreach($this->_customVariables as $custom){
                $result[] = sprintf("dataLayer.push(%s);\n", json_encode($custom));
            }
        }

        return implode("\n", $result) . "\n";
    }

    /**
     * Add variable to the default data layer
     *
     * @return $this
     */
    public function addVariable($name, $value)
    {
        $this->_dataLayerModel->addVariable($name, $value);

        return $this;
    }

    /**
     * Add variable to the custom push data layer
     *
     * @return $this
     */
    public function addCustomVariable($name, $value = null)
    {
        if(is_array($name))
        {
            $this->_customVariables[] = $name;
        }
        else
        {
            $this->_customVariables[] = [$name => $value];
        }

        return $this;
    }

    /**
     * Format Price
     *
     * @return float
     */
    public function formatPrice($price)
    {
        return $this->_dataLayerModel->formatPrice($price);
    }

    /**
     * Get order collection
     *
     * @return $this
     */
    public function getOrderCollection()
    {
        $orderIds = $this->getOrderIds();

        if (empty($orderIds) || !is_array($orderIds))
        {
            return;
        }

        if (!$this->_orderCollection)
        {
            $this->_orderCollection = $this->_salesOrderCollection->create();
            $this->_orderCollection->addFieldToFilter('entity_id',['in' =>  $orderIds]);
        }

        return $this->_orderCollection;
    }

}