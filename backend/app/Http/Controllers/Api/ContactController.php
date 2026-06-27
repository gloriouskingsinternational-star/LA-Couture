<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $key = 'contact:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'message' => "Too many submissions. Try again in {$seconds} seconds.",
            ], 429);
        }

        $request->validate([
            'name'         => 'required|string|max:100',
            'email'        => 'required|email|max:255',
            'phone'        => 'nullable|string|max:20',
            'enquiry_type' => 'nullable|string|max:100',
            'message'      => 'required|string|max:5000',
        ]);

        RateLimiter::hit($key, 3600);

        $form = ContactForm::create([
            'name'         => strip_tags($request->name),
            'email'        => $request->email,
            'phone'        => $request->phone ? preg_replace('/[^0-9+\-\s()]/', '', $request->phone) : null,
            'enquiry_type' => $request->enquiry_type ? strip_tags($request->enquiry_type) : null,
            'message'      => strip_tags($request->message),
            'status'       => 'received',
        ]);

        return response()->json([
            'message' => 'Thank you! We will respond within 24 hours.',
            'id'      => $form->id,
        ], 201);
    }
}
