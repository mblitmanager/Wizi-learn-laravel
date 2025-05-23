<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ParrainageController extends Controller
{
    public function index()
    {
        return view('admin.parrainage.index');
    }

    public function show($id)
    {
        return view('admin.parrainage.show', compact('id'));
    }
}
