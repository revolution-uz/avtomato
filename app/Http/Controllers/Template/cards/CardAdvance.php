<?php

namespace App\Http\Controllers\template\cards;

use App\Http\Controllers\Controller;

class CardAdvance extends Controller
{
  public function index()
  {
    return view('content.cards.cards-advance');
  }
}
