<?php
namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ResponseController as ResponseController;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Category;
use Illuminate\Support\Facades\Storage;
// use App\Http\Models\v1\Facebook;
// use App\Http\Models\v1\Location;

class CategoryController extends ResponseController {

  public function all(Request $request){

    $result=array("total"=>0,"results"=>array());
    $lists=Category::orderBy('id')->get();
    foreach ($lists as $list) {
      $result['results'][]=array(
        "id"=>$list->id,
        "icon"=>$list->icon,
        "cover"=>$list->cover,
        "title"=>$list->title
      );
    }
    $result['total']=count($lists);

    return $this->sendResponse($result);
  }

}
