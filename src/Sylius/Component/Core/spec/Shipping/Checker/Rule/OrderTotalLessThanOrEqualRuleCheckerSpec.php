<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Core\Shipping\Checker\Rule;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Shipping\Checker\Rule\OrderTotalLessThanOrEqualRuleChecker;
use Sylius\Component\Shipping\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface as BaseShipmentInterface;

final class OrderTotalLessThanOrEqualRuleCheckerSpec extends ObjectBehavior
{
    public function it_implements_rule_checker_interface(): void
    {
        $this->shouldImplement(RuleCheckerInterface::class);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(OrderTotalLessThanOrEqualRuleChecker::class);
    }

    public function it_denies_subject_if_subject_is_not_a_core_shipment(BaseShipmentInterface $shipment): void
    {
        $this->isEligible($shipment, [])->shouldReturn(false);
    }

    public function it_recognizes_subject_if_order_total_is_less_than_configured_amount(
        ShipmentInterface $subject,
        OrderInterface $order,
        ChannelInterface $channel
    ): void {
        $subject->getOrder()->willReturn($order);
        $order->getChannel()->willReturn($channel);
        $order->getTotal()->willReturn(99);
        $channel->getCode()->willReturn('CHANNEL');

        $this->isEligible($subject, [
            'total' => [
                'CHANNEL' => [
                    'amount' => 100,
                ],
            ],
        ])->shouldReturn(true);
    }

    public function it_recognizes_subject_if_order_total_is_equal_to_configured_amount(
        ShipmentInterface $subject,
        OrderInterface $order,
        ChannelInterface $channel
    ): void {
        $subject->getOrder()->willReturn($order);
        $order->getChannel()->willReturn($channel);
        $order->getTotal()->willReturn(100);
        $channel->getCode()->willReturn('CHANNEL');

        $this->isEligible($subject, [
            'total' => [
                'CHANNEL' => [
                    'amount' => 100,
                ],
            ],
        ])->shouldReturn(true);
    }

    public function it_denies_subject_if_order_total_is_greater_than_configured_amount(
        ShipmentInterface $subject,
        OrderInterface $order,
        ChannelInterface $channel
    ): void {
        $subject->getOrder()->willReturn($order);
        $order->getChannel()->willReturn($channel);
        $order->getTotal()->willReturn(101);
        $channel->getCode()->willReturn('CHANNEL');

        $this->isEligible($subject, [
            'total' => [
                'CHANNEL' => [
                    'amount' => 100,
                ],
            ],
        ])->shouldReturn(false);
    }
}
