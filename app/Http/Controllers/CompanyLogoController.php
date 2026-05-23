<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CompanyLogoController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): StreamedResponse
    {
        $company = $request->user()->company;

        abort_unless($company?->logo_disk && $company?->logo_path, 404);

        return Storage::disk($company->logo_disk)->response(
            $company->logo_path,
            $company->logo_original_name,
            ['Cache-Control' => 'private, max-age=300'],
        );
    }
}
