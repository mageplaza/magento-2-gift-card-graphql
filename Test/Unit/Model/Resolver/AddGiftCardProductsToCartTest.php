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

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\QuoteGraphQl\Model\Cart\AddProductsToCart;
use Magento\QuoteGraphQl\Model\Cart\GetCartForUser;
use Magento\Store\Api\Data\StoreInterface;
use Mageplaza\GiftCardGraphQl\Model\Resolver\AddGiftCardProductsToCart;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AddGiftCardProductsToCartTest extends TestCase
{
    /**
     * @var AddGiftCardProductsToCart
     */
    private $resolver;

    /**
     * @var GetCartForUser|MockObject
     */
    private $getCartForUserMock;

    /**
     * @var AddProductsToCart|MockObject
     */
    private $addProductsToCartMock;

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
        $this->getCartForUserMock    = $this->createMock(GetCartForUser::class);
        $this->addProductsToCartMock = $this->createMock(AddProductsToCart::class);
        $this->fieldMock             = $this->createMock(Field::class);
        $this->resolveInfoMock       = $this->createMock(ResolveInfo::class);

        /** @phpstan-ignore-next-line */
        $this->resolver = new AddGiftCardProductsToCart(
            $this->getCartForUserMock,
            $this->addProductsToCartMock
        );
    }

    public function testResolveWithMissingCartId()
    {
        $this->expectException(GraphQlInputException::class);
        $this->expectExceptionMessage('Required parameter "cart_id" is missing');

        $this->resolver->resolve(
            $this->fieldMock,
            $this->createContextMock(),
            $this->resolveInfoMock,
            null,
            ['input' => []]
        );
    }

    public function testResolveWithEmptyCartId()
    {
        $this->expectException(GraphQlInputException::class);
        $this->expectExceptionMessage('Required parameter "cart_id" is missing');

        $this->resolver->resolve(
            $this->fieldMock,
            $this->createContextMock(),
            $this->resolveInfoMock,
            null,
            ['input' => ['cart_id' => '']]
        );
    }

    public function testResolveWithMissingCartItems()
    {
        $this->expectException(GraphQlInputException::class);
        $this->expectExceptionMessage('Required parameter "cart_items" is missing');

        $this->resolver->resolve(
            $this->fieldMock,
            $this->createContextMock(),
            $this->resolveInfoMock,
            null,
            ['input' => ['cart_id' => 'test_cart_id']]
        );
    }

    public function testResolveWithEmptyCartItems()
    {
        $this->expectException(GraphQlInputException::class);
        $this->expectExceptionMessage('Required parameter "cart_items" is missing');

        $this->resolver->resolve(
            $this->fieldMock,
            $this->createContextMock(),
            $this->resolveInfoMock,
            null,
            ['input' => ['cart_id' => 'test_cart_id', 'cart_items' => []]]
        );
    }

    public function testResolveWithNonArrayCartItems()
    {
        $this->expectException(GraphQlInputException::class);
        $this->expectExceptionMessage('Required parameter "cart_items" is missing');

        $this->resolver->resolve(
            $this->fieldMock,
            $this->createContextMock(),
            $this->resolveInfoMock,
            null,
            ['input' => ['cart_id' => 'test_cart_id', 'cart_items' => 'not_an_array']]
        );
    }

    public function testResolveSuccessfully()
    {
        $cartId    = 'test_cart_id';
        $cartItems = [
            ['sku' => 'gift-card-1', 'quantity' => 1],
            ['sku' => 'gift-card-2', 'quantity' => 2]
        ];
        $storeId   = 1;
        $userId    = 123;

        $cartMock    = $this->createMock(\Magento\Quote\Model\Quote::class);
        $contextMock = $this->createContextMock($storeId, $userId);

        $this->getCartForUserMock->expects($this->once())
            ->method('execute')
            ->with($cartId, $userId, $storeId)
            ->willReturn($cartMock);

        $this->addProductsToCartMock->expects($this->once())
            ->method('execute')
            ->with($cartMock, $cartItems);

        $result = $this->resolver->resolve(
            $this->fieldMock,
            $contextMock,
            $this->resolveInfoMock,
            null,
            ['input' => ['cart_id' => $cartId, 'cart_items' => $cartItems]]
        );

        $this->assertArrayHasKey('cart', $result);
        $this->assertArrayHasKey('model', $result['cart']);
        $this->assertEquals($cartMock, $result['cart']['model']);
    }

    public function testResolveWithSingleGiftCard()
    {
        $cartId    = 'test_cart_id';
        $cartItems = [
            ['sku' => 'gift-card-single', 'quantity' => 1]
        ];
        $storeId   = 1;
        $userId    = 123;

        $cartMock    = $this->createMock(\Magento\Quote\Model\Quote::class);
        $contextMock = $this->createContextMock($storeId, $userId);

        $this->getCartForUserMock->expects($this->once())
            ->method('execute')
            ->with($cartId, $userId, $storeId)
            ->willReturn($cartMock);

        $this->addProductsToCartMock->expects($this->once())
            ->method('execute')
            ->with($cartMock, $cartItems);

        $result = $this->resolver->resolve(
            $this->fieldMock,
            $contextMock,
            $this->resolveInfoMock,
            null,
            ['input' => ['cart_id' => $cartId, 'cart_items' => $cartItems]]
        );

        $this->assertArrayHasKey('cart', $result);
        $this->assertArrayHasKey('model', $result['cart']);
        $this->assertEquals($cartMock, $result['cart']['model']);
    }

    private function createContextMock($storeId = 1, $userId = 123)
    {
        $contextMock = $this->getMockBuilder(\Magento\Framework\GraphQl\Query\Resolver\ContextInterface::class)
            ->addMethods(['getExtensionAttributes', 'getUserId'])
            ->getMock();

        // Create a mock for the extension attributes that has getStore method
        $extensionAttributesMock = $this->createMock(\Magento\GraphQl\Model\Query\ContextExtensionInterface::class);

        $storeMock = $this->createMock(StoreInterface::class);
        $storeMock->method('getId')->willReturn($storeId);

        $extensionAttributesMock->method('getStore')->willReturn($storeMock);

        $contextMock->method('getExtensionAttributes')->willReturn($extensionAttributesMock);
        $contextMock->method('getUserId')->willReturn($userId);

        return $contextMock;
    }
}
