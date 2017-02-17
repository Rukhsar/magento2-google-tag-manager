<?php

namespace Rukhsar\GoogleTagManager\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Cookie\Helper\Cookie as CookieHelper;
use Rukhsar\GoogleTagManager\Helper\Data as GoogleTagManagerHelper;

/**
 * Google Tag Manager Page Block
 */
class GoogleTagManagerCode extends Template
{

    /**
     * Google Tag Manager data
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
     * @param Context $context
     * @param CookieHelper $cookieHelper
     * @param GoogleTagManagerHelper $googleTagManagerHelper
     * @param array $data
     */
    public function __construct(
                        Context $context,
                        CookieHelper $cookieHelper,
                        GoogleTagManagerHelper $googleTagManagerHelper,
                        array $data = []
    )
    {
        $this->_cookieHelper = $cookieHelper;
        $this->_googleTagManagerHelper = $googleTagManagerHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get Account Id
     *
     * @return string
     */
    public function getAccountId()
    {
        return $this->_googleTagManagerHelper->getAccountId();
    }

    /**
     * Render tag manager JS
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_googleTagManagerHelper->isEnabled())
        {
            return '';
        }
        return parent::_toHtml();
    }

}