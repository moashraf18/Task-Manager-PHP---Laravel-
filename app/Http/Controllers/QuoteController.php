<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class QuoteController extends Controller
{
    public function fetch(): JsonResponse
    {
        // reads from config/services.php → 'zenquotes' → 'url', which itself reads QUOTE_API_URL from your .env file. This chain exists because calling env() directly in controllers breaks when Laravel's config cache is active in production.
        $url = config('services.zenquotes.url');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; TaskManager/1.0)');

        // This fixes SSL errors on localhost (XAMPP/WAMP etc.)
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);
        curl_close($ch);

        // Show the actual curl error so you know what went wrong
        if ($error) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Curl error: ' . $error,
            ]);
        }

        // Rate limited or blocked by zenquotes
        if ($httpCode !== 200) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Quote service returned HTTP ' . $httpCode,
            ]);
        }

        $data = json_decode($response, true);

        if (!$data || !isset($data[0]['q'])) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Bad response from quote service. Raw: ' . substr($response, 0, 200),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'quote'  => $data[0]['q'],
            'author' => $data[0]['a'],
        ]);
    }
}