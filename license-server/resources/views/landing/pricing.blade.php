@extends('landing.layout')

@section('title', 'Pricing')

@section('content')
<!-- Hero Section -->
<section class="hero">
    <h1>Simple, Transparent Pricing</h1>
    <p>Choose the perfect plan for your WordPress site. All plans include AWS S3 & CloudFront integration.</p>
</section>

<!-- Pricing Section -->
<section class="container">
    <div class="pricing-grid">
        @foreach($plans as $plan)
        <div class="pricing-card {{ in_array($plan->slug, ['gold', 'gem']) ? 'featured' : '' }}">
            @if($plan->slug === 'gold')
                <div class="pricing-badge">Most Popular</div>
            @elseif($plan->slug === 'gem')
                <div class="pricing-badge">Best for Affiliates</div>
            @elseif($plan->slug === 'unlimited')
                <div class="pricing-badge">Enterprise</div>
            @endif
            
            <h3>{{ $plan->name }}</h3>
            
            <div class="pricing-price">
                @if($plan->price > 0)
                    ${{ number_format($plan->price, 0) }}
                @else
                    Free
                @endif
            </div>
            <div class="pricing-period">
                @if($plan->price > 0)
                    per year
                @else
                    forever
                @endif
            </div>
            
            <ul class="pricing-features">
                <li>
                    @if($plan->media_limit)
                        {{ number_format($plan->media_limit) }} media files
                    @else
                        Unlimited media files
                    @endif
                </li>
                <li>{{ $plan->max_sites }} {{ $plan->max_sites > 1 ? 'sites' : 'site' }}</li>
                <li>AWS S3 integration</li>
                <li>CloudFront CDN</li>
                <li>Automatic uploads</li>
                <li>Bulk upload tool</li>
                
                @if($plan->price > 0)
                    <li>Priority support</li>
                @endif
                
                @if($plan->price >= 149)
                    <li>Usage analytics</li>
                @endif
                
                @if($plan->price >= 199)
                    <li>White-label option</li>
                @endif
                
                @if($plan->price >= 1000)
                    <li>Dedicated support</li>
                    <li>Custom integrations</li>
                @endif
            </ul>
            
            @if($plan->price > 0)
                <form action="{{ route('checkout.create') }}" method="POST">
                    @csrf
                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                    <input type="hidden" name="email" value="" id="email-{{ $plan->id }}">
                    <input type="hidden" name="name" value="" id="name-{{ $plan->id }}">
                    
                    <button type="button" class="btn-pricing" onclick="showCheckoutModal({{ $plan->id }}, '{{ $plan->name }}', {{ $plan->price }})">
                        Buy Now
                    </button>
                </form>
            @else
                <a href="{{ route('docs') }}" class="btn-pricing">
                    Get Started Free
                </a>
            @endif
        </div>
        @endforeach
    </div>
</section>

<!-- FAQ Section -->
<section class="container" style="background: #f9fafb; margin: 0; max-width: 100%; padding: 80px 20px;">
    <div style="max-width: 800px; margin: 0 auto;">
        <div class="section-title">
            <h2>Frequently Asked Questions</h2>
        </div>
        
        <div style="background: white; padding: 30px; border-radius: 12px; margin-bottom: 20px;">
            <h3 style="margin-bottom: 10px;">What happens when I reach my media limit?</h3>
            <p style="color: #666;">When you reach your plan's media limit, you'll need to upgrade to a higher plan to continue uploading. Your existing media will remain accessible.</p>
        </div>
        
        <div style="background: white; padding: 30px; border-radius: 12px; margin-bottom: 20px;">
            <h3 style="margin-bottom: 10px;">Can I upgrade or downgrade my plan?</h3>
            <p style="color: #666;">Yes! You can upgrade at any time. Contact support for downgrades or plan changes.</p>
        </div>
        
        <div style="background: white; padding: 30px; border-radius: 12px; margin-bottom: 20px;">
            <h3 style="margin-bottom: 10px;">Do I need my own AWS account?</h3>
            <p style="color: #666;">Yes, you'll need your own AWS account with S3 and CloudFront configured. We provide detailed setup guides.</p>
        </div>
        
        <div style="background: white; padding: 30px; border-radius: 12px; margin-bottom: 20px;">
            <h3 style="margin-bottom: 10px;">What payment methods do you accept?</h3>
            <p style="color: #666;">We accept all major credit cards through Stripe. All payments are secure and encrypted.</p>
        </div>
        
        <div style="background: white; padding: 30px; border-radius: 12px;">
            <h3 style="margin-bottom: 10px;">Is there a refund policy?</h3>
            <p style="color: #666;">Yes, we offer a 30-day money-back guarantee. If you're not satisfied, contact us for a full refund.</p>
        </div>
    </div>
</section>

<!-- Checkout Modal -->
<div id="checkoutModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; padding: 40px; border-radius: 16px; max-width: 500px; width: 90%;">
        <h2 style="margin-bottom: 20px;">Complete Your Purchase</h2>
        <p style="color: #666; margin-bottom: 30px;">Plan: <strong id="modalPlanName"></strong> - $<span id="modalPlanPrice"></span>/year</p>
        
        <form id="checkoutForm" action="{{ route('checkout.create') }}" method="POST">
            @csrf
            <input type="hidden" name="plan_id" id="modalPlanId">
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">Your Name</label>
                <input type="text" name="name" required style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">Email Address</label>
                <input type="email" name="email" required style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px;">
            </div>
            
            <div style="display: flex; gap: 10px;">
                <button type="submit" style="flex: 1; padding: 14px; background: linear-gradient(135deg, #8B5CF6, #F97316); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    Continue to Payment
                </button>
                <button type="button" onclick="closeCheckoutModal()" style="padding: 14px 20px; background: #6b7280; color: white; border: none; border-radius: 8px; cursor: pointer;">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showCheckoutModal(planId, planName, planPrice) {
    document.getElementById('modalPlanId').value = planId;
    document.getElementById('modalPlanName').textContent = planName;
    document.getElementById('modalPlanPrice').textContent = planPrice;
    document.getElementById('checkoutModal').style.display = 'flex';
}

function closeCheckoutModal() {
    document.getElementById('checkoutModal').style.display = 'none';
}
</script>
@endsection
