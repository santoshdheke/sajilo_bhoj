<?php

namespace Modules\Restaurant\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index()
    {
        return view('restaurant::admin.theme_one.billing.index');
    }
}
