<?php
namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ResponseController as ResponseController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Validator;
use App\Http\Models\Payment;
use App\Http\Models\MyWallet;
use Illuminate\Support\Facades\DB;

class PaymentController extends ResponseController {

  public function topup(Request $request){
    $return=array(
      "status"  => false,
      "message" => null,
      "code"    => 0,
      "data"    => null
    );
    $user=Auth::user();

    $aInput=$request->all();


    $validator=Validator::make($request->all(), [
      'type' => 'required',
      'data' => 'required',
      'payment' => 'required|file|mimes:jpeg,png,jpg,gif|max:5120',
    ]);

    $Wallet=MyWallet::where('user_id','=',$user->id)
      ->where('type','=',1)
      ->get();
    if ($Wallet->count()==0) {
      $aInsert=array("type"=>1,"user_id"=>$user->id);
      $Wallet=MyWallet::create($aInsert);
    }
    $destPath='/payment/'.$user->id.'/';
    if ($request->file('payment')) {
      $paidFile=$request->file('payment');
      $paidSurname=mb_strtolower($paidFile->getClientOriginalExtension());
      $paidName=sprintf("paid_%s.%s",date('YmdHis'),$paidSurname);
      $destPaid=sprintf("%s%s",$destPath,$paidName);
      $aReturn['destPaid']=$destPaid;
//      if ($request->file('cover')->storeAs($destPath,$imgCover)) {
      if ($request->file('payment')->storeAs('public'.$destPath,$paidName)) {
        $upload['payment']=sprintf("storage%s%s",$destPath,$paidName);
        $upload['url']=url(sprintf("storage%s%s",$destPath,$paidName));
        $orgCertFile=sprintf("public%s%s",$destPath,$paidName);

        $aInsert=array(
          'wallet_id' => $Wallet->id,
          'type' => 'topup',
          'note'=>sprintf("%s - %s",$aInput['type'],$aInput['data']),
          'amount' => $aInput['amount'],
          'files' => $upload['payment'],
          'created_uid' => 99,
          'updated_uid' => 99,
        );
        $myWIP=Payment::create($aInsert);
        if (! $myWIP) {
          Storage::delete(sprintf("public%s%s",$destPath,$certName));
          $return=array("status"=>false,"message"=>'Cannot add certificate.');
          return $this->sendResponse($return);
        }
        $return['status']=true;
      }
    }

    return $this->sendResponse($return);
  }

}
