<?php

namespace App\Http\Controllers;

use App\Models\ContactInquiry;
use Illuminate\Http\Request;

class SkinbaeController extends Controller
{
    public function home()
    {
        return view('skinbae.home');
    }

    public function services()
    {
        return view('skinbae.services');
    }

    public function gallery()
    {
        return view('skinbae.gallery');
    }

    public function contact()
    {
        return view('skinbae.contact');
    }

    public function contactStore(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:32', 'regex:/^[0-9+\-\s()]+$/'],
            'inquiry_type' => ['required', 'in:general,booking,collaboration'],
            'service_interest' => ['nullable', 'string', 'max:120'],
            'preferred_date' => ['nullable', 'date', 'after_or_equal:today'],
            'message' => ['required', 'string', 'max:2000'],
            'privacy' => ['accepted'],
        ], [
            'phone.regex' => 'Format nomor telepon tidak valid.',
            'privacy.accepted' => 'Anda harus menyetujui kebijakan privasi.',
        ]);

        ContactInquiry::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'inquiry_type' => $data['inquiry_type'],
            'service_interest' => $data['service_interest'] ?? null,
            'preferred_date' => ! empty($data['preferred_date']) ? $data['preferred_date'] : null,
            'message' => $data['message'],
        ]);

        return redirect()
            ->route('skinbae.contact')
            ->with('success', 'Terima kasih. Pesan Anda telah kami terima. Tim kami akan menghubungi Anda segera.');
    }
}
