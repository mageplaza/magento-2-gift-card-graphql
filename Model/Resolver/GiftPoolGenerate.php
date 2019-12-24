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
use Mageplaza\GiftCard\Api\GiftPoolManagementInterface;
use Mageplaza\GiftCard\Helper\Data;
use Mageplaza\GiftCardGraphQl\Helper\Auth;

/**
 * Class GiftPoolGenerate
 * @package Mageplaza\GiftCardGraphQl\Model\Resolver
 */
class GiftPoolGenerate implements ResolverInterface
{
    /**
     * @var string
     */
    protected $_aclResource = 'Mageplaza_GiftCard::pool';

    /**
     * @var GiftPoolManagementInterface
     */
    private $giftPoolManagement;

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var Auth
     */
    private $auth;

    /**
     * AbstractResolver constructor.
     *
     * @param GiftPoolManagementInterface $giftPoolManagement
     * @param Data $helper
     * @param Auth $auth
     */
    public function __construct(
        GiftPoolManagementInterface $giftPoolManagement,
        Data $helper,
        Auth $auth
    ) {
        $this->giftPoolManagement = $giftPoolManagement;
        $this->helper             = $helper;
        $this->auth               = $auth;
    }

    /**
     * {@inheritDoc}
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!$this->helper->isEnabled()) {
            throw new GraphQlInputException(__('The module is disabled'));
        }

        if (!$this->auth->isAllowed($args['accessToken'], $this->_aclResource)) {
            throw new GraphQlInputException(__("The consumer isn't authorized to access %1", $this->_aclResource));
        }

        if ($args['pattern'] === '') {
            throw new GraphQlInputException(__('pattern value must not be empty.'));
        }

        if ($args['qty'] < 0) {
            throw new GraphQlInputException(__('qty value must be greater than 0.'));
        }

        return $this->giftPoolManagement->generate($args['id'], $args['pattern'], $args['qty']);
    }
}
