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
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Mageplaza\GiftCard\Api\GiftCodeManagementInterface;
use Mageplaza\GiftCard\Helper\Product;

/**
 * Class CheckCode
 * @package Mageplaza\GiftCardGraphQl\Model\Resolver
 */
class CheckCode implements ResolverInterface
{
    /**
     * @var Product
     */
    private $productHelper;

    /**
     * @var GiftCodeManagementInterface
     */
    private $giftCodeManagement;

    /**
     * CheckCode constructor.
     *
     * @param Product $productHelper
     * @param GiftCodeManagementInterface $giftCodeManagement
     */
    public function __construct(
        Product $productHelper,
        GiftCodeManagementInterface $giftCodeManagement
    ) {
        $this->productHelper      = $productHelper;
        $this->giftCodeManagement = $giftCodeManagement;
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

        try {
            $result = $this->giftCodeManagement->check($args['code']);
        } catch (Exception $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }

        return $result;
    }
}
