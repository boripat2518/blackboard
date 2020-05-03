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
use App\Http\Models\Certificate;
use Illuminate\Support\Facades\Storage;

class CertificateController extends ResponseController {

  public function create(Request $request){
    $aInput=$request->all();

    $validator=Validator::make($request->all(), [
      'note' => 'required',
      'cert' => 'required|file|mimes:jpeg,png,jpg,gif,pdf|max:5120',
    ]);

    if($validator->fails()){
      $valids=$validator->errors()->toArray();
      $err_msg=null;
      foreach ($valids as $valid) {
        foreach ($valid as $vals) {
          if ($err_msg) $err_msg.=",\n";
          $err_msg.=sprintf("%s",$vals);
        }
      }
      $aResult=array("status"=>false,"message"=>$err_msg);
      return $this->sendResponse($aResult);
    }

    $user=Auth::user();
    $room=Room::where('user_id','=',$user->id)->first();
    $destPath='/rooms/'.$room->id.'/';


    if ($request->file('cert')) {
      $certFile=$request->file('cert');
      $certSurname=mb_strtolower($certFile->getClientOriginalExtension());
      $certName=sprintf("cert_%s.%s",date('YmdHis'),$certSurname);
      $destCert=sprintf("%s%s",$destPath,$certName);
      $aReturn['destCert']=$destCert;
//      if ($request->file('cover')->storeAs($destPath,$imgCover)) {
      if ($request->file('cert')->storeAs('public'.$destPath,$certName)) {
        $upload['cert']=sprintf("storage%s%s",$destPath,$certName);
        $upload['url']=url(sprintf("storage%s%s",$destPath,$certName));
        $orgCertFile=sprintf("public%s%s",$destPath,$certName);

        $aInsert=array(
          'note'=>$aInput['note'],
          'room_id'=>$room->id,
          'file_url'=>sprintf("storage%s%s",$destPath,$certName)
        );
        $myCert=Certificate::create($aInsert);
        if (! $myCert) {
          Storage::delete(sprintf("public%s%s",$destPath,$certName));
          $result=array("status"=>false,"message"=>'Cannot add certificate.');
          return $this->sendResponse($result);
        }
      }
    }

    $aResult=array("status"=>true,"message"=>"Successful.");
    return $this->sendResponse($aResult);
  }

  public function list(Request $request){
    $aResult=array("status"=>false,"result"=>array());
    $user = Auth::user();
    $room = Room::where('user_id','=',$user->id)->first();
    if (! $room) {
      return $this->sendResponse($aResult);
    }
    $certs = Certificate::where('room_id','=',$room->id)
      ->where('active','=',1)
      ->orderBy('status','DESC')
      ->get();
    foreach($certs as $cert) {
      $aResult['result'][]=array(
        "id" => $cert->id,
        "note" => $cert->note,
        "status" => $cert->status
      );
    }
    $aResult['status']=true;
    return $this->sendResponse($aResult);
  }

  public function user_list(Request $request,$id=0){
    $aResult=array("status"=>false,"message"=>null,"code"=>0,"result"=>array());
    $room = Room::where('id','=',$id)->first();
    if (! $room) {
      $aResut['message']="Can not find room.";
      return $this->sendResponse($aResult);
    }

    $certs = Certificate::where('room_id','=',$room->id)
      ->where('active','=',1)
      ->where('status','=',1)
      ->orderBy('status','DESC')
      ->get();
    foreach($certs as $cert) {
      $aResult['result'][]=array(
        "id" => $cert->id,
        "note" => $cert->note,
        "status" => $cert->status
      );
    }
    $aResult['status']=true;
    return $this->sendResponse($aResult);
  }

  public function delete(Request $request){
  }

}
