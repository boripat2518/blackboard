<?php
namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ResponseController as ResponseController;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
// use App\Http\Models\v1\Facebook;
// use App\Http\Models\v1\Location;

class LessonController extends ResponseController {

  public function create(Request $request){
    $validator=Validator::make($request->all(), [
      'name' => 'required',
      'cover' => 'file|mimes:jpeg,png,jpg,gif|max:5120',
      'lesson' => 'file|mimes:mp4,mpeg|max:51200'
    ]);

    if($validator->fails()){
      return $this->sendError($validator->errors());
    }

    $success=array();
    $success['request']=$request->all();
    $aUser = Auth::user();
    $destPath='public/lessons/tmp/';
    if ($request->file('cover')) {
      $imgCover=$request->file('cover');
      $imgName=sprintf("%05d_%s.%s",
                  $aUser->id, date('YmdHis'),
                  mb_strtolower($imgCover->getClientOriginalExtension()));
      $destCover=sprintf("%s%s",$destPath,$imgName);
      $aReturn['destCover']=$destCover;
//      if ($request->file('cover')->storeAs($destPath,$imgCover)) {
      if ($request->file('cover')->storeAs($destPath,$imgName)) {
        $success['cover']=sprintf("%s%s",$destPath,$imgName);
      }
    }
    if ($request->file('lesson')) {
      $vdoLesson=$request->file('lesson');
      $vdoName=sprintf("%05d_%s.%s",
                  $aUser->id, date('YmdHis'),
                  mb_strtolower($vdoLesson->getClientOriginalExtension()));
      $destFile=sprintf("%s%s",$destPath,$vdoName);
      $aReturn['destVideo']=$destFile;
//      if ($request->file('lesson')->storeAs($destPath,$vdoLesson)) {
      if ($request->file('lesson')->storeAs($destPath,$vdoName)) {
        $success['lesson']=sprintf("%s%s",$destPath,$vdoName);
      }
    }
    $success['return']=$aReturn;
    return $this->sendResponse($success);
  }

}
