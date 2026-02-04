<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InquiryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Inquiry', [
           'data' => Inquiry::orderByDesc('created_at')->get()
        ]);
    }

    public function markAs(Request $request, Inquiry $inquiry)
    {
        $validated = $request->validate([
            'status' => 'required|in:read,replied'
        ]);

        $inquiry->markAs($validated['status']);

        return redirect()->back();
    }
}
