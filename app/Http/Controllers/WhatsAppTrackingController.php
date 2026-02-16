<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\WhatsAppMessage;
use Illuminate\Support\Facades\Log;

class WhatsAppTrackingController extends Controller
{
    /**
     * Handle incoming status updates from Wablas.
     */
    public function updateStatus(Request $request)
    {
        // Log incoming request for debugging
        Log::info('Wablas Webhook Data:', $request->all());

        $validated = $request->validate([
            'id' => 'required',
            'phone' => 'required',
            'status' => 'required',
        ]);

        $messageId = $validated['id'];
        $status = $validated['status'];
        
        // Find the message by Wablas ID
        $whatsappMessage = WhatsAppMessage::where('wablas_message_id', $messageId)->first();

        if ($whatsappMessage) {
            $whatsappMessage->update([
                'status' => $status,
            ]);
            
            Log::info("WhatsApp Message ID {$messageId} updated to status: {$status}");
        } else {
            Log::warning("WhatsApp Message ID {$messageId} not found in database.");
        }

        return response()->json(['message' => 'Status updated successfully']);
    }
}
