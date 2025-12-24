@extends('landing.layout')

@section('title', 'Contact Us')

@section('content')
<section class="hero">
    <h1>Contact Us</h1>
    <p>Have questions? We're here to help!</p>
</section>

<section class="container">
    <div style="max-width: 600px; margin: 0 auto;">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        <div class="feature-card">
            <form action="{{ route('contact.submit') }}" method="POST">
                @csrf
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">Your Name</label>
                    <input type="text" name="name" required style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px;">
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">Email Address</label>
                    <input type="email" name="email" required style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px;">
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">Subject</label>
                    <input type="text" name="subject" required style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px;">
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">Message</label>
                    <textarea name="message" required rows="6" style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; resize: vertical;"></textarea>
                </div>
                
                <button type="submit" style="width: 100%; padding: 14px; background: linear-gradient(135deg, #8B5CF6, #F97316); color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer;">
                    Send Message
                </button>
            </form>
        </div>
        
        <div style="margin-top: 40px; text-align: center;">
            <h3 style="margin-bottom: 20px;">Other Ways to Reach Us</h3>
            <p style="color: #666; margin-bottom: 10px;">📧 Email: support@yourcompany.com</p>
            <p style="color: #666;">⏰ Response time: Within 24 hours</p>
        </div>
    </div>
</section>
@endsection
