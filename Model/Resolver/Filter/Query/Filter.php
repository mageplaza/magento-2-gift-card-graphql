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

namespace Mageplaza\GiftCardGraphQl\Model\Resolver\Filter\Query;

use Magento\Framework\GraphQl\Query\Resolver\Argument\SearchCriteria\Builder;
use Mageplaza\GiftCard\Api\GiftCodeManagementInterface;
use Mageplaza\GiftCard\Api\GiftPoolManagementInterface;
use Mageplaza\GiftCard\Api\GiftTemplateManagementInterface;
use Mageplaza\GiftCard\Model\GiftCard as GiftCardModel;
use Mageplaza\GiftCard\Model\Pool as PoolModel;
use Mageplaza\GiftCard\Model\Template as TemplateModel;
use Mageplaza\GiftCardGraphQl\Model\Resolver\Filter\SearchResult;
use Mageplaza\GiftCardGraphQl\Model\Resolver\Filter\SearchResultFactory;

/**
 * Retrieve filtered data based off given search criteria in a format that GraphQL can interpret.
 */
class Filter
{
    /**
     * @var Builder
     */
    private $searchCriteriaBuilder;

    /**
     * @var SearchResultFactory
     */
    private $searchResultFactory;

    /**
     * @var GiftCodeManagementInterface
     */
    private $giftCodeManagement;

    /**
     * @var GiftPoolManagementInterface
     */
    private $giftPoolManagement;

    /**
     * @var GiftTemplateManagementInterface
     */
    private $giftTemplateManagement;

    /**
     * Filter constructor.
     *
     * @param Builder $searchCriteriaBuilder
     * @param SearchResultFactory $searchResultFactory
     * @param GiftCodeManagementInterface $giftCodeManagement
     * @param GiftPoolManagementInterface $giftPoolManagement
     * @param GiftTemplateManagementInterface $giftTemplateManagement
     */
    public function __construct(
        Builder $searchCriteriaBuilder,
        SearchResultFactory $searchResultFactory,
        GiftCodeManagementInterface $giftCodeManagement,
        GiftPoolManagementInterface $giftPoolManagement,
        GiftTemplateManagementInterface $giftTemplateManagement
    ) {
        $this->searchCriteriaBuilder  = $searchCriteriaBuilder;
        $this->searchResultFactory    = $searchResultFactory;
        $this->giftCodeManagement     = $giftCodeManagement;
        $this->giftPoolManagement     = $giftPoolManagement;
        $this->giftTemplateManagement = $giftTemplateManagement;
    }

    /**
     * @param array $args
     * @param string $type
     *
     * @return SearchResult
     */
    public function getResult($args, $type): SearchResult
    {
        $searchCriteria = $this->searchCriteriaBuilder->build($type, $args);
        $searchCriteria->setCurrentPage($args['currentPage']);
        $searchCriteria->setPageSize($args['pageSize']);

        switch ($type) {
            case 'mpGiftCode':
                $list = $this->giftCodeManagement->getList($searchCriteria);
                break;
            case 'mpGiftPool':
                $list = $this->giftPoolManagement->getList($searchCriteria);
                break;
            case 'mpGiftTemplate':
            default:
                $list = $this->giftTemplateManagement->getList($searchCriteria);
                break;
        }

        $listArray = [];
        /** @var GiftCardModel|PoolModel|TemplateModel $item */
        foreach ($list->getItems() as $item) {
            $listArray[$item->getId()]          = $item->getData();
            $listArray[$item->getId()]['model'] = $item;
        }

        return $this->searchResultFactory->create($list->getTotalCount(), $listArray);
    }
}
