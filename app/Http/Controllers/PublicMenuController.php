<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Sppg;
use Illuminate\Http\Request;

class PublicMenuController extends Controller
{
    public function index(Request $request)
    {
        $query = Menu::with(['sppg.province', 'sppg.city'])
            ->whereHas('sppg', function ($q) {
                $q->where('is_active', true);
            });

        // Filter by SPPG
        if ($request->filled('sppg')) {
            $query->where('sppg_id', $request->sppg);
        }

        // Filter by Province (via SPPG)
        if ($request->filled('province')) {
            $query->whereHas('sppg', function ($q) use ($request) {
                $q->where('province_code', $request->province);
            });
        }

        // Filter by City/Kabupaten (via SPPG)
        if ($request->filled('city')) {
            $query->whereHas('sppg', function ($q) use ($request) {
                $q->where('city_code', $request->city);
            });
        }

        // Filter by Date (date column)
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        $menus = $query->orderBy('date', 'desc')->paginate(12)->withQueryString();

        // Data for filters
        $sppgs = Sppg::where('is_active', true)->orderBy('nama_sppg')->pluck('nama_sppg', 'id');
        $provinces = \Laravolt\Indonesia\Models\Province::orderBy('name')->pluck('name', 'code');
        
        // Optimize cities loading: if province is selected, only show cities from that province
        $cities = [];
        if ($request->filled('province')) {
            $cities = \Laravolt\Indonesia\Models\City::where('province_code', $request->province)
                ->orderBy('name')
                ->pluck('name', 'code');
        } elseif ($request->filled('city')) {
             // If city is selected but province isn't (edge case or direct link), load that city or all
             // For now, let's load all or maybe better to rely on JS for dynamic loading, 
             // but for server-side init, if no province selected, maybe don't load all cities to avoid heavy page?
             // Let's load all cities if no province is strictly required, OR just let user search if we used a fuzzy search.
             // Given the requirements, let's just allow filtering by city if province is selected, or maybe just list all used cities?
             // Let's get cities that actually have SPPGs to reduce list size?
             // For simple implementation:
             // If province present -> subset of cities.
             // If no province -> empty cities or all? Let's leave empty and require province selection for city filter, or show all.
             // Let's show all cities that have active SPPGs if possible, or just standard cities.
             // Let's stick to standard behavior: dependent dropdown logic usually requires JS.
             // For server side rendering, we pass what we have.
             $cities = \Laravolt\Indonesia\Models\City::orderBy('name')->pluck('name', 'code');
        }

        return view('public.menu.index', compact('menus', 'sppgs', 'provinces', 'cities'));
    }
}
