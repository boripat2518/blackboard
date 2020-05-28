<?php
namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ResponseController as ResponseController;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Auth;
use Validator;
use App\User;
use App\Http\Models\Room;
use App\Http\Models\Lesson;

class RoomController extends ResponseController {

  public function create(Request $request){
    $aInput=$request->all();

    $user = Auth::user();
    $validator=Validator::make($request->all(), [
      'name' => 'required',
      'cover' => 'required|file|mimes:jpeg,png,jpg,gif|max:5120',
    ]);

    if($validator->fails()){
      $result=array("error"=>$validator->errors());
      return $this->sendError($result);
    }

    $myRoom=Room::where('user_id','=',$user->id)->first();
    if ($myRoom) {
      $result=array("message"=>'This user already have thier room.');
      return $this->sendError($result);
    }

    $destPath='/rooms/tmp/';
    if ($request->file('cover')) {
      $imgCover=$request->file('cover');
      $imgSurname=mb_strtolower($imgCover->getClientOriginalExtension());
      $imgName=sprintf("cover.%s",$imgSurname);
//      if ($request->file('cover')->storeAs($destPath,$imgCover)) {
      if ($request->file('cover')->storeAs('public'.$destPath,$imgName)) {
        $imgCoverFile=sprintf("storage%s%s",$destPath,$imgName);
        $orgImageFile=sprintf("public%s%s",$destPath,$imgName);
      } else {
        $result=array("message"=>'Cannot Add Room');
        return $this->sendError($result);
      }
    }

    $aInsert=array(
      "id"      => $user->id,
      "user_id" => $user->id,
      "name"    => $aInput['name'],
      "note"    => $aInput['detail'],
      "cover"   => $imgCoverFile,
    );
    $myRoom=Room::create($aInsert);

    $newPath='/rooms/'.$myRoom->id.'/';
    $newImageName=sprintf("cover.%s",$imgSurname);
    $newImageFile=sprintf("public%s%s",$newPath,$newImageName);
    Storage::move($orgImageFile,$newImageFile);
    $myRoom->cover=sprintf("storage%s%s",$newPath,$newImageName);
    $myRoom->save();


    $result=array("result"=>1,"message"=>"Successful");

    return $this->sendResponse($result);
  }

  public function update(Request $request) {
    $aReturn=array("status"=>false,"message"=>"","code"=>0,"result"=>null);
    $aInput=$request->all();
//    $aReturn['debug']=$aInput;
    $user = Auth::user();
    $myRoom=Room::where('user_id','=',$user->id)->first();

    if (! $myRoom) {
      // No found room
      $aReturn["message"]='This user already have thier room.';
      return $this->sendError($aReturn);
    }
    $destPath='/rooms/'.$myRoom->id.'/';
    if ($request->file('cover')) {
      $imgCover=$request->file('cover');
      $imgSurname=mb_strtolower($imgCover->getClientOriginalExtension());
      $imgName=sprintf("cover.%s",$imgSurname);
      $newImageFile=sprintf("public%s%s",$destPath,$imgName);
      Storage::delete($newImageFile);
      if ($request->file('cover')->storeAs('public'.$destPath,$imgName)) {
        $aReturn['message']="Successful.";
        $myRoom->cover=sprintf("storage%s%s",$destPath,$imgName);
      }
    }
    if ($aInput['name'] != $myRoom->name) {
      $myRoom->name = $aInput['name'];
    }
    if ($aInput['detail'] != $myRoom->note) {
      $myRoom->note = $aInput['detail'];
    }
    $myRoom->save();

    $aReturn['message']="Successful.";
    $aReturn['status']=true;
    return $this->sendResponse($aReturn);
  }


  public function profile(Request $request){
    $aReturn=array("status"=>false,"message"=>"","code"=>0,"result"=>null);
    $user = Auth::user();
    $myRoom=Room::where('user_id','=',$user->id)->first();

    if (! $myRoom) {
      $aReturn["message"]='This user already have thier room.';
      return $this->sendError($aReturn);
    }
    $photo_url=(is_null($user->photo_url))?'stroage/images/avatar.png':$user->photo_url;
    $myLesson = Lesson::where('room_id','=',$myRoom->id)->get();
    $aResult=array(
      "id"=>$myRoom->id,
      "avatar"=>url($photo_url),
      "cover"=>url($myRoom->cover),
      "name"=>$myRoom->name,
      "detai"=>$myRoom->note,
      "favorite"=>$this->count_favorite($myRoom->id),
      "vote"=>$this->count_vote($myRoom->id),
      "comment"=>$this->count_comment($myRoom->id)
    );
    $aResult["lesson"]=array("total"=>$myLesson->count());
    $aReturn['status']=true;
    $aReturn['result']=$aResult;
    $aReturn['message']="Successful.";

    return $this->sendResponse($aReturn);
  }

  public function count_favorite($id=0) {
    return 0;
  }

  public function count_vote($id=0) {
    return 0;
  }

  public function count_comment($id=0) {
    return 0;
  }

}
