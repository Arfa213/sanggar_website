<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Topeng;
use Illuminate\Http\Request;

class TopengApiController extends Controller
{
    public function index()
    {
        $topeng = Topeng::all()->map(fn($t) => array_merge($t->toArray(), [
            'foto' => $t->foto ? asset('storage/' . $t->foto) : null,
        ]));

        return response()->json(['data' => $topeng]);
    }

    public function show($id)
    {
        $topeng = Topeng::findOrFail($id);

        return response()->json([
            'data' => array_merge($topeng->toArray(), [
                'foto' => $topeng->foto ? asset('storage/' . $topeng->foto) : null,
            ]),
        ]);
    }
}
