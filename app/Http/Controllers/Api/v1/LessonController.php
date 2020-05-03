<?php
namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ResponseController as ResponseController;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Room;
use App\Http\Models\Lesson;
use App\Http\Models\Category;
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
      'cover' => 'required|file|mimes:jpeg,png,jpg,gif|max:5120',
      'lesson' => 'required|file|mimes:mp4,mpeg|max:524288'
    ]);

    if($validator->fails()){
      return $this->sendError($validator->errors());
    }

    $success=array();
    $success['request']=$request->all();
    $aUser = Auth::user();
    $myRoom = Room::where('user_id','=',$aUser->id)->first();

    if (! $myRoom) {
      $error=array("message"=>"No room information.");
      return $this->sendError($error);
    }

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
      "room_id" => $myRoom->id
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
    Storage::delete($newImageFile);
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

  public function show(Request $request,$id=0){
    $return=array("lesson"=>null,"vdo_list"=>null);
    $lesson=Lesson::where('id','=',$id);
    if (! $lesson) {
      return $this->sendError("Failed");
    }
    $videos=Video::where('lesson_id','=',$id)->get();
    if (! $videos) {
      return $this->sendError("Failed");
    }
    $return['lesson']=array(
      "id" => $lesson->id,
      "cat_id" => $lesson->cat_id,
      "room_id" => $lesson->room_id,
      "type" => $lesson->type,
      "title" => $lesson->title,
      "desc" => $lesson->note,
      "tag" => $lesson->tag,
      "price" => floatval($lesson->price),
      "net" => floatval($lesson->net),
      "cover" => url($lesson->cover)
    );
    foreach ($videos as $video) {
      $return['vdo_list'][]=array(
        "id" => $video->id,
        "url" => url($video->link)
      );
    }

    return $this->sendResponse($return);
  }

  public function update(Request $request,$id=0){
    $aInput=$request->all();
    $myLesson=Lesson::findorFail($id);

    $validator=Validator::make($request->all(), [
      'name' => 'required',
      'category' => 'required',
      'type' => 'required',
      'price' => 'required',
      'net' => 'required',
      'cover' => 'file|mimes:jpeg,png,jpg,gif|max:5120',
      'lesson' => 'file|mimes:mp4,mpeg|max:512000'
    ]);

    if($validator->fails()){
      return $this->sendError($validator->errors());
    }

    $destPath='/lessons/'.$myLesson->id.'/';
    if ($request->file('cover')) {
      $imgCover=$request->file('cover');
      $imgSurname=mb_strtolower($imgCover->getClientOriginalExtension());
      $imgName=sprintf("cover.%s",$imgSurname);
      $destCover=sprintf("%s%s",$destPath,$imgName);
//      if ($request->file('cover')->storeAs($destPath,$imgCover)) {
      if ($request->file('cover')->storeAs('public'.$destPath,$imgName)) {
        $myLesson->cover=sprintf("storage%s%s",$destPath,$imgName);
      }
    }

    if ($request->file('lesson')) {
      $vdoLesson=$request->file('lesson');
      $vdoSurname=mb_strtolower($vdoLesson->getClientOriginalExtension());
      $vdoName=sprintf("%s.%s",date('YmdHis'),$vdoSurname);
      $destLesson=sprintf("%s%s",$destPath,$imgName);
//      if ($request->file('cover')->storeAs($destPath,$imgCover)) {
      if ($request->file('lesson')->storeAs('public'.$destPath,$vdoName)) {
        $myLesson->lesson=sprintf("storage%s%s",$destPath,$vdoName);
      }
    }
    if ($myLesson->title != $aInput['name']) $myLesson->title = $aInput['name'];
    if ($myLesson->cat_id != $aInput['category']) $myLesson->cat_id = $aInput['category'];
    if ($myLesson->type != $aInput['type']) $myLesson->type = $aInput['type'];
    if ($myLesson->price != $aInput['price']) $myLesson->price = $aInput['price'];
    if ($myLesson->net != $aInput['net']) $myLesson->net = $aInput['net'];
    if ($myLesson->note != $aInput['detail']) $myLesson->note = $aInput['detail'];
    if ($myLesson->tag != $aInput['tag']) $myLesson->tag = $aInput['tag'];

    $myLesson->save();

    $return=array("result"=>1,"message"=>"Successful.");
    return $this->sendResponse($return);
  }

  public function searchByCategory(Request $request){
    $input = $request->all();
    $aResult = array(
      "status"  => false,
      "message" => null,
      "code"    => 0,
      "data"    => null
    );
    // Find category
    if (! isset($input['cat']) ) {
      $aResult['message'] = "Can not found category";
      return $this->sendResponse($aResult);
    }

    $category = Category::where('id','=',$input['cat'])->first();

    if (! $category) {
      $aResult['message'] = "Can not found category";
      return $this->sendResponse($aResult);
    }

    $page=(isset($input['page']))?$input['page']:0;
    $perpage=(isset($input['perpage']))?$input['perpage']:10;

    $lessons = Lesson::where('cat_id','=',$input['cat'])
      ->orderBy('type')
      ->orderBy('title')
      ->paginate($perpage);

    if ($lessons->count() == 0 ) {
      $aResult['message'] = "Can not find any lesson.";
      return $this->sendResponse($aResult);
    } else {
      $aResult["status"] = true;
      $aLesson=array();
      $pageLesson=$lessons->toArray();
//      print_r($pageLesson);
      foreach($pageLesson['data'] as $myLesson) {
        $aLesson[]=array(
          "id" => $myLesson['id'],
          "cat_id" => $myLesson['cat_id'],
          "room_id" => $myLesson['room_id'],
          "title" => $myLesson['title'],
          "note" => $myLesson['note'],
          "type" => $myLesson['type'],
          "tag" => $myLesson['tag'],
          "cover" => url($myLesson['cover']),
          "price" => floatval($myLesson['price']),
          "net" => floatval($myLesson['net']),
//          "favorite" => $this->isFavorite($myLesson['id']),
          "rate" => $this->rating($myLesson['id']),
          "view" => $this->countView($myLesson['id']),
        );
      }
      $myData=array(
        "pagination"=>array(
          "page"  => intVal($pageLesson['current_page']),
          "total"   => intVal($pageLesson['total']),
          "perpage" => intVal($pageLesson['per_page']),
          "offset"  => intVal($pageLesson['from']),
        ),
//        "result"=>$pageLesson,
        "result"=>$aLesson,
      );
      $aResult['data']=$myData;
//      $aResult['lessons']=$pageLesson;
    }
    return $this->sendResponse($aResult);
  }

  public function listByCategory(Request $request){
    $input=$request->all();
    $aResult=array(
      "status"  => false,
      "message" => null,
      "code"    => 0,
      "data"    => null
    );
    $category = Category::where('id','=',$input['cat'])->first();
    if (! $category) {
      $aResult['message'] = "Can not found category";
      return $this->sendError($aResult,401);
    }
    $page=(isset($input['page']))?$input['page']:0;
    $perpage=(isset($input['pp']))?$input['pp']:10;
    $lessons = Lesson::where('cat_id','=',$id)
      ->orderBy('type')
      ->orderBy('title')
      ->paginate($perpage);

    return $this->sendResponse($aResult);
  }

  public function myLesson(Request $request){
    $aResult=array(
      "status"  => false,
      "message" => null,
      "code"    => 0,
      "data"    => null
    );
    // Find category
    $user=Auth::user();
    $myRoom = Room::where('user_id','=',$user->id)->first();

    if (! $myRoom) {
      $aResult['message'] = "You don't have any room.";
      return $this->sendError($aResult,401);
    }

    $lessons = Lesson::where('room_id','=',$myRoom->id)
      ->orderBy('type')
      ->orderBy('title')
      ->get();

    $aResult["status"] = true;
    if (! $lessons ) {
      $aResult['message'] = "Can not find any lesson.";
    } else {
      $myDatas=array();
      foreach ($lessons as $lesson) {
        $myDatas[]=array(
          "id" => $lesson->id,
//          "cat_id" => $lesson->cat_id,
//          "room_id" => $lesson->room_id,
          "title" => $lesson->title,
          "type" => $lesson->type,
          "tag" => $lesson->tag,
          "cover" => url($lesson->cover),
          "price" => floatval($lesson->price),
          "net" => floatval($lesson->net),
//          "rate" => 0,
//          "favorite" => 0,
          "count" => 0
        );
      }
      $aResult['data']=$myDatas;
    }
    return $this->sendResponse($aResult);
  }

  protected function isFavorite($id=0) {
    $valReturn=0;
    $user=Auth::user();
    if ($user) {
      $counter=DB::table('lesson_favorites')
        ->where('lesson_id','=',$id)
        ->where('user_id','=',$user->id)
        ->count();
      $valReturn=intVal($counter);
    }
    return intval($valReturn);
  }

  protected function rating($id=0) {
    $counter=DB::table('lesson_rates')
      ->where('lesson_id','=',$id)
      ->avg('rate');
    return intval($counter);
  }

  protected function countView($id=0) {
    $counter=0;
//    $counter=DB::table('lesson_views')
//      ->where('lesson_id','=',$id)
//      ->count();
    return intval($counter);
  }

}
