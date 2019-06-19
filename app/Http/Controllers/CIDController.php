<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CIDController extends Controller
{
    public function transactiontraceback()
    {
        return view('cid.transactiontraceback.index');
    }

    public function iptracking()
    {
        return view('cid.iptracking.index');
    }
    //
}
