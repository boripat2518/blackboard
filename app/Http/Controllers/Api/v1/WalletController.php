<?php
namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ResponseController as ResponseController;
use App\Http\Models\MyWallet;
use App\Http\Models\LogWallet;
use App\Http\Models\Room;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\Lesson;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WalletController extends ResponseController {
  private $system_wallet=99;
  private $system_user=99;

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
        "user_id" => $Room->user_id,
        "type"    => 2,
        "current" => 0,
      );
      $dSWallet=MyWallet::create($aInsert);
    }
    if ($dSWallet->user_id == $dUWallet->user_id) {
      $return['message'].='Cannot buy your own lesson.';
      return $this->sendResponse($return);
    }
    // Find % Discount
    $Discount=$this->Lesson_discount($Lesson->id);

    $return['debug']['lesson']=$Lesson;
    $return['debug']['wallet']['student']=$dUWallet;
    $return['debug']['wallet']['teacher']=$dSWallet;

    if ($dUWallet->current < $Lesson->net) {
      $return['message'].='No enough coin.';
      return $this->sendResponse($return);
    }

    $price=$Lesson->net;
    $rate_discount = $this->Lesson_discount($Lesson->id);
    $discount=round($price * $rate_discount,2);
    $amount = round($price - $discount,2);

    // Student Transcation OUT
    $aStudent = array(
      "wallet_id"=>$dUWallet->id,
      "current"=>$dUWallet->current,
      "type"=>"BUY",
      "note"=>sprintf("buy '%d' transfer '%d' => '%d' with '%0.2f'",
        $Lesson->id,$dUWallet->id,$dSWallet->id,$price),
      "files"=>"/storage/payment/system.jpg",
      "amount"=>$price,
      "created_uid"=>$this->system_user,
      "status"=>1,
    );

    // Shop Transcation IN
    $aTeacher = array(
      "wallet_id"=>$dSWallet->id,
      "current"=>$dSWallet->current,
      "type"=>"TRANSFER",
      "note"=>sprintf("buy '%d' transfer '%d' => '%d' with '%0.2f'",
        $Lesson->id,$dUWallet->id,$dSWallet->id,$amount),
      "files"=>"/storage/payment/system.jpg",
      "amount"=>$amount,
      "created_uid"=>$this->system_user,
      "status"=>1,
    );

    // CENTER Transcation IN
    $aCenter = array(
      "wallet_id"=>99,
      "current"=>$dSWallet->current,
      "type"=>"INCOME",
      "note"=>sprintf("buy '%d' transfer '%d' => '%d' with '%0.2f'",
        $Lesson->id,$dUWallet->id,$this->system_wallet,$discount),
      "files"=>"/storage/payment/system.jpg",
      "amount"=>$discount,
      "created_uid"=>$this->system_user,
      "status"=>1,
    );
    $return['debug']['wallet_log']['student']=$aStudent;
    $return['debug']['wallet_log']['teacher']=$aTeacher;
    $return['debug']['wallet_log']['center']=$aCenter;

    $logStudent = LogWallet::create($aStudent);
    if ($logStudent) {
      if (! $this->wallet_decrease($dUWallet->id,$price)) {
        $return['message'] = "Not enough coin.";
        return $this->sendResponse($return);
      }
    }

    $logTeacher = LogWallet::create($aTeacher);
    if ($logTeacher) {
      if (! $this->wallet_increase($dSWallet->id,$amount)) {
        $return['message'] = "Cannot add coin to teacher";
        if (! $this->wallet_increase($dUWallet->id,$price)) {
          $return['message'] = "Cannot return coin.";
          return $this->sendResponse($return);
        }
        return $this->sendResponse($return);
      }
    }
    $logCenter = LogWallet::create($aCenter);
    if ($logCenter) {
      if (! $this->wallet_increase($this->system_wallet,$discount)) {
        $return['message'] = "Cannot add discount to center account";
        if (! $this->wallet_decrease($dSWallet->id,$amount)) {
          $return['message'] = "Cannot return coin from teacher";
          return $this->sendResponse($return);
        }
        if (! $this->wallet_increase($dUWallet->id,$price)) {
          $return['message'] = "Cannot return coin to student";
          return $this->sendResponse($return);
        }
        return $this->sendResponse($return);
      }
    }
    $insert=DB::table('lesson_purchases')->insert(
      ['lesson_id' => $Lesson->id,'user_id'=>$user->id]
    );
    if ($insert === null) {
      $return['message'] = "Cannot add purchase lesson '".$Lesson->id."'";
      return $this->sendResponse($return);
    }

    $return['status']=true;
    $return['message']="successful.";
    return $this->sendResponse($return);
  }

  public function adjust(Request $Request) {
    $return=array();
    $aInput=$Request->all();
    $result=$this->wallet_adjust($aInput['wallet_id'],$aInput['amount']);
    $return=array("input"=>$aInput,"result"=>$result);
    return $this->sendResponse($return);
  }

  public function increase(Request $Request) {
    $return=array();
    $aInput=$Request->all();
    $result=$this->wallet_increase($aInput['wallet_id'],$aInput['amount']);
    $return=array("input"=>$aInput,"result"=>$result);
    return $this->sendResponse($return);
  }

  public function decrease(Request $Request) {
    $return=array();
    $aInput=$Request->all();
    $result=$this->wallet_decrease($aInput['wallet_id'],$aInput['amount']);
    $return=array("input"=>$aInput,"result"=>$result);
    return $this->sendResponse($return);
  }

  private function wallet_adjust($id=0,$amount=0) {
    $myWallet=MyWallet::find($id);
    if ($myWallet === null ) return false;
    $myWallet->current = $amount;
    return $myWallet->save();
  }

  private function wallet_increase($id=0,$amount=0) {
    $myWallet=MyWallet::find($id);
    if ($myWallet === null ) return false;
    $myWallet->current += $amount;
    return $myWallet->save();
  }

  private function wallet_decrease($id=0,$amount=0) {
    $myWallet=MyWallet::find($id);
    if ($myWallet === null ) return false;
    $myWallet->current -= $amount;
    return $myWallet->save();
  }

}
