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
use Mageplaza\GiftCard\Helper\Product as ProductHelper;
use Mageplaza\GiftCard\Model\CreditFactory;

/**
 * Class SaveNotificationSettings
 * @package Mageplaza\GiftCardGraphQl\Model\Resolver
 */
class SaveNotificationSettings implements ResolverInterface
{
    /**
     * @var ProductHelper
     */
    private $productHelper;

    /**
     * @var GetCustomer
     */
    private $getCustomer;

    /**
     * @var CreditFactory
     */
    private $creditFactory;

    /**
     * SaveNotificationSettings constructor.
     *
     * @param ProductHelper $productHelper
     * @param GetCustomer $getCustomer
     * @param CreditFactory $creditFactory
     */
    public function __construct(
        ProductHelper $productHelper,
        GetCustomer $getCustomer,
        CreditFactory $creditFactory
    ) {
        $this->productHelper = $productHelper;
        $this->getCustomer   = $getCustomer;
        $this->creditFactory = $creditFactory;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!$this->productHelper->isEnabled()) {
            throw new GraphQlInputException(__('The module is disabled'));
        }
        /** @var ContextInterface $context */
        if ($context->getExtensionAttributes()->getIsCustomer() === false) {
            throw new GraphQlAuthorizationException(__('The current customer isn\'t authorized.'));
        }

        if (!isset($args['credit_notification']) && !isset($args['giftcard_notification'])) {
            throw new GraphQlInputException(
                __('At least one of the two values [credit_notification, giftcard_notification] should be filled in')
            );
        }

        $customer = $this->getCustomer->execute($context);

        try {
            $customerCredit = $this->creditFactory->create()->load($customer->getId(), 'customer_id');
            if (!$customerCredit->getId()) {
                $customerCredit->setCustomerId($customer->getId());
            }

            if (isset($args['credit_notification'])) {
                $customerCredit->addData(['credit_notification' => $args['credit_notification']]);
            }

            if (isset($args['giftcard_notification'])) {
                $customerCredit->addData(['credit_notification' => $args['giftcard_notification']]);
            }

            $customerCredit->save();
        } catch (Exception $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }

        return true;
    }
}
