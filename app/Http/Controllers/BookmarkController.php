<?php

namespace App\Http\Controllers;

use App\Models\Opportunity;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    public function toggle(Request $request, Opportunity $opportunity)
    {
        $request->user()->savedOpportunities()->toggle($opportunity->id);

        return back();
    }
}
