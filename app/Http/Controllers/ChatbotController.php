<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatbotController extends Controller
{
    public function ask(Request $request)
    {
        $request->validate([
            'question' => 'required|string'
        ]);

        $question = $request->input('question');
        
        // Replace with your Flask API URL
        $api_url = 'http://185.170.58.204:5000/';
        
        $response = Http::post($api_url, [
            'question' => $question
        ]);

        if ($response->successful()) {
            return response()->json([
                'answer' => $response->json()['response']
            ]);
        } else {
            return response()->json([
                'error' => 'Error connecting to the chatbot API.'
            ], 500);
        }
    }
}
