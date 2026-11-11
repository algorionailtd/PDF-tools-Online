<?php

namespace App\Http\Controllers;

use App\Models\PdfUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MergePdfController extends Controller
{
    /**
     * Show Merge PDF page
     */
    public function index()
    {
        return view('merge');
    }

    /**
     * Handle PDF merge request
     *
     * Global rule:
     * 1st file = FREE
     * 2nd file = FREE
     * 3rd file = PAYMENT REQUIRED
     *
     * The count is shared across all 12 PDF tools.
     */
    public function merge(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Please login first.');
        }

        /*
        |--------------------------------------------------------------------------
        | Get or create usage record
        |--------------------------------------------------------------------------
        */

        $usage = PdfUsage::firstOrCreate(
            ['user_id' => $user->id],
            ['free_uploads_used' => 0]
        );

        /*
        |--------------------------------------------------------------------------
        | Check free upload limit
        |--------------------------------------------------------------------------
        */

        if ($usage->free_uploads_used >= 2) {
            return redirect()->back()
                ->with('payment_required', true)
                ->with(
                    'error',
                    'Your 2 free PDF uploads have been used. Please make a payment to continue.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Validate files
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'files' => ['required', 'array', 'min:2'],
            'files.*' => ['required', 'file', 'mimes:pdf'],
        ]);

        foreach ($request->file('files') as $file) {
            if ($file->getSize() === 0) {
                return back()->withInput()->withErrors([
                    'files' => '"' . $file->getClientOriginalName() . '" is empty (0 bytes). Please choose a non-empty PDF.',
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Count the uploaded files
        |--------------------------------------------------------------------------
        |
        | If one merge operation contains multiple PDF files, each uploaded
        | PDF is counted as one free file.
        |
        */

        $fileCount = count($request->file('files'));

        $remainingFree = 2 - $usage->free_uploads_used;

        if ($fileCount > $remainingFree) {
            return redirect()->back()
                ->with('payment_required', true)
                ->with(
                    'error',
                    "You only have {$remainingFree} free file upload(s) remaining. Please make a payment to upload more files."
                );
        }

        /*
        |--------------------------------------------------------------------------
        | TODO: Your existing PDF merge code goes here
        |--------------------------------------------------------------------------
        |
        | Example:
        |
        | $files = $request->file('files');
        |
        | // Merge PDF files here...
        |
        */

        /*
        |--------------------------------------------------------------------------
        | Consume free uploads
        |--------------------------------------------------------------------------
        */

        $usage->increment('free_uploads_used', $fileCount);

        /*
        |--------------------------------------------------------------------------
        | Temporary response
        |--------------------------------------------------------------------------
        |
        | Replace this with your actual merged PDF download response.
        |
        */

        return redirect()->back()
            ->with(
                'success',
                'PDF files processed successfully. Free uploads used: ' .
                ($usage->free_uploads_used) .
                '/2'
            );
    }
}
