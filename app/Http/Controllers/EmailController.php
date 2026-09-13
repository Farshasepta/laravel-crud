<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function index()
    {
        return view('email.index');
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'email_penerima' => ['required', 'email'],
            'subject' => ['required', 'string'],
            'pesan' => ['required', 'string'],
        ]);

        try {
            Mail::raw($validated['pesan'], function ($message) use ($validated) {
                $message
                    ->to($validated['email_penerima'])
                    ->subject($validated['subject']);
            });

            return redirect()->route('email.index')->with('success', 'Email Berhasil Dikirimkan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengirim email: '.$e->getMessage());
        }
    }
}
