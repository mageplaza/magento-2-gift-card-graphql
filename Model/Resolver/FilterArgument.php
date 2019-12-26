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
use Magento\Framework\GraphQl\ConfigInterface;
use Magento\Framework\GraphQl\Query\Resolver\Argument\FieldEntityAttributesInterface;

/**
 * Class FilterArgument
 * @package Mageplaza\GiftCardGraphQl\Model\Resolver
 */
class FilterArgument implements FieldEntityAttributesInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * FilterArgument constructor.
     *
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     */
    public function getEntityAttributes(): array
    {
        $fields = [];

        $elements = ['GiftCodeOutput', 'GiftPoolOutput', 'GiftTemplateOutput'];
        foreach ($elements as $element) {
            /** @var Field $field */
            foreach ($this->config->getConfigElement($element)->getFields() as $field) {
                $fields[$field->getName()] = '';
            }
        }

        return array_keys($fields);
    }
}
