<?php

namespace App\Http\Controllers;

use App\Models\Sppg;
use Illuminate\Http\Request;

class PublicSppgController extends Controller
{
    public function index(Request $request)
    {
        $query = Sppg::where('is_active', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_sppg', 'like', "%{$search}%")
                  ->orWhere('kode_sppg', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        if ($request->filled('province')) {
            $query->where('province_code', $request->province);
        }

        $sppgs = $query->paginate(25)->withQueryString();
        $provinces = \Laravolt\Indonesia\Models\Province::orderBy('name')->pluck('name', 'code');

        return view('sppg.index', compact('sppgs', 'provinces'));
    }

    public function show(Sppg $sppg)
    {
        // Ensure only active SPPGs can be viewed
        if (!$sppg->is_active) {
            abort(404);
        }

        $sppg->load(['province', 'city', 'pjSppg', 'menus']);

        return view('sppg.show', compact('sppg'));
    }
}
