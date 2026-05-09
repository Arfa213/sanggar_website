<?php
namespace App\Http\Controllers;
use App\Models\{Tarian, Topeng};

class DigitalArchiveController extends Controller {
    public function index() {
        $tarian = Tarian::aktif()->orderBy('urutan')->get();
        $topeng = Topeng::aktif()->orderBy('urutan')->get();
        return view('pages.digital-archive', compact('tarian', 'topeng'));
    }
}
