<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MediaLibraryController extends Controller
{
    /**
   * Get Media Library page
   * @return View
   */
  
  public function mediaLibrary(Request $request)
  {
    $user_obj = 123;
    return view('medialibrary', ['user_obj' => $user_obj ]);
  }
}
