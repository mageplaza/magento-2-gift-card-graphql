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

namespace Mageplaza\GiftCardGraphQl\Model\Resolver;

use Exception;
use Magento\CustomerGraphQl\Model\Customer\GetCustomer;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\GraphQl\Model\Query\ContextInterface;
use Mageplaza\GiftCard\Api\GiftCardManagementInterface;
use Mageplaza\GiftCard\Helper\Product;

/**
 * Class AddList
 * @package Mageplaza\GiftCardGraphQl\Model\Resolver
 */
class AddList implements ResolverInterface
{
    /**
     * @var GetCustomer
     */
    private $getCustomer;

    /**
     * @var Product
     */
    private $productHelper;

    /**
     * @var GiftCardManagementInterface
     */
    private $giftCardManagement;

    /**
     * AddList constructor.
     *
     * @param GetCustomer $getCustomer
     * @param Product $productHelper
     * @param GiftCardManagementInterface $giftCardManagement
     */
    public function __construct(
        GetCustomer $getCustomer,
        Product $productHelper,
        GiftCardManagementInterface $giftCardManagement
    ) {
        $this->getCustomer        = $getCustomer;
        $this->productHelper      = $productHelper;
        $this->giftCardManagement = $giftCardManagement;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (!$this->productHelper->isEnabled()) {
            throw new GraphQlInputException(__('The module is disabled'));
        }
        /** @var ContextInterface $context */
        if ($context->getExtensionAttributes()->getIsCustomer() === false) {
            throw new GraphQlAuthorizationException(__('The current customer isn\'t authorized.'));
        }

        $customer = $this->getCustomer->execute($context);
        try {
            return $this->giftCardManagement->addList($customer->getId(), $args['code']);
        } catch (Exception $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }
    }
}
