<?php

namespace Modules\Restaurant\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        return view('restaurant::frontend.theme_one.contact.index');
    }
}
