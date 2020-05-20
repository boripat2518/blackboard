<?php
namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ResponseController as ResponseController;
use App\Http\Models\MyWallet;
use App\Http\Models\LogWallet;
use App\Http\Models\Lesson;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MyWalletController extends ResponseController {

  public function wallet(Request $request){
    $user=Auth::user();
    $return = array(
      "status"  => false,
      "message" => "",
      "code"    => 0,
      "data"    => null,
    );
    $aData=array(
      "amount"      =>0,
      "last_updated"=>date('Y-m-d H:i:s'),
    );
    $dData = MyWallet::where('user_id','=',$user->id)
      ->where('type','=',1)
      ->first();
    if ($dData) {
      $return['status']=true;
      $aData=array(
        "amount"        => floatVal($dData->current),
        "last_updated"  => Carbon::createFromFormat('Y-m-d H:i:s', $dData->updated_at)->format('Y-m-d H:i:s'),
      );

    }
    $return['data'] = $aData;
    return $this->sendResponse($return);

  }

  public function buy(Request $request){
    $aInput=$request->all();
    $user=Auth::user();
    $return = array(
      "status"  => false,
      "message" => "",
      "code"    => 0,
      "data"    => null,
    );
    $aData=array(
      "amount"      =>0,
      "last_updated"=>date('Y-m-d H:i:s'),
    );
    $dUWallet = MyWallet::where('user_id','=',$user->id)
      ->where('type','=',1)
      ->first();
    if (! $dUWallet) {
      $aInsert=array(
        "user_id" => $user->id,
        "type"    => 1,
        "current" => 0,
      );
      $dUWallet=MyWallet::create($aInsert);
    }

    $Lesson = Lesson::where('id','=',$aInput['lesson_id'])->get();
    if (! $Lesson) {
      $return['message'].='No Found Lesson';
    } elseif (! $dUWallet) {
        $return['message'].='No Wallet Found.';
    } else {
      $Discount=$this->Lesson_discount($Lesson->id);
      $Room = Room::where('id','=',$Lesson->room_id)->get();
      if (! $Room) {
        $return['message'].='No Room Found.';
      } else {
        $dSWallet = MyWallet::where('user_id','=',$room->user_id)
          ->where('type','=',2)
          ->first();
        if (! $dSWallet) {
          $aInsert=array(
            "user_id" => $room->user_id,
            "type"    => 2,
            "current" => 0,
          );
          $dSWallet=MyWallet::create($aInsert);
        }
        if ($dUWallet->current < $Lesson->net) {
          $return['message'].='No enough coin.';
        } else {
          $discount = $this->Lesson_discount($Lesson->id);
          $amount = round($Lesson->net * $discount,2);
          $remain = $dUWallet->current - $amount;
        }
      }
    } else {

    }
    return $this->sendResponse($return);

  }

  public function Lesson_discount($id=0) {
    $discount=20;
    $Lesson = Lesson::findorFail($id);
    $dtNow = Carbon::now();
    $OneYear = Carbon::now()->subYear(1);
    $TwoYear = Carbon::now()->subYear(2);
    $ThreeYear = Carbon::now()->subYear(3);
    if ($Lesson->created_at < $ThreeYear) {
      $discount=50;
    } elseif ($Lesson->created_at < $TwoYear) {
      $discount=30;
    } elseif ($Lesson->created_at > $OneYear) {
      $discount=25;
    } else {
      $discount=20;

    }
    return floatVal($discount/100);
  }
}
