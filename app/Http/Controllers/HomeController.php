<?php

namespace App\Http\Controllers;

use App\Models\Opportunity;

class HomeController extends Controller
{
    public function index()
    {
        $active = Opportunity::published()->whereDate('deadline', '>=', today())->get();

        return view('home', [
            'featured' => $active->sortByDesc('created_at')->take(3),
            'closing' => $active->sortBy('deadline')->take(4),
            'counts' => $active->groupBy('category')->map->count(),
        ]);
    }
}
