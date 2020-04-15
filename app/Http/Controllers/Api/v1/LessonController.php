<?php
namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ResponseController as ResponseController;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Lesson;
use App\Http\Models\Video;
use Illuminate\Support\Facades\Storage;
// use App\Http\Models\v1\Facebook;
// use App\Http\Models\v1\Location;

class LessonController extends ResponseController {

  public function create(Request $request){
    $aInput=$request->all();

    $validator=Validator::make($request->all(), [
      'name' => 'required',
      'category' => 'required',
      'type' => 'required',
      'price' => 'required',
      'net' => 'required',
      'cover' => 'file|mimes:jpeg,png,jpg,gif|max:5120',
      'lesson' => 'file|mimes:mp4,mpeg|max:51200'
    ]);

    if($validator->fails()){
      return $this->sendError($validator->errors());
    }

    $success=array();
    $success['request']=$request->all();
    $aUser = Auth::user();
    $destPath='/lessons/tmp/';
    if ($request->file('cover')) {
      $imgCover=$request->file('cover');
      $imgSurname=mb_strtolower($imgCover->getClientOriginalExtension());
      $imgName=sprintf("%05d_%s.%s",
        $aUser->id,
        date('YmdHis'),
        $imgSurname);
      $destCover=sprintf("%s%s",$destPath,$imgName);
      $aReturn['destCover']=$destCover;
//      if ($request->file('cover')->storeAs($destPath,$imgCover)) {
      if ($request->file('cover')->storeAs('public'.$destPath,$imgName)) {
        $upload['old_cover']=url(sprintf("storage%s%s",$destPath,$imgName));
        $orgImageFile=sprintf("public%s%s",$destPath,$imgName);
      }
    }
    $myDataLesson=array(
      "title" => $aInput['name'],
      "cat_id"=> $aInput['category'],
      "type" => $aInput['type'],
      "price" => $aInput['price'],
      "net" => $aInput['net'],
      "cover" => sprintf("storage%s%s",$destPath,$imgName),
      "room_id" => Auth::user()->id
    );
    if (isset($aInput['tag'])) $myDataLesson['tag']=$aInput['tag'];
    if (isset($aInput['detail'])) $myDataLesson['note']=$aInput['detail'];

    $myLesson = Lesson::create($myDataLesson);
    if (! $myLesson) {
      // Delete Cover Image
      Storage::delete(sprintf('public%s%s',$destPath,$imgName));
      // Delete Lesson Video
      if ($request->file('lesson')) {
        Storage::delete($request->file('lesson'));
      }
      $result=array("result"=>0,"message"=>'Cannot Add Lesson');
      return $this->sendError($result);
    }

    $newPath='/lessons/'.$myLesson->id.'/';
    $newImageName=sprintf("cover.%s",$imgSurname);
    $newImageFile=sprintf("public%s%s",$newPath,$newImageName);
    Storage::move($orgImageFile,$newImageFile);
    $myLesson->cover=sprintf("storage%s%s",$newPath,$newImageName);
    $upload['new_cover']=url($myLesson->cover);
    $myLesson->save();

    if ($request->file('lesson')) {
      $vdoLesson=$request->file('lesson');
      $vdoName=sprintf("%s.%s",
        date('YmdHis'),
        mb_strtolower($vdoLesson->getClientOriginalExtension()));
      $destFile=sprintf("%s%s",$newPath,$vdoName);
      $aReturn['destVideo']=$destFile;
//      if ($request->file('lesson')->storeAs($destPath,$vdoLesson)) {
      if ($request->file('lesson')->storeAs('public'.$newPath,$vdoName)) {
        $myDataVideo = array(
          "lesson_id" => $myLesson->id,
          "link" => sprintf("storage%s%s",$newPath,$vdoName)
        );
        $upload['lesson']=url($myDataVideo['link']);
// Video DB
        $myVideo = Video::create($myDataVideo);
        if (! $myVideo) {
          Storage::delete(sprintf('public%s%s',$newPath,$vdoName));
          Storage::delete(sprintf('public%s%s',$newPath,$newImageName));
          $myLesson->delete();
          $result=array("result"=>0,"message"=>'Cannot Add Video Lesson');
          return $this->sendError($result);
        }
      }
    }

    $result=array("result"=>1,"message"=>"Successful");

    return $this->sendResponse($result);
  }

}
