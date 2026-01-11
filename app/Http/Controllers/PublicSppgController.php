<?php

namespace App\Http\Controllers;

use App\Models\Sppg;
use Illuminate\Http\Request;

class PublicSppgController extends Controller
{
    public function index()
    {
        $sppgs = Sppg::where('is_active', true)->paginate(25);
        return view('sppg.index', compact('sppgs'));
    }

    public function show(Sppg $sppg)
    {
        // Ensure only active SPPGs can be viewed
        if (!$sppg->is_active) {
            abort(404);
        }

        return view('sppg.show', compact('sppg'));
    }
}
