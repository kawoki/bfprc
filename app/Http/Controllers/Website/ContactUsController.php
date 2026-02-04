<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Requests\StoreInquiryRequest;

class ContactUsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function index(Request $request)
    {
        return Inertia::render('ContactUs');
    }

    public function store(StoreInquiryRequest $request)
    {
        Inquiry::create($request->validated());

        return redirect()->back()->with('success', 'Inquiry submitted!');
    }
}
