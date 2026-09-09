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

namespace Mageplaza\GiftCardGraphQl\Test\Unit\Model\Resolver;

use Exception;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Mageplaza\GiftCard\Api\GiftCodeManagementInterface;
use Mageplaza\GiftCard\Helper\Product;
use Mageplaza\GiftCardGraphQl\Model\Resolver\CheckCode;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CheckCodeTest extends TestCase
{
    /**
     * @var CheckCode
     */
    private $checkCodeResolver;

    /**
     * @var Product|MockObject
     */
    private $productHelperMock;

    /**
     * @var GiftCodeManagementInterface|MockObject
     */
    private $giftCodeManagementMock;

    /**
     * @var Field|MockObject
     */
    private $fieldMock;

    /**
     * @var ResolveInfo|MockObject
     */
    private $resolveInfoMock;

    protected function setUp(): void
    {
        $this->productHelperMock = $this->createMock(Product::class);
        $this->giftCodeManagementMock = $this->createMock(GiftCodeManagementInterface::class);
        $this->fieldMock = $this->createMock(Field::class);
        $this->resolveInfoMock = $this->createMock(ResolveInfo::class);

        /** @phpstan-ignore-next-line */
        $this->checkCodeResolver = new CheckCode(
            $this->productHelperMock,
            $this->giftCodeManagementMock
        );
    }

    public function testResolveWithDisabledModule()
    {
        $this->productHelperMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(false);

        $this->expectException(GraphQlInputException::class);
        $this->expectExceptionMessage('The module is disabled');

        $this->checkCodeResolver->resolve(
            $this->fieldMock,
            null,
            $this->resolveInfoMock,
            null,
            ['code' => 'TEST123']
        );
    }

    public function testResolveWithValidCode()
    {
        $code = 'TEST123';
        $expectedResult = ['status' => 'valid', 'balance' => 100];

        $this->productHelperMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->giftCodeManagementMock->expects($this->once())
            ->method('check')
            ->with($code)
            ->willReturn($expectedResult);

        $result = $this->checkCodeResolver->resolve(
            $this->fieldMock,
            null,
            $this->resolveInfoMock,
            null,
            ['code' => $code]
        );

        $this->assertEquals($expectedResult, $result);
    }

    public function testResolveWithException()
    {
        $code = 'INVALID123';
        $exceptionMessage = 'Invalid gift code';

        $this->productHelperMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->giftCodeManagementMock->expects($this->once())
            ->method('check')
            ->with($code)
            ->willThrowException(new Exception($exceptionMessage));

        $this->expectException(GraphQlInputException::class);
        $this->expectExceptionMessage($exceptionMessage);

        $this->checkCodeResolver->resolve(
            $this->fieldMock,
            null,
            $this->resolveInfoMock,
            null,
            ['code' => $code]
        );
    }

    public function testResolveWithEmptyCode()
    {
        $this->productHelperMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->giftCodeManagementMock->expects($this->once())
            ->method('check')
            ->with('')
            ->willReturn(['status' => 'invalid', 'message' => 'Code is required']);

        $result = $this->checkCodeResolver->resolve(
            $this->fieldMock,
            null,
            $this->resolveInfoMock,
            null,
            ['code' => '']
        );

        $this->assertEquals(['status' => 'invalid', 'message' => 'Code is required'], $result);
    }
}
