<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactForm;
use App\Models\Enquiry;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function stats()
    {
        return response()->json([
            'approved_clients' => User::where('role', 'client')->where('status', 'approved')->count(),
            'pending_clients'  => User::where('role', 'client')->where('status', 'pending')->count(),
            'total_orders'     => Order::count(),
            'open_enquiries'   => Enquiry::where('status', 'open')->count(),
            'contact_forms'    => ContactForm::count(),
            'total_revenue'    => Order::where('status', 'paid')->sum('total'),
        ]);
    }

    public function clients(Request $request)
    {
        $clients = User::where('role', 'client')
                       ->orderByDesc('created_at')
                       ->get(['id','name','email','phone','status','interests','created_at','approved_at','rejected_at','rejection_reason']);

        return response()->json($clients);
    }

    public function approveClient(Request $request, User $user)
    {
        if ($user->role !== 'client') {
            return response()->json(['message' => 'Not a client account.'], 400);
        }

        $user->update([
            'status'      => 'approved',
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        return response()->json(['message' => 'Client approved.', 'user' => $user]);
    }

    public function rejectClient(Request $request, User $user)
    {
        if ($user->role !== 'client') {
            return response()->json(['message' => 'Not a client account.'], 400);
        }

        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $user->update([
            'status'           => 'rejected',
            'rejected_at'      => now(),
            'rejection_reason' => $request->reason ? strip_tags($request->reason) : 'No reason provided.',
        ]);

        return response()->json(['message' => 'Client rejected.', 'user' => $user]);
    }

    public function contactForms()
    {
        return response()->json(
            ContactForm::orderByDesc('created_at')->paginate(50)
        );
    }

    public function updateContactForm(Request $request, ContactForm $contactForm)
    {
        $request->validate([
            'status' => 'required|in:received,reviewed,responded',
        ]);

        $contactForm->update(['status' => $request->status]);

        return response()->json($contactForm);
    }
}
