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

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Mageplaza\GiftCard\Helper\Data;
use Mageplaza\GiftCard\Plugin\Quote\CartTotalRepository;

/**
 * Class GiftCardConfig
 * @package Mageplaza\GiftCardGraphQl\Model\Resolver
 */
class GiftCardConfig implements ResolverInterface
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var CartTotalRepository
     */
    private $cartTotalRepository;

    /**
     * GiftCardConfig constructor.
     *
     * @param Data $helper
     * @param CartRepositoryInterface $quoteRepository
     * @param CartTotalRepository $cartTotalRepository
     */
    public function __construct(
        Data $helper,
        CartRepositoryInterface $quoteRepository,
        CartTotalRepository $cartTotalRepository
    ) {
        $this->helper              = $helper;
        $this->quoteRepository     = $quoteRepository;
        $this->cartTotalRepository = $cartTotalRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!$this->helper->isEnabled()) {
            throw new GraphQlInputException(__('The module is disabled'));
        }

        /** @var Quote $quote */
        $quote = $this->quoteRepository->get($value['model']->getId());

        return $this->cartTotalRepository->getGiftCardConfig($quote, true);
    }
}
