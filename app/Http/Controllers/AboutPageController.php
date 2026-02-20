<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class AboutPageController extends Controller
{
    public function __invoke(): View
    {
        return view('about');
    }
}
