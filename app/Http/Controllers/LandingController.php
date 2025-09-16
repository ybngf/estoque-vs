<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;

class LandingController extends Controller
{
    public function index()
    {
        $plans = Plan::active()->ordered()->get();
        
        return view('landing.index', compact('plans'));
    }

    public function pricing()
    {
        $plans = Plan::active()->ordered()->get();
        
        return view('landing.pricing', compact('plans'));
    }

    public function features()
    {
        return view('landing.features');
    }

    public function about()
    {
        return view('landing.about');
    }

    public function contact()
    {
        return view('landing.contact');
    }

    public function demo()
    {
        return view('landing.demo');
    }
}
