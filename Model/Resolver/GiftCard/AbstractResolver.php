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
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\QuoteGraphQl\Model\Cart\GetCartForUser;
use Mageplaza\GiftCard\Api\GiftCardManagementInterface;
use Mageplaza\GiftCard\Helper\Data;

/**
 * Class AbstractResolver
 * @package Mageplaza\GiftCardGraphQl\Model\Resolver\GiftCard
 */
abstract class AbstractResolver implements ResolverInterface
{
    /**
     * @var GiftCardManagementInterface
     */
    protected $giftCardManagement;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var GetCartForUser
     */
    private $getCartForUser;

    /**
     * AbstractResolver constructor.
     *
     * @param GiftCardManagementInterface $giftCardManagement
     * @param Data $helper
     * @param GetCartForUser $getCartForUser
     */
    public function __construct(
        GiftCardManagementInterface $giftCardManagement,
        Data $helper,
        GetCartForUser $getCartForUser
    ) {
        $this->giftCardManagement = $giftCardManagement;
        $this->helper             = $helper;
        $this->getCartForUser     = $getCartForUser;
    }

    /**
     * {@inheritDoc}
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!$this->helper->isEnabled()) {
            throw new GraphQlInputException(__('The module is disabled'));
        }

        if (!isset($args['cartId']) || empty($args['cartId'])) {
            throw new GraphQlInputException(__('Required parameter "cartId" is missing'));
        }

        $maskedCartId = $args['cartId'];
        $storeId = (int)$context->getExtensionAttributes()->getStore()->getId();
        $cart = $this->getCartForUser->execute($maskedCartId, $context->getUserId(), $storeId);
        $args['cartId'] = $cart->getId();

        return $this->handleArgs($args, $context);
    }

    /**
     * @param array $args
     * @param ContextInterface $context
     *
     * @return mixed
     */
    abstract protected function handleArgs(array $args, $context);
}
