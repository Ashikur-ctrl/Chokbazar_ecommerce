<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SourcingController extends Controller
{
    public function setMode(string $mode, Request $request)
    {
        if (!in_array($mode, ['local', 'import'])) {
            $mode = 'local';
        }

        session(['sourcing_mode' => $mode]);

        if ($request->wantsJson()) {
            return response()->json(['mode' => $mode]);
        }

        return redirect()->back();
    }
}
