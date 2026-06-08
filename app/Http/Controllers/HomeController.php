<?php
namespace App\Http\Controllers;
use App\Models\{SanggarProfile, Galeri, Topeng, Tarian};

class HomeController extends Controller {
    public function index() {
        $profil = SanggarProfile::getInstance();
        $galeri = Galeri::aktif()->get();
        $topeng = Topeng::aktif()->orderBy('urutan')->get();
        $tarian = Tarian::aktif()->get();
        return view('pages.home', compact('profil', 'galeri', 'topeng', 'tarian'));
    }
}
