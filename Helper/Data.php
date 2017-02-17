<?php

namespace Rukhsar\GoogleTagManager\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_TO_ACTIVE = 'googletagmanager/general/active';
    const XML_PATH_TO_ACCOUNT = 'googletagmanager/general/account';


    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;


    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\ObjectManagerInterface
     */
    public function __construct(
            \Magento\Framework\App\Helper\Context $context,
            \Magento\Framework\ObjectManagerInterface $objectManager
    )
    {
        $this->objectManager = $objectManager;
        parent::__construct($context);
    }


    /**
     * Whether Tag Manager is ready to use
     *
     * @return bool
     */
    public function isEnabled()
    {
        $accountId = $this->scopeConfig->getValue(self::XML_PATH_TO_ACCOUNT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        return $accountId && $this->scopeConfig->isSetFlag(self::XML_PATH_TO_ACTIVE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }


    /**
     * Get Tag Manager Account ID
     *
     * @return bool | null | string
     */
    public function getAccountId()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_TO_ACCOUNT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}