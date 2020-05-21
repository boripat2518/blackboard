<?php
namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ResponseController as ResponseController;
use App\Http\Models\MyWallet;
use App\Http\Models\LogWallet;
use App\Http\Models\Lesson;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class WalletController extends ResponseController {

  public function wallet(Request $request){
    $user=Auth::user();
    $return = array(
      "status"  => false,
      "message" => "",
      "code"    => 0,
      "data"    => null,
    );
    date_default_timezone_set('Asia/Bangkok');
    $aData=array(
      "amount"      => 0,
      "last_updated"=> date('Y-m-d H:i:s',"+7"),
    );
    $dData = MyWallet::where('user_id','=',$user->id)
      ->where('type','=',1)
      ->first();
    if ($dData) {
      $return['status'] = true;
      $aData=array(
        "amount"        => floatVal($dData->current),
        "last_updated"  => Carbon::createFromFormat('Y-m-d H:i:s', $dData->updated_at)->format('Y-m-d H:i:s'),
      );

    }
    $return['data'] = $aData;
    return $this->sendResponse($return);
  }

  public function shop_wallet(Request $request){
    $user=Auth::user();
    $return = array(
      "status"  => false,
      "message" => "",
      "code"    => 0,
      "data"    => null,
    );
    date_default_timezone_set('Asia/Bangkok');
    $aData=array(
      "amount"      => 0,
      "last_updated"=> date('Y-m-d H:i:s'),
    );
    $dData = MyWallet::where('user_id','=',$user->id)
      ->where('type','=',2)
      ->first();
    if ($dData) {
      $return['status'] = true;
      $aData=array(
        "amount"        => floatVal($dData->current),
        "last_updated"  => Carbon::createFromFormat('Y-m-d H:i:s', $dData->updated_at)->format('Y-m-d H:i:s'),
      );

    }
    $return['data'] = $aData;
    return $this->sendResponse($return);
  }

  public function lesson_buy(Request $request){
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
    // User Wallet
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
    // Lesson Info
    $Lesson = Lesson::where('id','=',$aInput['lesson_id'])->first();
    if (! $Lesson) {
    // No Found Lesson
      $return['message'].='No Found Lesson';
      return $this->sendResponse($return);
    }
    $Room = Room::where('id','=',$Lesson->room_id)->first();
    if (! $Room) {
    // No Found Lesson
      $return['message'].='No Found Room';
      return $this->sendResponse($return);
    }
    // Shop Wallet
    $dSWallet = MyWallet::where('user_id','=',$Room->user_id)
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
    // Find % Discount
    $Discount=$this->Lesson_discount($Lesson->id);

    if ($dUWallet->current < $Lesson->net) {
      $return['message'].='No enough coin.';
      return $this->sendResponse($return);
    }

    $price=$Lesson->net;
    $rate_discount = $this->Lesson_discount($Lesson->id);
    $discount=round($price * $rate_discount,2);
    $amount = round($price - $discount,2);

    // Student Transcation OUT
    $aUser = array(
      "wallet_id"=>$dUWallet->id,
      "current"=>$dUWallet->current,
      "type"=>"BUY",
      "note"=>sprintf("buy '%d' transfer '%d' => '%d' with '%0.2f'",
        $Lesson->id,$dUWallet->id,$dSWallet->id,$price),
      "files"=>"/storage/payment/system.jpg",
      "amount"=>$price,
      "status"=>1,
    );

    // Shop Transcation IN
    $aShop = array(
      "wallet_id"=>$dSWallet->id,
      "current"=>$dSWallet->current,
      "type"=>"TRANSFER",
      "note"=>sprintf("buy '%d' transfer '%d' => '%d' with '%0.2f'",
        $Lesson->id,$dUWallet->id,$dSWallet->id,$amount),
      "files"=>"/storage/payment/system.jpg",
      "amount"=>$amount,
      "status"=>1,
    );

    // CENTER Transcation IN
    $aCenter = array(
      "wallet_id"=>99,
      "current"=>$dSWallet->current,
      "type"=>"BANK",
      "note"=>sprintf("buy '%d' transfer '%d' => '%d' with '%0.2f'",
        $Lesson->id,$dUWallet->id,$dSWallet->id,$amount),
      "files"=>"/storage/payment/system.jpg",
      "amount"=>$discount,
      "status"=>1,
    );

    $Logs = LogWallet::create($aInsert);

    return $this->sendResponse($return);

  }

  public function Lesson_discount($id=0,$coupon=0) {
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
    } elseif ($Lesson->created_at < $OneYear) {
      $discount=25;
    } else {
      $discount=20;
    }
    return floatVal($discount/100);
  }
}
