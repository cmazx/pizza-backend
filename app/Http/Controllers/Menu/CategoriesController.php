<?php

namespace App\Http\Controllers\Menu;

use App\Http\Controllers\Controller;
use App\MenuCategory;

class CategoriesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function index(){
        return MenuCategory::ordered()->get();
    }

}
