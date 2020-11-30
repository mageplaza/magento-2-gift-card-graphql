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
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Mageplaza\GiftCard\Api\GiftTemplateManagementInterface;
use Mageplaza\GiftCard\Helper\Data;

/**
 * Class UploadImage
 * @package Mageplaza\GiftCardGraphQl\Model\Resolver
 */
class UploadImage implements ResolverInterface
{
    /**
     * @var Data
     */
    private $helperData;

    /**
     * @var GiftTemplateManagementInterface
     */
    private $giftTemplateManagement;

    /**
     * UploadImage constructor.
     *
     * @param Data $helperData
     * @param GiftTemplateManagementInterface $giftTemplateManagement
     */
    public function __construct(
        Data $helperData,
        GiftTemplateManagementInterface $giftTemplateManagement
    ) {
        $this->helperData             = $helperData;
        $this->giftTemplateManagement = $giftTemplateManagement;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!$this->helperData->isEnabled()) {
            throw new GraphQlInputException(__('The module is disabled'));
        }

        try {
            return $this->giftTemplateManagement->uploadImage($args);
        } catch (Exception $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }
    }
}
