@extends('landing.layout')

@section('title', 'Features')

@section('content')
<section class="hero">
    <h1>Powerful Features</h1>
    <p>Everything you need to manage WordPress media at scale</p>
</section>

<section class="container">
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">☁️</div>
            <h3>AWS S3 Integration</h3>
            <p>Automatically upload all WordPress media to Amazon S3. Configure once and forget about it.</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">⚡</div>
            <h3>CloudFront CDN</h3>
            <p>Serve media through CloudFront for lightning-fast global delivery and reduced server load.</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">📦</div>
            <h3>Bulk Upload</h3>
            <p>Migrate 250K+ existing images with our optimized bulk upload tool. Perfect for large sites.</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">🎨</div>
            <h3>Modern UI</h3>
            <p>Beautiful purple-orange gradient interface with light and dark mode support.</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">🔄</div>
            <h3>Auto Upload</h3>
            <p>New media files are automatically uploaded to S3 when added to WordPress.</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">📊</div>
            <h3>Usage Tracking</h3>
            <p>Monitor your media uploads and track usage against your plan limits in real-time.</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">🔒</div>
            <h3>Secure</h3>
            <p>Enterprise-grade security with license validation and encrypted connections.</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">⚙️</div>
            <h3>Easy Setup</h3>
            <p>Simple configuration wizard guides you through AWS setup in minutes.</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">📱</div>
            <h3>Responsive</h3>
            <p>Works perfectly on desktop, tablet, and mobile devices.</p>
        </div>
    </div>
</section>
@endsection
