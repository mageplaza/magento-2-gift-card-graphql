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
use Mageplaza\GiftCard\Helper\Template;
use Mageplaza\GiftCard\Model\Product\Type\GiftCard as Type;
use Mageplaza\GiftCard\Model\Source\Status;
use Mageplaza\GiftCard\Model\TemplateFactory;

/**
 * @inheritdoc
 */
class GiftCardProductTemplate implements ResolverInterface
{
    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var TemplateFactory
     */
    private $templateFactory;

    /**
     * @var Template
     */
    private $templateHelper;

    /**
     * GiftCardProductTemplate constructor.
     *
     * @param MetadataPool $metadataPool
     * @param TemplateFactory $templateFactory
     * @param Template $templateHelper
     */
    public function __construct(
        MetadataPool $metadataPool,
        TemplateFactory $templateFactory,
        Template $templateHelper
    ) {
        $this->metadataPool    = $metadataPool;
        $this->templateFactory = $templateFactory;
        $this->templateHelper  = $templateHelper;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $linkField = $this->metadataPool->getMetadata(ProductInterface::class)->getLinkField();
        if ($value['type_id'] !== Type::TYPE_GIFTCARD
            || !isset($value[$linkField])
            || !$this->templateHelper->isEnabled()
        ) {
            return [];
        }

        /** @var Product $product */
        $product = $value['model'];
        $product->load($product->getId());

        $templateIds     = $product->getGiftProductTemplate();
        $resultTemplates = [];
        if ($templateIds) {
            $templates = $this->templateFactory->create()
                ->getCollection()
                ->addFieldToFilter('template_id', ['in' => explode(',', $templateIds)])
                ->addFieldToFilter('status', Status::STATUS_ACTIVE);
            foreach ($templates->getItems() as $template) {
                $data           = $this->templateHelper->prepareTemplateData($template->getData());
                $data['card']   = Template::jsonEncode($data['card']);
                $data['design'] = Template::jsonEncode($data['design']);

                $resultTemplates[$template->getId()] = $data;
            }
        }

        return $resultTemplates;
    }
}
