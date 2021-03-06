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

namespace Mageplaza\GiftCardGraphQl\Model\Cart\BuyRequest;

use Magento\Framework\Stdlib\ArrayManager;
use Magento\QuoteGraphQl\Model\Cart\BuyRequest\BuyRequestDataProviderInterface;

/**
 * Class GiftCardOptionsDataProvider
 * @package Mageplaza\GiftCardGraphQl\Model\Cart\BuyRequest
 */
class GiftCardOptionsDataProvider implements BuyRequestDataProviderInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * GiftCardOptionsDataProvider constructor.
     *
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        ArrayManager $arrayManager
    ) {
        $this->arrayManager = $arrayManager;
    }

    /**
     * @inheritdoc
     */
    public function execute(array $cartItemData): array
    {
        return $this->arrayManager->get('giftcard_options', $cartItemData) ?? [];
    }
}
