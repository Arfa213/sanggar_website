<?php
namespace App\Http\Controllers;
use App\Models\{Tarian, Topeng};

class DigitalArchiveController extends Controller {
    public function index() {
        $tarianGrouped = Tarian::aktif()->orderBy('urutan')->get()->groupBy('jenis_kegiatan');
        $topeng = Topeng::aktif()->orderBy('urutan')->get();
        return view('pages.digital-archive', compact('tarianGrouped', 'topeng'));
    }
}
