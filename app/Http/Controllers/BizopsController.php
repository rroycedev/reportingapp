<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BizopsController extends Controller
{
    public function employees()
    {
        return view('bizops.employees.index');
    }

    public function territories()
    {
        return view('bizops.territories.index');
    }

}
