@extends('landing.layout')

@section('title', 'Documentation')

@section('content')
<section class="hero">
    <h1>Documentation</h1>
    <p>Everything you need to get started with WP Cloud Media Offload</p>
</section>

<section class="container">
    <div style="max-width: 800px; margin: 0 auto;">
        <div class="feature-card" style="margin-bottom: 30px;">
            <h3>📚 Getting Started</h3>
            <p>Learn how to install and configure the plugin in under 10 minutes.</p>
            <ul style="margin-top: 15px; padding-left: 20px;">
                <li>Installation guide</li>
                <li>AWS S3 setup</li>
                <li>CloudFront configuration</li>
                <li>License activation</li>
            </ul>
        </div>
        
        <div class="feature-card" style="margin-bottom: 30px;">
            <h3>⚙️ Configuration</h3>
            <p>Detailed guides for configuring all plugin features.</p>
            <ul style="margin-top: 15px; padding-left: 20px;">
                <li>S3 bucket settings</li>
                <li>CloudFront distribution</li>
                <li>Bulk upload options</li>
                <li>Advanced settings</li>
            </ul>
        </div>
        
        <div class="feature-card" style="margin-bottom: 30px;">
            <h3>🔧 Troubleshooting</h3>
            <p>Common issues and their solutions.</p>
            <ul style="margin-top: 15px; padding-left: 20px;">
                <li>Connection errors</li>
                <li>Upload failures</li>
                <li>License issues</li>
                <li>Performance optimization</li>
            </ul>
        </div>
        
        <div class="feature-card">
            <h3>💡 Best Practices</h3>
            <p>Tips and recommendations for optimal performance.</p>
            <ul style="margin-top: 15px; padding-left: 20px;">
                <li>S3 bucket configuration</li>
                <li>CloudFront optimization</li>
                <li>Cost management</li>
                <li>Security recommendations</li>
            </ul>
        </div>
    </div>
</section>
@endsection
