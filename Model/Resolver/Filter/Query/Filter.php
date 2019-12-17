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

use Exception;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Query\Resolver\Argument\SearchCriteria\Builder;
use Mageplaza\GiftCard\Api\Data\GiftCodeInterface;
use Mageplaza\GiftCard\Api\Data\GiftPoolInterface;
use Mageplaza\GiftCard\Api\Data\GiftTemplateInterface;
use Mageplaza\GiftCard\Api\GiftCodeManagementInterface;
use Mageplaza\GiftCard\Api\GiftPoolManagementInterface;
use Mageplaza\GiftCard\Api\GiftTemplateManagementInterface;
use Mageplaza\GiftCard\Model\GiftCard as GiftCardModel;
use Mageplaza\GiftCard\Model\GiftCardFactory;
use Mageplaza\GiftCard\Model\Pool as PoolModel;
use Mageplaza\GiftCard\Model\PoolFactory;
use Mageplaza\GiftCard\Model\Template as TemplateModel;
use Mageplaza\GiftCard\Model\TemplateFactory;
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
     * @var GiftCardFactory
     */
    private $giftCardFactory;

    /**
     * @var PoolFactory
     */
    private $poolFactory;

    /**
     * @var TemplateFactory
     */
    private $templateFactory;

    /**
     * Filter constructor.
     *
     * @param Builder $searchCriteriaBuilder
     * @param SearchResultFactory $searchResultFactory
     * @param GiftCodeManagementInterface $giftCodeManagement
     * @param GiftPoolManagementInterface $giftPoolManagement
     * @param GiftTemplateManagementInterface $giftTemplateManagement
     * @param GiftCardFactory $giftCardFactory
     * @param PoolFactory $poolFactory
     * @param TemplateFactory $templateFactory
     */
    public function __construct(
        Builder $searchCriteriaBuilder,
        SearchResultFactory $searchResultFactory,
        GiftCodeManagementInterface $giftCodeManagement,
        GiftPoolManagementInterface $giftPoolManagement,
        GiftTemplateManagementInterface $giftTemplateManagement,
        GiftCardFactory $giftCardFactory,
        PoolFactory $poolFactory,
        TemplateFactory $templateFactory
    ) {
        $this->searchCriteriaBuilder  = $searchCriteriaBuilder;
        $this->searchResultFactory    = $searchResultFactory;
        $this->giftCodeManagement     = $giftCodeManagement;
        $this->giftPoolManagement     = $giftPoolManagement;
        $this->giftTemplateManagement = $giftTemplateManagement;
        $this->giftCardFactory        = $giftCardFactory;
        $this->poolFactory            = $poolFactory;
        $this->templateFactory        = $templateFactory;
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

    /**
     * @param string $id
     * @param string $type
     *
     * @return GiftCodeInterface|GiftPoolInterface|GiftTemplateInterface
     * @throws NoSuchEntityException
     */
    public function getResultById($id, $type)
    {
        switch ($type) {
            case 'mpGiftCode':
                return $this->giftCodeManagement->get($id);
            case 'mpGiftPool':
                return $this->giftPoolManagement->get($id);
            case 'mpGiftTemplate':
            default:
                return $this->giftTemplateManagement->get($id);
        }
    }

    /**
     * @param array $data
     * @param string $type
     *
     * @return GiftCodeInterface|GiftPoolInterface|GiftTemplateInterface
     * @throws Exception
     */
    public function saveEntity($data, $type)
    {
        switch ($type) {
            case 'mpGiftCode':
                $entity = $this->giftCardFactory->create()->setData($data);

                return $this->giftCodeManagement->save($entity);
            case 'mpGiftPool':
                $entity = $this->poolFactory->create()->setData($data);

                return $this->giftPoolManagement->save($entity);
            case 'mpGiftTemplate':
            default:
                $entity = $this->templateFactory->create()->setData($data);

                return $this->giftTemplateManagement->save($entity);
        }
    }

    /**
     * @param string $id
     * @param string $type
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    public function deleteEntity($id, $type)
    {
        switch ($type) {
            case 'mpGiftCode':
                return $this->giftCodeManagement->delete($id);
            case 'mpGiftPool':
                return $this->giftPoolManagement->delete($id);
            case 'mpGiftTemplate':
            default:
                return $this->giftTemplateManagement->delete($id);
        }
    }
}
