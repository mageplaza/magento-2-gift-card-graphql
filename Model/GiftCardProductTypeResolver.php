<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mageplaza\GiftCardGraphQl\Model;

use Magento\Framework\GraphQl\Query\Resolver\TypeResolverInterface;
use Mageplaza\GiftCard\Model\Product\Type\GiftCard as Type;

/**
 * @inheritdoc
 */
class GiftCardProductTypeResolver implements TypeResolverInterface
{
    /**
     * Configurable product type resolver code
     */
    const TYPE_RESOLVER = 'MpGiftCardProduct';

    /**
     * @inheritdoc
     */
    public function resolveType(array $data): string
    {
        if (isset($data['type_id']) && $data['type_id'] === Type::TYPE_GIFTCARD) {
            return self::TYPE_RESOLVER;
        }
        return '';
    }
}
