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

namespace Mageplaza\GiftCardGraphQl\Plugin\QuoteGraphQl\Model\Resolver;

use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\TotalsCollector;
use Magento\QuoteGraphQl\Model\Resolver\CartPrices as SourceCartPrices;
use Mageplaza\GiftCard\Helper\Checkout as GiftCardCheckoutHelper;

/**
 * Class CartPrices
 * @package Mageplaza\GiftCardGraphQl\Plugin\QuoteGraphQl\Model\Resolver
 */
class CartPrices
{
    /**
     * @var TotalsCollector
     */
    private $totalsCollector;

    /**
     * @param TotalsCollector $totalsCollector
     */
    public function __construct(
        TotalsCollector $totalsCollector
    ) {
        $this->totalsCollector = $totalsCollector;
    }

    /**
     * @param SourceCartPrices $subject
     * @param array $result
     * @param mixed ...$arg
     *
     * @return array
     */
    public function afterResolve(
        SourceCartPrices $subject,
        $result,
        ...$arg
    ) {
        /** @var Quote $quote */
        $quote      = $arg[3]['model'];
        $cartTotals = $this->totalsCollector->collectQuoteTotals($quote);
        $currency   = $quote->getQuoteCurrencyCode();

        $giftCards     = GiftCardCheckoutHelper::jsonDecode($quote->getMpGiftCards());
        $giftCardsData = [];
        foreach ($giftCards as $key => $giftCard) {
            $giftCardsData[] = [
                'code'  => $key,
                'value' => $giftCard
            ];
        }
        $result['gift_card'] = [
            'value'      => $cartTotals->getGiftCardAmount(),
            'gift_cards' => $giftCardsData,
            'currency' => $currency
        ];

        $gcCredit = (float) $quote->getGcCredit();
        $result['gift_credit'] = [
            'value'      => -$gcCredit,
            'currency' => $currency
        ];

        return $result;
    }
}
