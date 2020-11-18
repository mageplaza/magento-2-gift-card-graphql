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

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;

/**
 * Class RemoveGiftCode
 * @package Mageplaza\GiftCardGraphQl\Model\Resolver\GiftCard
 */
class RemoveGiftCode extends AbstractResolver
{
    /**
     * @param array $args
     * @param ContextInterface $context
     *
     * @return bool
     * @throws GraphQlInputException
     */
    protected function handleArgs(array $args, $context)
    {
        try {
            return $this->giftCardManagement->remove($args['cartId'], $args['code']);
        } catch (CouldNotDeleteException $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        } catch (NoSuchEntityException $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }
    }
}
