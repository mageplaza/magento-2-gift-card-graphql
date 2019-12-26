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

namespace Mageplaza\GiftCardGraphQl\Model\Resolver\Delete;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;

/**
 * Class AbstractResolver
 * @package Mageplaza\GiftCardGraphQl\Model\Resolver\Delete
 */
class AbstractResolver extends \Mageplaza\GiftCardGraphQl\Model\Resolver\AbstractResolver
{
    /**
     * @param array $args
     *
     * @return bool
     * @throws GraphQlInputException
     */
    protected function handleArgs(array $args)
    {
        try {
            return $this->filter->deleteEntity($args['id'], $this->_type);
        } catch (NoSuchEntityException $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }
    }
}
