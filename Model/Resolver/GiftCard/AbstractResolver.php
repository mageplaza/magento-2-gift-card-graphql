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

declare(strict_types=1);

namespace Mageplaza\GiftCardGraphQl\Model\Resolver\GiftCard;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Mageplaza\GiftCard\Api\GiftCardManagementInterface;
use Mageplaza\GiftCard\Helper\Data;
use Mageplaza\GiftCardGraphQl\Helper\Auth;

/**
 * Class AbstractResolver
 * @package Mageplaza\GiftCardGraphQl\Model\Resolver\GiftCard
 */
abstract class AbstractResolver implements ResolverInterface
{
    /**
     * @var string
     */
    protected $_aclResource = 'Mageplaza_GiftCard::code';

    /**
     * @var GiftCardManagementInterface
     */
    protected $giftCardManagement;

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var Auth
     */
    private $auth;

    /**
     * AbstractResolver constructor.
     *
     * @param GiftCardManagementInterface $giftCardManagement
     * @param Data $helper
     * @param Auth $auth
     */
    public function __construct(
        GiftCardManagementInterface $giftCardManagement,
        Data $helper,
        Auth $auth
    ) {
        $this->giftCardManagement = $giftCardManagement;
        $this->helper             = $helper;
        $this->auth               = $auth;
    }

    /**
     * {@inheritDoc}
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!$this->helper->isEnabled()) {
            throw new GraphQlInputException(__('The module is disabled'));
        }

        if (!$this->auth->isAllowed($args['accessToken'], $this->_aclResource)) {
            throw new GraphQlInputException(__("The consumer isn't authorized to access %1", $this->_aclResource));
        }

        return $this->handleArgs($args);
    }

    /**
     * @param array $args
     *
     * @return mixed
     */
    abstract protected function handleArgs(array $args);
}
