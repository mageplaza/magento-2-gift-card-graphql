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

use Exception;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Mageplaza\GiftCard\Api\Data\RedeemDetailInterface;

/**
 * Class Redeem
 * @package Mageplaza\GiftCardGraphQl\Model\Resolver\GiftCard
 */
class Redeem extends AbstractResolver
{
    /**
     * @param array $args
     *
     * @return RedeemDetailInterface
     * @throws GraphQlInputException
     */
    protected function handleArgs(array $args)
    {
        try {
            return $this->giftCardManagement->redeem($args['customerId'], $args['code']);
        } catch (Exception $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }
    }
}
