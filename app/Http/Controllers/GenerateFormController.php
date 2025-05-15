<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GenerateFormController extends Controller
{
    //
    public function index()
    {
        return view('generate_form.index')->with('title', 'Form Customize')->with('breadcrumb', ['Home', 'Marketing', 'Form Customize']);
    }
}
