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

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\GraphQl\Model\Query\ContextInterface;

/**
 * Class SetGiftCredit
 * @package Mageplaza\GiftCardGraphQl\Model\Resolver\GiftCard
 */
class SetGiftCredit extends AbstractResolver
{
    /**
     * @param array $args
     *
     * @return bool
     * @throws GraphQlInputException|GraphQlAuthorizationException
     */
    protected function handleArgs(array $args)
    {
        /** @var ContextInterface $context */
        if ($context->getExtensionAttributes()->getIsCustomer() === false) {
            throw new GraphQlAuthorizationException(__('The current customer isn\'t authorized.'));
        }

        try {
            return $this->giftCardManagement->credit($args['cartId'], $args['amount']);
        } catch (CouldNotSaveException $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        } catch (NoSuchEntityException $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }
    }
}
