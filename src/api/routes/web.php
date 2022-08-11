<?php
use App\Models\User;
use App\Models\TodoNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
// use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Cache;


/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function (Request $request) use ($router) {
    Redis::set('name', 'Taylor');
    $name = Redis::get('name');
    return $name;
    //$client = new Predis\Client();
    // Redis::get('hello');
    // Cache::store('redis')->put('site_name', 'Lumen测试', 10);
    // Cache::put('key', 'value123', $seconds = 100);
    // $value = Cache::get('key');
    // return $value;
    // return $router->app->version();
});

$attributes = [
    // do not change the prefix our the endpoints
    'prefix' => 'user',
    // Can change the namespace
    'namespace' => 'User',
];
$router->group(
    $attributes,
    function () use ($router, $attributes) {

    $router->post('/signup', function (Request $request) use ($router) {
        $password_hash = Hash::make($request->password);
        $new_user_data = array('username'=>$request->username,'password_hash'=>$password_hash);
        $new_user = User::create($new_user_data);
        if($new_user){
            $api_token = Str::random(60);
            // $api_token = hash('sha256', $api_token);
            Redis::set('user:api_token:'.$new_user->id, $api_token);
            $new_user->api_token = $api_token;
            $new_user->save();
            return response(['api_token'=>$api_token],200);
        }
        // return $router->app->version();
        // create a new user and login the user
        // inputs: [username, password, confirm_password]
        // returns: api_token
    });

    $router->post('/login', function (Request $request) use ($router) {
        $credentials = $request->only(['username','password']);
        $user = User::where('username',$request->username)->first();
        if($user && Hash::check($credentials['password'], $user->password_hash)){
            $api_token_redis = Redis::get('user:api_token:'.$user->id);
            if($api_token_redis){
                return response(['status'=>'success','api_token'=>$api_token_redis,'user_name'=>$user->username],200);
            }
            else{
                $new_api_token = Str::random(60);
                $user->api_token = $new_api_token;
                $user->save();
                Redis::set('user:api_token:'.$user->id, $new_api_token);
                return response(['status'=>'success','api_token'=>$new_api_token,'user_name'=>$user->username],200);
            }
        }
        else{
            return response(['status'=>'fail','message'=>'credentials incorrect'],400);
        }
        // if (Auth::attempt($credentials)) {
        //     return 'login';

        // }

        // return an API token that is stored in the db/redis
        // inputs: [username, password]
        // returns: api_token
        // return $router->app->version();
    });

    $router->post('/logout', function (Request $request) use ($router) {
        $api_token = $request->api_token;
        $user = User::where('api_token', $api_token)->first();
        if($user){
            Redis::del('user:api_token:'.$user->id);
            return response(['status'=>'success','message'=>'log out success'],200);
        }
        else{
            return response(['status'=>'fail','message'=>'non-valid api tokens'],401);
        }
        // invalidate the token api key in the db/redis.
        // Return if successful, fail for non-valid api tokens
        // inputs: [api_token]
        // return: [success]
        // return $router->app->version();
    });

    $router->post('/me', function (Request $request) use ($router) {
        $api_token = $request->api_token;
        $user = User::where('api_token', $api_token)->first();
        if($user){
            $user_name = $user->username;
            return response(['username'=>$user_name],200);
        }
        // Return the username for a given api token
        // inputs: [api_token]
        // return: [username]
        // return $router->app->version();
    });

});

$attributes = [
    // do not change the prefix our the endpoints
    'prefix' => 'todonotes',
    // Can change the namespace
    'namespace' => 'TodoNotes',
];
$router->group(
    $attributes,
    function () use ($router, $attributes) {

    $router->get('/', function (Request $request) use ($router) {
        $api_token = $request->api_token;
        $user = User::where('api_token', $api_token)->first();
        $notes = TodoNote::where('user_id',$user->id)->orderByDesc('created_at')->paginate(2);


        return $notes;
        // return paginated results of sorted (created_at) todonotes for this user
        // inputs: [api_token, ?page]
        // returns: {notes: [{todonote}, ...], next_page_exists: boolean, prev_page_exists: boolean}
        //     todonote = {id, note_string, created_at, completed_at}
        //     next_page_exists = true only if there exists todonotes in the next page number
        //     prev_page_exists = true only if there exists todonotes in the prev page number
        // return $router->app->version();
    });

    $router->post('/create', function (Request $request) use ($router) {
        $api_token = $request->api_token;
        $user = User::where('api_token', $api_token)->first();
        if($user){
            $new_note_data = array('user_id'=>$user->id,'note'=>$request->note,'status'=>1);
            $new_note = TodoNote::create($new_note_data);
            return $new_note;
        }
        else{
            return response(['status'=>'fail','message'=>'non-valid api tokens'],401);
        }
        // return an API token that is stored in the db/redis
        // inputs: [api_token, todo_note_string]
        // returns: api_token
        // return $router->app->version();
    });

    $router->post('/mark/done/{todo_id}', function (Request $request,$todo_id) use ($router) {

        $api_token = $request->api_token;
        $user = User::where('api_token', $api_token)->first();
        $todo_note = TodoNote::where('id', $todo_id)->first();
        if($todo_note && $user && $todo_note->user_id == $user->id){
            $todo_note->status = 3;
            $todo_note->completed_at = date('Y-m-d H:i:s');
            $todo_note->save();
            return response(['status'=>'success','message'=>'change to done success'],200);
        }


        // Marks a todo note as done and store the time it was finished
        // inputs: [api_token]
        // return: [success]

    });

    $router->post('/mark/pending/{todo_id}', function (Request $request,$todo_id) use ($router) {
        $api_token = $request->api_token;
        $user = User::where('api_token', $api_token)->first();
        $todo_note = TodoNote::where('id', $todo_id)->first();
        if($todo_note && $user && $todo_note->user_id == $user->id){
            $todo_note->status = 2;
            $todo_note->save();
            return response(['status'=>'success','message'=>'change to pending success'],200);

        }
        // Marks a todo note as pending
        // inputs: [api_token]
        // return: [success]
        return $router->app->version();
    });

});
