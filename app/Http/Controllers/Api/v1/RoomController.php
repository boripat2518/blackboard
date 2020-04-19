<?php
namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ResponseController as ResponseController;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Auth;
use Validator;
use App\User;
use App\Http\Models\Room;

class RoomController extends ResponseController {

  public function create(Request $request){
    $aInput=$request->all();

    $user = Auth::user();
    $validator=Validator::make($request->all(), [
      'name' => 'required',
      'cover' => 'required|file|mimes:jpeg,png,jpg,gif|max:5120',
    ]);

    if($validator->fails()){
      return $this->sendError($validator->errors());
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
        $result=array("result"=>0,"message"=>'Cannot Add Room');
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


}
