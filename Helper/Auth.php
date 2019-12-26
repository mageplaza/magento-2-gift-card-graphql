<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_GiftCardGraphQl
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GiftCardGraphQl\Helper;

use Magento\Framework\Authorization\PolicyInterface;
use Magento\Integration\Api\IntegrationServiceInterface;
use Magento\Integration\Model\Oauth\TokenFactory;

/**
 * Class Auth
 * @package Mageplaza\GiftCardGraphQl\Helper
 */
class Auth
{
    /**
     * @var PolicyInterface
     */
    private $aclPolicy;

    /**
     * @var TokenFactory
     */
    private $tokenFactory;

    /**
     * @var IntegrationServiceInterface
     */
    private $integrationService;

    /**
     * Auth constructor.
     *
     * @param PolicyInterface $aclPolicy
     * @param TokenFactory $tokenFactory
     * @param IntegrationServiceInterface $integrationService
     */
    public function __construct(
        PolicyInterface $aclPolicy,
        TokenFactory $tokenFactory,
        IntegrationServiceInterface $integrationService
    ) {
        $this->aclPolicy          = $aclPolicy;
        $this->tokenFactory       = $tokenFactory;
        $this->integrationService = $integrationService;
    }

    /**
     * @param string $accessToken
     *
     * @return string
     */
    private function getAclRoleId($accessToken)
    {
        $token = $this->tokenFactory->create()->loadByToken($accessToken);

        return $this->integrationService->findByConsumerId($token->getConsumerId())->getId();
    }

    /**
     * @param string $accessToken
     * @param string $resource
     * @param string $privilege
     *
     * @return bool
     */
    public function isAllowed($accessToken, $resource, $privilege = null)
    {
        return $this->aclPolicy->isAllowed($this->getAclRoleId($accessToken), $resource, $privilege);
    }
}
