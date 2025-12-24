<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class LandingController extends Controller
{
    public function index()
    {
        $plans = Plan::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        
        return view('landing.index', compact('plans'));
    }
    
    public function pricing()
    {
        $plans = Plan::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        
        return view('landing.pricing', compact('plans'));
    }
    
    public function features()
    {
        return view('landing.features');
    }
    
    public function docs()
    {
        return view('landing.docs');
    }
    
    public function contact()
    {
        return view('landing.contact');
    }
    
    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);
        
        // TODO: Send email notification
        // Mail::to('admin@yourcompany.com')->send(new ContactFormMail($validated));
        
        return back()->with('success', 'Thank you for contacting us! We will get back to you soon.');
    }
}
