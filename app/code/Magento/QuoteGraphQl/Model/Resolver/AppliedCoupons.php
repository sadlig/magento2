<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\QuoteGraphQl\Model\Resolver;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CouponManagementInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * @inheritdoc
 */
class AppliedCoupons implements ResolverInterface
{
    /**
     * @var CouponManagementInterface
     */
    private $couponManagement;

    /**
     * @param CouponManagementInterface $couponManagement
     */
    public function __construct(
        CouponManagementInterface $couponManagement
    ) {
        $this->couponManagement = $couponManagement;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, ?array $value = null, ?array $args = null)
    {
        if (!isset($value['model'])) {
            throw new LocalizedException(__('"model" value should be specified'));
        }
        $cart = $value['model'];
        $cartId = $cart->getId();
        $appliedCoupons = [];
        $appliedCoupon = $this->couponManagement->get($cartId);
        if ($appliedCoupon) {
            $appliedCoupons[] = [ 'code' => $appliedCoupon ];
        }
        return !empty($appliedCoupons) ? $appliedCoupons : null;
    }
}
