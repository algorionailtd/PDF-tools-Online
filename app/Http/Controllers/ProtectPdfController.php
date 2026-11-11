<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use setasign\Fpdi\Tcpdf\Fpdi;

class ProtectPdfController extends Controller
{
    public function protect(Request $request)
    {
      
        $request->validate([
            'pdf' => 'required|file|mimes:pdf',
            'password' => 'required|string|min:8',
        ]);

        $password = $request->input('password');

        if (
            !preg_match('/[a-z]/', $password) ||
            !preg_match('/[A-Z]/', $password) ||
            !preg_match('/[0-9]/', $password) ||
            !preg_match('/[^A-Za-z0-9]/', $password)
        ) {
            return response()->json([
                'message' =>
                    'Password must contain uppercase, lowercase, number and special character.'
            ], 422);
        }

    
        $uploadedFile = $request->file('pdf');
        if (!$uploadedFile || !$uploadedFile->isValid()) {
            return response()->json([
                'message' => 'The uploaded PDF is invalid.'
            ], 422);
        }

        if ($uploadedFile->getSize() === 0) {
            return response()->json([
                'message' => 'The uploaded PDF is empty (0 bytes). Please choose a non-empty PDF.'
            ], 422);
        }

        $inputPath = $uploadedFile->getRealPath();

        if (!$inputPath || !File::exists($inputPath)) {
            return response()->json([
                'message' => 'Uploaded PDF could not be found.'
            ], 422);
        }

    
        $folder = storage_path('app/private/protected');

        if (!File::exists($folder)) {
            File::makeDirectory($folder, 0755, true);
        }

    
        $fileName = 'protected-' . Str::random(20) . '.pdf';

        $outputPath = $folder . DIRECTORY_SEPARATOR . $fileName;

        try {


            $pdf = new Fpdi();

            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);

            $pdf->SetCreator('PDFTools');
            $pdf->SetAuthor('PDFTools');
            $pdf->SetTitle('Password Protected PDF');
            $pdf->SetSubject('Password Protected PDF');
            $pdf->SetKeywords(
                'PDFTools, PDF, Password, Protected, Security'
            );

            $pageCount = $pdf->setSourceFile($inputPath);

            if ($pageCount < 1) {
                throw new \RuntimeException(
                    'The uploaded PDF does not contain any pages.'
                );
            }

            $ownerPassword = Str::random(64);

            /*
            |--------------------------------------------------------------------------
            | Password Protection
            |--------------------------------------------------------------------------
            |
            | Mode 2 = AES-128 encryption.
            |
            | 0 = RC4 40-bit
            | 1 = RC4 128-bit
            | 2 = AES-128
            |
            */

            $pdf->SetProtection(
                ['print'],
                $password,
                $ownerPassword,
                2,
                null
            );

         
            for ($page = 1; $page <= $pageCount; $page++) {

                $template = $pdf->importPage($page);
                $size = $pdf->getTemplateSize($template);
                $width = (float) $size['width'];
                $height = (float) $size['height'];

                $orientation = $width > $height
                    ? 'L'
                    : 'P';

                $pdf->AddPage(
                    $orientation,
                    [
                        $width,
                        $height
                    ]
                );

              
                $pdf->useTemplate(
                    $template,
                    0,
                    0,
                    $width,
                    $height,
                    true
                );
            }

            $pdf->Output(
                $outputPath,
                'F'
            );

         
            if (!File::exists($outputPath)) {
                throw new \RuntimeException(
                    'Protected PDF could not be created.'
                );
            }

            $fileSize = File::size($outputPath);

            if ($fileSize < 100) {
                throw new \RuntimeException(
                    'Generated PDF is empty or invalid.'
                );
            }

            $handle = fopen($outputPath, 'rb');

            if (!$handle) {
                throw new \RuntimeException(
                    'Generated PDF could not be opened.'
                );
            }

            $header = fread($handle, 5);

            fclose($handle);

            if ($header !== '%PDF-') {
                throw new \RuntimeException(
                    'Generated file is not a valid PDF.'
                );
            }

         
            $originalName = pathinfo(
                $uploadedFile->getClientOriginalName(),
                PATHINFO_FILENAME
            );

            $originalName = preg_replace(
                '/[^A-Za-z0-9_\- ]/',
                '',
                $originalName
            );

            if (!$originalName) {
                $originalName = 'document';
            }

            $downloadName = $originalName . '-protected.pdf';

            return response()->download(
                $outputPath,
                $downloadName,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' =>
                        'attachment; filename="' . $downloadName . '"',
                    'Content-Length' => (string) $fileSize,
                    'Cache-Control' =>
                        'no-store, no-cache, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0',
                ]
            )->deleteFileAfterSend(true);

        } catch (\Throwable $e) {

      
            if (File::exists($outputPath)) {
                File::delete($outputPath);
            }

            return response()->json([
                'message' =>
                    'Could not protect PDF: ' . $e->getMessage()
            ], 500);
        }
    }
}
