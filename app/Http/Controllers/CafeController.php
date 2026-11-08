<?php

namespace App\Http\Controllers;

use App\Support\CafeMapPage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CafeController extends Controller
{
    /**
     * The single-page cafe map (/cafes).
     *
     * The provinsi -> kabupaten/kota cascade is server-rendered with café
     * counts so availability ("Bandung · 469 kedai" / "belum tersedia")
     * is visible before any JS runs. Kept module-clean: nothing here is
     * shared with the catalog controllers.
     */
    public function index(Request $request, CafeMapPage $page): View
    {
        return view('public.cafes.index', $page->data($request));
    }
}
