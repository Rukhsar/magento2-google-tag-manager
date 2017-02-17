<?php

namespace Rukhsar\GoogleTagManager\Model;

use Magento\Framework\DataObject;

class DataLayer extends DataObject
{

    protected $_quote = null;

    protected $_variables = [];

    protected $_customerSession;

    protected $_scopeConfig;

    protected $_context;

    protected $_coreRegistry = null;

    protected $_checkoutSession;

    protected $_fullActionName;

    public function __construct(
        \Magento\Framework\App\Action\Context  $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Registry $registry
    )
    {
        $this->_scopeConfig = $scopeConfig;
        $this->_customerSession = $customerSession;
        $this->_context = $context;
        $this->_coreRegistry = $registry;
        $this->_checkoutSession = $checkoutSession;

        $this->fullActionName = $this->_context->getRequest()->getFullActionName();

        $this->addVariable('pageType', $this->fullActionName);
        $this->addVariable('list', 'other');

        $this->setCustomerDataLayer();
        $this->setProductDataLayer();
        $this->setCategoryDataLayer();
        $this->setCartDataLayer();

    }

    public function getVariables() {
        return $this->_variables;
    }

    public function addVariable($name, $value) {
        if (!empty($name)) {
            $this->_variables[$name] = $value;
        }
        return $this;
    }

    protected function setCategoryDataLayer() {
        if($this->fullActionName === 'catalog_category_view'
            && $_category = $this->_coreRegistry->registry('current_category')
        ) {
            $category = [
                'id' => $_category->getId(),
                'name' => $_category->getName(),
            ];
            $this->addVariable('category', $category);
            $this->addVariable('list', 'category');
        }
        return $this;
    }

    protected function setProductDataLayer() {
        if($this->fullActionName === 'catalog_product_view'
            && $_product = $this->_coreRegistry->registry('current_product')
        ) {
            $this->addVariable('list', 'detail');
            $product = [
                'id' => $_product->getId(),
                'sku' => $_product->getSku(),
                'name' => $_product->getName(),
            ];
            $this->addVariable('product', $product);
        }
        return $this;
    }

    protected function setCustomerDataLayer() {
        $customer = [];
        if ($this->_customerSession->isLoggedIn()) {
            $customer['isLoggedIn'] = true;
            $customer['id'] = $this->_customerSession->getCustomerId();
            $customer['groupId'] = $this->_customerSession->getCustomerGroupId();
        } else {
            $customer['isLoggedIn'] = false;
        }

        $this->addVariable('customer', $customer);
        return $this;
    }

    protected function setCartDataLayer() {
        if($this->fullActionName === 'checkout_index_index'){
            $this->addVariable('list', 'cart');
        }

        $quote = $this->getQuote();
        $cart = [];

        $cart['hasItems'] = false;
        if ($quote->getItemsCount()) {
            $items = [];
            // set items
            foreach($quote->getAllVisibleItems() as $item){
                $items[] = [
                    'sku' => $item->getSku(),
                    'name' => $item->getName(),
                    'price' => $item->getPrice(),
                    'quantity' => $item->getQty(),
                ];
            }

            if(count($items) > 0){
                $cart['hasItems'] = true;
                $cart['items'] = $items;
            }

            $cart['total'] = $quote->getGrandTotal();
            $cart['itemCount'] = $quote->getItemsCount();


            //set coupon code
            $coupon = $quote->getCouponCode();

            $cart['hasCoupons'] = $coupon ? true : false;
            if($coupon){
                $cart['couponCode'] = $coupon;
            }
        }

        $this->addVariable('cart', $cart);

        return $this;
    }


    public function getQuote()
    {
        if (null === $this->_quote) {
            $this->_quote = $this->_checkoutSession->getQuote();
        }
        return $this->_quote;
    }


    public function formatPrice($price){
        return sprintf('%.2F', $price);
    }

}

