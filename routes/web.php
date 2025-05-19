 <?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\ProductsController;
use App\Http\Controllers\Web\UsersController;
use App\Http\Controllers\Web\StudentsController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

Route::get('register', [UsersController::class, 'register'])->name('register');
Route::post('register', [UsersController::class, 'doRegister'])->name('do_register');
Route::get('login', [UsersController::class, 'login'])->name('login');
Route::post('login', [UsersController::class, 'doLogin'])->name('do_login');
Route::get('logout', [UsersController::class, 'doLogout'])->name('do_logout');
Route::get('users', [UsersController::class, 'list'])->name('users');
Route::get('profile/{user?}', [UsersController::class, 'profile'])->name('profile');
Route::get('users/edit/{user?}', [UsersController::class, 'edit'])->name('users_edit');
Route::post('users/save/{user}', [UsersController::class, 'save'])->name('users_save');
Route::get('users/delete/{user}', [UsersController::class, 'delete'])->name('users_delete');
Route::post('/profile/add-credit', [UsersController::class, 'addCredit'])->name('profile.add_credit');
Route::get('users/edit_password/{user?}', [UsersController::class, 'editPassword'])->name('edit_password');
Route::post('users/save_password/{user}', [UsersController::class, 'savePassword'])->name('save_password');
Route::get('students', [ProductController::class, 'reset'])->name('students_list');
Route::post('/reset-credit', [UsersController::class, 'reset'])->name('credit.reset');
Route::get('/products/{id}/reviews', [ProductController::class, 'showReviews'])->name('products.showReviews');
Route::get('/auth/google', [UsersController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [UsersController::class, 'handleGoogleCallback']);
Route::get('products', [ProductsController::class, 'list'])->name('products_list');
Route::post('/buy-product/{id}', [ProductsController::class, 'buyProduct'])->name('buy_product');
Route::get('products/edit/{product?}', [ProductsController::class, 'edit'])->name('products_edit');
Route::post('products/save/{product?}', [ProductsController::class, 'save'])->name('products_save');
Route::get('products/delete/{product}', [ProductsController::class, 'delete'])->name('products_delete');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/multable', function (Request $request) {
    $j = $request->number??5;
    $msg = $request->msg;
    return view('multable', compact("j", "msg"));
});

Route::get('/even', function () {
    return view('even');
});

Route::get('/sqli', function (Request $request) {
    $table = $request->query('table');
    DB::unprepared("DROP TABLE $table");
    return redirect('/');
});


Route::get('/collect', function (Request $request) {
    $name = $request->query('name');
    $credit = $request->query('credit');

    return response('data collected', 200)
        ->header('Access-Control-Allow-Origin', '*') 
        ->header('Access-Control-Allow-Methods', 'GET,POST,OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type,X-Requested-With');
});

Route::get('/prime', function () {
    return view('prime');
});

Route::get('/test', function () {
    return view('test');
});

Route::get('/cryptography', function (Request $request) {
    $data = $request->data??"Welcome to Cryptography";
    $action = $request->action??"Encrypt";
    $result = $request->result??"";
    $status = "Failed";
    if($request->action=="Encrypt") {
        $temp = openssl_encrypt($request->data, 'aes-128-ecb', 'thisisasecretkey', OPENSSL_RAW_DATA, '');
        if($temp) {
            $status = 'Encrypted Successfully';
            $result = base64_encode($temp);
        }
    }
    else if($request->action=="Decrypt") {
        $temp = base64_decode($request->data);
        $result = openssl_decrypt($temp, 'aes-128-ecb', 'thisisasecretkey', OPENSSL_RAW_DATA, '');
        if($result) $status = 'Decrypted Successfully';
    }
    else if($request->action=="Hash") {
        $temp = hash('sha256', $request->data);
        $result = base64_encode($temp);
        $status = 'Hashed Successfully';
    }
     else if($request->action=="Sign") {
         $path = storage_path('app/private/useremail@domain.com.pfx');
         $password = '12345678';
         $certificates = [];
         $pfx = file_get_contents($path);
         openssl_pkcs12_read($pfx, $certificates, $password);
         $privateKey = $certificates['pkey'];
         $signature = '';
         if(openssl_sign($request->data, $signature, $privateKey, 'sha256')) {
             $result = base64_encode($signature);
             $status = 'Signed Successfully';
         }
     }
     else if($request->action=="Verify") {
         $signature = base64_decode($request->result);
         $path = storage_path('app/public/useremail@domain.com.crt');
         $publicKey = file_get_contents($path);
         if(openssl_verify($request->data, $signature, $publicKey, 'sha256')) {
             $status = 'Verified Successfully';
         }
     }
     else if($request->action=="KeySend") {
         $path = storage_path('app/public/useremail@domain.com.crt');
         $publicKey = file_get_contents($path);
         $temp = '';
         if(openssl_public_encrypt($request->data, $temp, $publicKey)) {
             $result = base64_encode($temp);
             $status = 'Key is Encrypted Successfully';
         }
     }
     else if($request->action=="KeyRecive") {
         $path = storage_path('app/private/useremail@domain.com.pfx');
         $password = '12345678';
         $certificates = [];
         $pfx = file_get_contents($path);
         openssl_pkcs12_read($pfx, $certificates, $password);
         $privateKey = $certificates['pkey'];
         $encryptedKey = base64_decode($request->data);
         $result = '';
         if(openssl_private_decrypt($encryptedKey, $result, $privateKey)) {
             $status = 'Key is Decrypted Successfully';
        }
     }

    return view('cryptography', compact('data', 'result', 'action', 'status'));
    })->name('cryptography');


//    Route::get('/', function () {
        // Correct function name and variable
  //      $email = emailFromLoginCertificate();
        
      //  if ($email && !auth()->check()) {
    //        $user = User::firstOrCreate(
        //        ['email' => $email],
          //      [
            //        'name' => explode('@', $email)[0],
              //      'password' => Hash::make(Str::random(32))
      //          ]
 //           );
   //         Auth::login($user);
     //   }
        
//        return view('welcome');
//    })->name('home');