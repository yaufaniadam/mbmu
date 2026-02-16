<?php

namespace App\Http\Controllers;

use App\Models\Instruction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InstructionAttachmentController extends Controller
{
    public function download(Instruction $instruction)
    {
        // Simple auth check via middleware is sufficient for now, 
        // but ideally should use Policy: $this->authorize('view', $instruction);
        
        return $this->serveFile($instruction);
    }

    public function downloadSigned(Request $request, Instruction $instruction)
    {
        if (!$request->hasValidSignature()) {
            abort(403);
        }

        return $this->serveFile($instruction);
    }

    private function serveFile(Instruction $instruction)
    {
        $path = $instruction->attachment_path;

        if (!$path) {
            abort(404);
        }

        // Check local first (new secure storage)
        if (Storage::disk('local')->exists($path)) {
            return Storage::disk('local')->response($path);
        }
        
        // Fallback to public (legacy)
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->response($path);
        }

        abort(404);
    }
}
