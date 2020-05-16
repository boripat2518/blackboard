<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OptionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
      $options=DB::table('options')
        ->orderBy('head')
        ->orderBy('seq')
        ->get();
      $aOption=array();
      $myOption=$options->toArray();
//      echo json_encode($options->toArray());
      foreach($myOption as $option) {
        $head=$option->head;
        $aOption[$head][]=array(
          'value'=>intVal($option->value),
          'title'=>$option->title,
        );
      }
      echo json_encode($aOption);
    }
}
