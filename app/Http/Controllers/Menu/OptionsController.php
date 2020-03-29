<?php

namespace App\Http\Controllers\Menu;

use App\Http\Controllers\Controller;
use App\Http\Resources\MenuPositionOption as MenuPositionOptionResource;
use App\MenuPositionOption;

class OptionsController extends Controller
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

    public function index()
    {
        return MenuPositionOptionResource::collection(MenuPositionOption::all());
    }
}
