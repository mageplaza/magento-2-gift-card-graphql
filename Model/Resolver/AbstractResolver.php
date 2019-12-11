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
use Mageplaza\GiftCardGraphQl\Model\Resolver\Filter\Query\Filter;
use Mageplaza\GiftCardGraphQl\Model\Resolver\Filter\SearchResult;

/**
 * Class AbstractResolver
 * @package Mageplaza\GiftCardGraphQl\Model\Resolver
 */
class AbstractResolver implements ResolverInterface
{
    protected $_type = '';

    /**
     * @var Filter
     */
    private $filter;

    /**
     * AbstractResolver constructor.
     *
     * @param Filter $filter
     */
    public function __construct(Filter $filter)
    {
        $this->filter = $filter;
    }

    /**
     * {@inheritDoc}
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $this->validateArgs($args);

        $searchResult = $this->filter->getResult($args, $this->_type);

        return [
            'total_count' => $searchResult->getTotalCount(),
            'items'       => $searchResult->getItems(),
            'page_info'   => $this->getPageInfo($searchResult, $args)
        ];
    }

    /**
     * @param array $args
     *
     * @throws GraphQlInputException
     */
    protected function validateArgs(array $args)
    {
        if ($args['currentPage'] < 1) {
            throw new GraphQlInputException(__('currentPage value must be greater than 0.'));
        }

        if ($args['pageSize'] < 1) {
            throw new GraphQlInputException(__('pageSize value must be greater than 0.'));
        }

        if (!isset($args['filter'])) {
            throw new GraphQlInputException(__("'filter' input argument is required."));
        }
    }

    /**
     * @param SearchResult $searchResult
     * @param array $args
     *
     * @return array
     * @throws GraphQlInputException
     */
    protected function getPageInfo($searchResult, $args)
    {
        $totalPages  = ceil($searchResult->getTotalCount() / $args['pageSize']);
        $currentPage = $args['currentPage'];

        if ($currentPage > $totalPages && $searchResult->getTotalCount() > 0) {
            throw new GraphQlInputException(__(
                'currentPage value %1 specified is greater than the %2 page(s) available.',
                [$currentPage, $totalPages]
            ));
        }

        return [
            'pageSize'        => $args['pageSize'],
            'currentPage'     => $currentPage,
            'hasNextPage'     => $currentPage < $totalPages,
            'hasPreviousPage' => $currentPage > 1,
            'startPage'       => 1,
            'endPage'         => $totalPages,
        ];
    }
}
