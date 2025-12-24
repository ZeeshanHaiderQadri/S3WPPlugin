<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\User;
use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    protected $paymentService;
    
    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }
    
    public function create(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'email' => 'required|email',
            'name' => 'required|string',
        ]);
        
        $plan = Plan::findOrFail($request->plan_id);
        
        // Create or get user
        $user = User::firstOrCreate(
            ['email' => $request->email],
            [
                'name' => $request->name,
                'password' => bcrypt(str()->random(16)),
                'role' => 'customer',
                'is_active' => true,
            ]
        );
        
        // Create order
        $order = $this->paymentService->createOrder($user, $plan);
        
        // Create Stripe checkout session
        $session = $this->paymentService->createCheckoutSession($order);
        
        if ($session['success']) {
            return redirect($session['checkout_url']);
        }
        
        return back()->with('error', 'Failed to create checkout session');
    }
    
    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');
        
        // TODO: Verify session and show success page
        
        return view('checkout.success');
    }
    
    public function cancel(Request $request)
    {
        return view('checkout.cancel');
    }
    
    public function webhook(Request $request)
    {
        $payload = @file_get_contents('php://input');
        $signature = $request->header('Stripe-Signature');
        
        $result = $this->paymentService->handleWebhook(
            json_decode($payload, true),
            $signature
        );
        
        return response()->json($result);
    }
}
