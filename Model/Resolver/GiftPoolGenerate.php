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
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Mageplaza\GiftCard\Api\GiftPoolManagementInterface;

/**
 * Class GiftPoolGenerate
 * @package Mageplaza\GiftCardGraphQl\Model\Resolver
 */
class GiftPoolGenerate implements ResolverInterface
{
    /**
     * @var GiftPoolManagementInterface
     */
    private $giftPoolManagement;

    /**
     * AbstractResolver constructor.
     *
     * @param GiftPoolManagementInterface $giftPoolManagement
     */
    public function __construct(GiftPoolManagementInterface $giftPoolManagement)
    {
        $this->giftPoolManagement = $giftPoolManagement;
    }

    /**
     * {@inheritDoc}
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        return $this->giftPoolManagement->generate($args['id'], $args['pattern'], $args['qty']);
    }
}
