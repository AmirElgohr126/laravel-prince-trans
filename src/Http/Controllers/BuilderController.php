<?php

namespace Astrotomic\TranslatableMigrationBuilder\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\View\View;

class BuilderController extends Controller
{
    /**
     * Show the builder
     */
    public function index(): View
    {
        return view('translatable-builder::index');
    }
}
