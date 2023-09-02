<?php

namespace App\Http\Controllers\template\apps;

use App\Http\Controllers\Controller;

class Kanban extends Controller
{
  public function index()
  {
    return view('content.apps.app-kanban');
  }
}
