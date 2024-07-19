<?php

namespace App\Http\Controllers;

class PartnerPanelController extends Controller
{
    public function index()
    {
        return response()->redirect('/panel');
    }
}
