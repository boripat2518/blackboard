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

  class UserController extends ResponseController {

    public $successStatus = 200;
    /**
     * login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request){

      $validator = Validator::make($request->all(), [
          'email' => 'required|string|email',
          'password' => 'required'
      ]);

      if($validator->fails()){
        $result=array("error"=>$validator->errors());
        return $this->sendError($result);
      }

      $credentials = request(['email', 'password']);
      if(!Auth::attempt($credentials)){
        $user= User::where('email','=',$request->get('email'))->first();
        $error['message']=($user)?"Password is not correct.":"Not found email.";
        return $this->sendError($error);
      }
      $user = $request->user();
      //       $success['token'] =  $user->createToken('token')->accessToken;

      $success['access_token'] =  $user->createToken('blackboard')->accessToken;
      $success['token'] =  md5($success['access_token']).sha1($success['access_token']);
      $success['token_type'] = "user";
      $success['created_at'] = Carbon::createFromFormat('Y-m-d H:i:s', $user->created_at)->format('Y-m-d H:i:s');

      $user->api_token=$success['token'];
      $user->save();
      unset($success['token']);

      return $this->sendResponse($success);
    }
/**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
      $input = $request->json()->all();
      $user=User::where('email','=',$input['email'])->first();
      if ($user) {
        $error=array(
          "status"=>false,
          "message" => "The email has already been taken.",
          "code"=>0,
          "data"=>null
        );
        return $this->sendResponse($error);
      }
      $input['password']  = bcrypt($input['password']);
      $input['phone'] = $input['tel'];
      $user = User::create($input);
      $success=array(
        "status"=>true,
        "message" => "",
        "code"=>0,
        "data"=>null,
      );
      $success['data']['access_token'] =  $user->createToken('EasyGo')->accessToken;
      $success['data']['token_type'] = "user";
      $success['data']['created_at'] = Carbon::createFromFormat('Y-m-d H:i:s', $user->created_at)->format('Y-m-d H:i:s');
//      return response()->json(['success'=>$success], $this-> successStatus);
      return $this->sendResponse($success);
    }
/**
     * details api
     *
     * @return \Illuminate\Http\Response
     */
    public function details()
    {
      $user = Auth::user();
      return response()->json(['success' => $user], $this-> successStatus);
    }

    public function user(Request $request) {

      $user = Auth::user();
      $room = DB::table('room_infos')->where('user_id','=',$user->id)->first();

      $follow=DB::table('room_follows')->where('user_id','=',$user->id)->count();

      $follower=0;
      $favorite=0;
      if ( $room ) {
        $follower=DB::table('room_follows')->where('room_id','=',$room->id)->count();
        $favorite=DB::table('lesson_favorites')->where('room_id','=',$room->id)->count();
      }
      $avatar=url('storage/images/avatar.jpg');
      if ($user->provider=='email') {
        if (! is_null($user->photo_url)) {
          $avatar=url($user->photo_url);
        }
      } else {
        $avatar=$user->provider_photo;
      }
      $success['user']=array(
        "id"        => $user->id,
        "avatar"    => $avatar,
        "name"      => $user->name,
        "follower"  => $follower,
        "room"      => ($room)?$room->id:0
      );
      $success['favorite']=$favorite;
      $success['follow']=$follow;
      $success['language']="th";
      return $this->sendResponse($success);

  /*
      if(Auth::attempt(['remember_token' => request('token')])){
        $user = Auth::user();
        $success['user']=$requiest->$user;
        return response()->json($success, $this-> successStatus);
      }
      else{
        $success['error']='Unauthorised';
        return response()->json($success, 401);
      }
  */
    }
    public function update(Request $req)
    {
      $aReturn=array("result"=>false,"message"=>"failed.");
      $aInput = (array) $req->json()->all();
      $aInput['first_name'] = $aInput['name'];
//      $aInput['email']      = $aInput['email_address'];
      $aInput['phone']      = $aInput['phone_number'];

      $aUser = Auth::user();
      $aData=array( "first_name"=>$aUser->first_name,
                    "last_name"=>$aUser->last_name,
                    "point"=>0,
                    "email"=>$aUser->email,
                    "phone"=>$aUser->phone,
                    "province"=>$aUser->location,
                    "image_url"=>($aUser->provider=='email')?$aUser->photo_url:$aUser->provider_photo,
                  );
      $bChange=0;
      if ($aUser->first_name <> $aInput['name']) {
        $aUser->first_name = $aInput['name'];
        $bChange++;
      }
      if ($aUser->last_name <> $aInput['last_name']) {
        $aUser->last_name = $aInput['last_name'];
        $bChange++;
      }
      if ($aUser->phone <> $aInput['phone_number']) {
        $aUser->phone = $aInput['phone_number'];
        $bChange++;
      }
      if (isset($aInput['province'])) {
        if ($aUser->location <> $aInput['province']) {
          $aUser->location = $aInput['province'];
          $bChange++;
        }
      }
      $aReturn['data']=$aData;
      if ($bChange>0) {
        if ($aUser->save()) {
          $aData['first_name']  = $aUser->first_name;
          $aData['last_name']   = $aUser->last_name;
          $aData['point']       = 0;
          $aData['email']       = $aUser->email;
          $aData['phone']       = $aUser->phone;
          $aData['province']    = $aUser->location;
          $aData['image_url']   = ($aUser->provider=='email')?$aUser->photo_url:$aUser->provider_photo;
          $aReturn['result']=true;
          $aReturn['data']=$aData;
          $aReturn['message'] = "Update user information successful.";
          return response()->json($aReturn, 200);
        } else {
          $aReturn['message'] = "Cannot update user information.";
          return response()->json($Return, 401);
        }
      } else {
        $aReturn['message'] = "No user information changed.";
        return response()->json($aReturn, 401);
      }

    }

    public function change_password(Request $req) {
      $aReturn=array("result"=>false,"message"=>"failed.");
      $rCode=401;
      $aInput = (array) $req->json()->all();
      $validator = Validator::make($aInput, [
        'old_password'    => 'required|string|min:6',
        'password'    => 'required|string|min:6|confirmed',
        'password_confirmation' => 'required|string|same:password',
      ]);
      if ($validator->fails()) {
//        return response()->json(['error'=>$validator->errors()], 401);
        $aReturn['message']="Change password failed.";
      } else {
        $aUser = Auth::user();
        if (Auth::guard('web')->attempt(['email' => $aUser->email,
                          'password' => $aInput['old_password']]) ) {
          $aUser->password=bcrypt($aInput['password']);
          if ($aUser->save()) {
            $rCode=200;
            $aReturn['result']=true;
            $aReturn['message']="Successful change password.";
          } else {
            $aReturn['message']="Cannot change password.";
          }
        } else {
          $aReturn['message']="Old password not match.";
        }
      }
      return response()->json($aReturn, $rCode);
    }

    public function change_email(Request $req) {
      $aReturn=array("result"=>false,"message"=>"failed.");
      // return HTTP Status Code
      $rCode=401;
      $aInput = (array) $req->json()->all();
      $validator = Validator::make($aInput, [
        'email' => 'required|string|email|max:255|unique:users',
      ]);
      if ($validator->fails()) {
//        return response()->json(['error'=>$validator->errors()], 401);
        $aReturn['message']="Exist email.";
      } else {
        $aUser = Auth::user();
        if ($aUser->email <> $aInput['email']) {
          $aUser->email=$aInput['email'];
          if ($aUser->save()) {
            $aReturn['result']=true;
            $aReturn['message']="Successful change email address.";
            $rCode = 200;
          } else {
            $aReturn['message']="Cannot change Email.";
          }
        } else {
          $aReturn['message']="Same email address.";
        }
      }
      return response()->json($aReturn, $rCode);
    }


    public function change_avatar(Request $req) {
      $aReturn=array("result"=>false,"message"=>"failed.");
      // return HTTP Status Code
      $rCode=401;
      $validator=$this->validate($req, [
        'image' => 'required|file|mimes:jpeg,png,jpg,gif|max:2048',
      ]);
      $aReturn['file']=$req->file('image');
      if ($validator) {
        $aUser = Auth::user();
        $imgProfile=$req->file('image');
        $imgName=sprintf("%010d.%s",
                    $aUser->id,
                    mb_strtolower($imgProfile->getClientOriginalExtension()));
        $destPath='/images/users/';
        $destFile=sprintf("%s%s",$destPath,$imgName);
        $aReturn['destFile']=$destFile;
//        if ($req->file('image')->storeAs($destPath,$imgName)) {
        if ($req->file('image')->storeAs($destPath,$imgName)) {
          $aUser->photo_url=sprintf("storage%s",$destFile);
          $aUser->save();
          $aReturn['result']=true;
          $rCode=200;
          $aReturn['message']="success store";
        } else {
          $aReturn['message']="failed store";
        }
/*
        if ($imgProfile->move($destPath,$imgName)) {
          $rCode=200;
          $aReturn['uploadto']=$imgProfile->;
          $aReturn['result']=true;
          $aReturn['message']="Change user image successful.";
        } else {
          $aReturn['message']="cannot move to '".$destFile."'";
        }
*/
      }

      return response()->json($aReturn, $rCode);
    }

    public function facebook(Request $request)
    {
      $success=NULL;
      $input = (array) $request->json()->all();
      $aFacebook=Facebook::facebook_update($input);
      if (! $aFacebook['result']) {
        // Create New Facebook User
        $input['provider']    = "facebook";
        $input['provider_id'] = $input['facebook_id'];
        $input['name']        = $input['name'];
        $input['password']    = bcrypt($input['facebook_id']);
        $input['provider_photo'] = sprintf("https://graph.facebook.com/v3.2/%s/picture?type=normal",$input['facebook_id']);
        $user = User::create($input);
        $aFacebook['data']['user_id']=$user->id;
//        $success['user']=$user;
      }
//      $success['facebook']=$aFacebook;
      $user = Auth::loginUsingId($aFacebook['data']['user_id']);
//      $user = Auth::user();
//      $success['user']=$user;
      $success['access_token'] = $user->createToken('EasyGo')->accessToken;
      $success['token'] =  md5($success['access_token']).sha1($success['access_token']);
      $success['token_type'] = "Bearer";
      $success['created_at'] = Carbon::createFromFormat('Y-m-d H:i:s', $user->created_at)->format('Y-m-d H:i:s');
//          return response()->json(['success' => $success], $this-> successStatus);
      $user->api_token=$success['token'];
      $user->save();

      return response()->json($success, $this-> successStatus);
//      return response()->json(['success'=>$success], $this-> successStatus);
//      return response()->json($success, $this-> successStatus);
    }

    public function getUser(Request $request)
    {
      //$id = $request->user()->id;
      $user = $request->user();
//      $user = Auth::user();
      if($user){
          return $this->sendResponse($user);
      }
      else{
          $error = "user not found";
          return $this->sendResponse($error);
      }
    }
}
