<?php

namespace App\Http\Controllers\template\pages;

use App\Http\Controllers\Controller;

class AccountSettingsNotifications extends Controller
{
  public function index()
  {
    return view('content.pages.pages-account-settings-notifications');
  }
}
