<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Str;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Stripe\PaymentIntent;
use Stripe\Webhook;

class PaymentService
{
    protected LicenseService $licenseService;

    public function __construct(LicenseService $licenseService)
    {
        $this->licenseService = $licenseService;
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create a new order
     */
    public function createOrder(User $user, Plan $plan): Order
    {
        return Order::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'order_number' => $this->generateOrderNumber(),
            'status' => 'pending',
            'amount' => $plan->price,
            'currency' => 'USD',
            'customer_email' => $user->email,
        ]);
    }

    /**
     * Generate unique order number
     */
    protected function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD-' . strtoupper(Str::random(10));
        } while (Order::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * Create Stripe Checkout Session
     */
    public function createCheckoutSession(Order $order): array
    {
        try {
            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => strtolower($order->currency),
                        'product_data' => [
                            'name' => $order->plan->name . ' Plan',
                            'description' => $order->plan->description,
                        ],
                        'unit_amount' => $order->amount * 100, // Convert to cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('checkout.success', ['order' => $order->order_number]) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('checkout.cancel', ['order' => $order->order_number]),
                'customer_email' => $order->customer_email,
                'client_reference_id' => $order->order_number,
                'metadata' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'user_id' => $order->user_id,
                    'plan_id' => $order->plan_id,
                ],
            ]);

            return [
                'success' => true,
                'session_id' => $session->id,
                'checkout_url' => $session->url,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Handle successful payment
     */
    public function handleSuccessfulPayment(Order $order, string $paymentId): void
    {
        $order->markAsCompleted($paymentId);

        // Create license for the user
        $license = $this->licenseService->createLicense(
            $order->user,
            $order->plan,
            365 // 1 year
        );

        // Send email with license key
        // event(new OrderCompleted($order, $license));
    }

    /**
     * Handle failed payment
     */
    public function handleFailedPayment(Order $order): void
    {
        $order->markAsFailed();
        
        // Send email notification
        // event(new OrderFailed($order));
    }

    /**
     * Handle Stripe webhook
     */
    public function handleWebhook(array $payload, string $signature): array
    {
        try {
            $event = Webhook::constructEvent(
                json_encode($payload),
                $signature,
                config('services.stripe.webhook_secret')
            );

            switch ($event->type) {
                case 'checkout.session.completed':
                    return $this->handleCheckoutSessionCompleted($event->data->object);

                case 'payment_intent.succeeded':
                    return $this->handlePaymentIntentSucceeded($event->data->object);

                case 'payment_intent.payment_failed':
                    return $this->handlePaymentIntentFailed($event->data->object);

                default:
                    return ['success' => true, 'message' => 'Unhandled event type'];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Handle checkout session completed
     */
    protected function handleCheckoutSessionCompleted($session): array
    {
        $orderNumber = $session->client_reference_id;
        $order = Order::where('order_number', $orderNumber)->first();

        if (!$order) {
            return [
                'success' => false,
                'message' => 'Order not found',
            ];
        }

        if ($order->isCompleted()) {
            return [
                'success' => true,
                'message' => 'Order already processed',
            ];
        }

        $this->handleSuccessfulPayment($order, $session->payment_intent);

        return [
            'success' => true,
            'message' => 'Payment processed successfully',
        ];
    }

    /**
     * Handle payment intent succeeded
     */
    protected function handlePaymentIntentSucceeded($paymentIntent): array
    {
        // Additional processing if needed
        return [
            'success' => true,
            'message' => 'Payment intent succeeded',
        ];
    }

    /**
     * Handle payment intent failed
     */
    protected function handlePaymentIntentFailed($paymentIntent): array
    {
        // Find order by payment intent ID and mark as failed
        $order = Order::where('payment_id', $paymentIntent->id)->first();
        
        if ($order) {
            $this->handleFailedPayment($order);
        }

        return [
            'success' => true,
            'message' => 'Payment intent failed',
        ];
    }

    /**
     * Process refund
     */
    public function processRefund(Order $order, ?string $reason = null): array
    {
        if (!$order->isCompleted()) {
            return [
                'success' => false,
                'message' => 'Order is not completed',
            ];
        }

        try {
            $refund = \Stripe\Refund::create([
                'payment_intent' => $order->payment_id,
                'reason' => $reason ?? 'requested_by_customer',
            ]);

            $order->update(['status' => 'refunded']);

            // Suspend associated license
            $license = $order->user->licenses()
                ->where('plan_id', $order->plan_id)
                ->where('status', 'active')
                ->first();

            if ($license) {
                $this->licenseService->suspendLicense($license, 'Order refunded');
            }

            return [
                'success' => true,
                'message' => 'Refund processed successfully',
                'refund_id' => $refund->id,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get payment statistics
     */
    public function getPaymentStats(?int $days = 30): array
    {
        $startDate = now()->subDays($days);

        $totalRevenue = Order::where('status', 'completed')
            ->where('paid_at', '>=', $startDate)
            ->sum('amount');

        $totalOrders = Order::where('paid_at', '>=', $startDate)->count();
        $completedOrders = Order::where('status', 'completed')
            ->where('paid_at', '>=', $startDate)
            ->count();
        $failedOrders = Order::where('status', 'failed')
            ->where('created_at', '>=', $startDate)
            ->count();

        $revenueByPlan = Order::where('status', 'completed')
            ->where('paid_at', '>=', $startDate)
            ->selectRaw('plan_id, SUM(amount) as revenue, COUNT(*) as orders')
            ->groupBy('plan_id')
            ->with('plan')
            ->get();

        return [
            'total_revenue' => $totalRevenue,
            'total_revenue_formatted' => '$' . number_format($totalRevenue, 2),
            'total_orders' => $totalOrders,
            'completed_orders' => $completedOrders,
            'failed_orders' => $failedOrders,
            'conversion_rate' => $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 2) : 0,
            'revenue_by_plan' => $revenueByPlan->map(function ($item) {
                return [
                    'plan' => $item->plan->name,
                    'revenue' => $item->revenue,
                    'revenue_formatted' => '$' . number_format($item->revenue, 2),
                    'orders' => $item->orders,
                ];
            }),
        ];
    }
}
