<?php

namespace Rukhsar\GoogleTagManager\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Cookie\Helper\Cookie as CookieHelper;
use Rukhsar\GoogleTagManager\Helper\Data as GoogleTagManagerHelper;

class GoogleTagManagerCode extends Template
{

    protected $_googleTagManagerHelper = null;

    protected $_cookieHelper = null;


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


}