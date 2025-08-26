<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function show()
    {
        $breadcrumbs = Breadcrumbs::generate('admin.home');
        $heading = 'Home';

        return Inertia::render('Admin/Home/Home', compact('heading', 'breadcrumbs'));
    }
}
