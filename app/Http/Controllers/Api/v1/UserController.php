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
    public function register(Request $request) {
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
      $success['data']['access_token'] =  $user->createToken('blackboard')->accessToken;
      $success['data']['token_type'] = "user";
      $success['data']['created_at'] = Carbon::createFromFormat('Y-m-d H:i:s', $user->created_at)->setTimezone('+7:00')->format('Y-m-d H:i:s');
//      return response()->json(['success'=>$success], $this-> successStatus);
      return $this->sendResponse($success);
    }

    public function user(Request $request) {

      $user = Auth::user();
      $room = DB::table('room_infos')->where('user_id','=',$user->id)->first();
      $follow=DB::table('room_follows')->where('user_id','=',$user->id)->count();

      $follower=0;
      $favorite=0;
      if ( $room ) {
        $follower=DB::table('room_follows')->where('room_id','=',$room->id)->count();
        $favorite=DB::table('room_favorites')->where('room_id','=',$room->id)->count();
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
    }

    public function update(Request $request) {
      $aReturn=array("status"=>false,"message"=>null);
      $aInput = $request->all();
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
                    "image_url"=>($aUser->provider=='email')?
                      $aUser->photo_url:$aUser->provider_photo,
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

    public function facebook(Request $request) {
      $input = $request->all();
      $aReturn=array("status"=>false,"message"=>"","code"=>0,"data"=>null);
      $user=User::where('provider','=','facebook')
        ->where('provider_id','=',$input['facebook'])
        ->first();

      if (! $user) {
        if (! ( isset($input['first_name'])
          && isset($input['last_name'])
          && isset($input['tel'])
          && isset($input['name'])
          )) {
            $aReturn['message']='Paramter not completed';
          return $this->sendResponse($aReturn);
        }
        $data['name']=$input['name'];
        $data['password']=bcrypt($input['name']);
        $data['first_name']=$input['first_name'];
        $data['last_name']=$input['last_name'];
        $data['provider'] = 'facebook';
        $data['provider_id'] = $input['facebook'];
        $data['email']  = sprintf("%s@facebook.com",$input['facebook']);
        $data['provider_photo']=sprintf("https://graph.facebook.com/v3.0/%s/picture?type=normal",$input['facebook']);
        $data['phone'] = $input['tel'];
        $user = User::create($data);
        if (! $user) {
          $aReturn['message']="Cannot Register User";
          return $this->sendResponse($aReturn);
        }
      }
      $aReturn['status']=true;
      $aReturn['data']['access_token'] =  $user->createToken('blackboard')->accessToken;
      $aReturn['data']['token_type'] = "facebook";
      $aReturn['data']['created_at'] = Carbon::createFromFormat('Y-m-d H:i:s', $user->created_at)->setTimezone('+7:00')->format('Y-m-d H:i:s');
//      return response()->json(['success'=>$success], $this-> successStatus);
      return $this->sendResponse($aReturn);
    }

}
