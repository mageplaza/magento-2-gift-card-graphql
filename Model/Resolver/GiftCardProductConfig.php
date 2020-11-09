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

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Mageplaza\GiftCard\Helper\Data;
use Mageplaza\GiftCard\Helper\Product as ProductHelper;
use Mageplaza\GiftCard\Model\Product\Type\GiftCard as Type;

/**
 * @inheritdoc
 */
class GiftCardProductConfig implements ResolverInterface
{
    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var ProductHelper
     */
    private $productHelper;

    /**
     * GiftCardProductInformation constructor.
     *
     * @param MetadataPool $metadataPool
     * @param ProductHelper $productHelper
     */
    public function __construct(
        MetadataPool $metadataPool,
        ProductHelper $productHelper
    ) {
        $this->metadataPool  = $metadataPool;
        $this->productHelper = $productHelper;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $linkField = $this->metadataPool->getMetadata(ProductInterface::class)->getLinkField();
        if ($value['type_id'] !== Type::TYPE_GIFTCARD
            || !isset($value[$linkField])
            || !$this->productHelper->isEnabled()
        ) {
            return [];
        }

        return $this->getGiftCardProductInformation($value['model']);
    }

    /**
     * @param Product $product
     *
     * @return array
     */
    public function getGiftCardProductInformation($product)
    {
        $product->load($product->getId());
        $information = $this->productHelper->getGiftCardProductInformation($product);
        $this->prepareInformation($information);

        return $information;
    }

    /**
     * @param array $information
     */
    public function prepareInformation(&$information)
    {
        foreach ($information['delivery'] as &$datum) {
            if (isset($datum['fields'])) {
                $datum['fields'] = Data::jsonEncode($datum['fields']);
            }
        }
    }
}
