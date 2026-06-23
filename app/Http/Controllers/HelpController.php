<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class HelpController extends Controller
{
    /**
     * Display the Help & Support Knowledge Base.
     */
    public function index(): View
    {
        return view('help.index');
    }
}
