@extends('landing.layout')

@section('title', 'Home')

@section('content')
<!-- Hero Section -->
<section class="hero">
    <h1>Offload WordPress Media to AWS S3</h1>
    <p>Professional plugin to automatically upload your WordPress media library to Amazon S3 and serve via CloudFront CDN. Boost performance and save server space.</p>
    
    <div class="hero-buttons">
        <a href="{{ route('pricing') }}" class="btn btn-primary">View Pricing</a>
        <a href="{{ route('docs') }}" class="btn btn-secondary">Read Documentation</a>
    </div>
</section>

<!-- Features Section -->
<section class="container">
    <div class="section-title">
        <h2>Why Choose WP Cloud Media Offload?</h2>
        <p>Everything you need to manage WordPress media at scale</p>
    </div>
    
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">☁️</div>
            <h3>AWS S3 Integration</h3>
            <p>Seamlessly upload all your media files to Amazon S3 with automatic synchronization.</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">⚡</div>
            <h3>CloudFront CDN</h3>
            <p>Serve your media through CloudFront CDN for lightning-fast global delivery.</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">📦</div>
            <h3>Bulk Upload</h3>
            <p>Upload 250K+ existing images with our optimized bulk upload feature.</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">🎨</div>
            <h3>Modern UI</h3>
            <p>Beautiful purple-orange gradient interface with light and dark mode support.</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">🔒</div>
            <h3>Secure & Reliable</h3>
            <p>Enterprise-grade security with license validation and usage tracking.</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">📊</div>
            <h3>Usage Analytics</h3>
            <p>Track your media uploads and monitor usage against your plan limits.</p>
        </div>
    </div>
</section>

<!-- Pricing Preview -->
<section class="container" style="background: #f9fafb; margin: 0; max-width: 100%; padding: 80px 20px;">
    <div class="section-title">
        <h2>Simple, Transparent Pricing</h2>
        <p>Choose the plan that fits your needs</p>
    </div>
    
    <div style="max-width: 1200px; margin: 0 auto;">
        <div class="pricing-grid">
            @foreach($plans->take(4) as $plan)
            <div class="pricing-card {{ $plan->slug === 'gold' ? 'featured' : '' }}">
                @if($plan->slug === 'gold')
                    <div class="pricing-badge">Most Popular</div>
                @endif
                
                <h3>{{ $plan->name }}</h3>
                <div class="pricing-price">
                    ${{ number_format($plan->price, 0) }}
                </div>
                <div class="pricing-period">per year</div>
                
                <ul class="pricing-features">
                    <li>{{ $plan->media_limit ? number_format($plan->media_limit) : 'Unlimited' }} media files</li>
                    <li>{{ $plan->max_sites }} {{ $plan->max_sites > 1 ? 'sites' : 'site' }}</li>
                    <li>AWS S3 & CloudFront</li>
                    <li>Automatic uploads</li>
                    @if($plan->price > 0)
                        <li>Priority support</li>
                    @endif
                </ul>
                
                <a href="{{ route('pricing') }}" class="btn-pricing">
                    {{ $plan->price > 0 ? 'Get Started' : 'Start Free' }}
                </a>
            </div>
            @endforeach
        </div>
        
        <div style="text-align: center; margin-top: 40px;">
            <a href="{{ route('pricing') }}" class="btn btn-primary">View All Plans</a>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="container">
    <div style="background: linear-gradient(135deg, #8B5CF6, #F97316); color: white; padding: 60px 40px; border-radius: 16px; text-align: center;">
        <h2 style="font-size: 36px; margin-bottom: 20px;">Ready to Get Started?</h2>
        <p style="font-size: 18px; margin-bottom: 30px; opacity: 0.95;">Join thousands of WordPress sites using WP Cloud Media Offload</p>
        <a href="{{ route('pricing') }}" class="btn btn-primary">Choose Your Plan</a>
    </div>
</section>
@endsection
