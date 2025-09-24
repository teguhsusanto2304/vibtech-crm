<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OllamaController extends Controller
{
    public function chat(Request $request)
    {
        $prompt = $request->input('prompt');

        $response = Http::timeout(120) // Ollama may take time
            ->post('http://127.0.0.1:11434/api/chat', [
                'model' => 'travelmap', // or your custom model like travelmapbot
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ],
                'stream' => false
            ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Failed to connect to Ollama'], 500);
        }

        return $response->json();
    }
}
