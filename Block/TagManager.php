<?php

namespace Rukhsar\GoogleTagManager\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Cookie\Helper\Cookie as CookieHelper;
use Rukhsar\GoogleTagManager\Helper\Data as GoogleTagManagerHelper;

class TagManager extends Template
{
    protected $_googleTagManagerHelper = null;

    protected $_cookieHelper = null;

    protected $_dataLayerModel = null;

    protected $_salesOrderCollection;

    private $_orderCollection;

    protected $_customVariables = array();

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
}