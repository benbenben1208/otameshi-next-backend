<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Mail\UserRegistered;
use App\Models\CorporationApplicant;
use App\Models\CorporationApplicantschedule;
use App\Models\CorporationJoboffer;
use App\Models\Favorite;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Services\ImageService;
use App\Services\UserService;
use Illuminate\Auth\Events\Registered;
class UserController extends Controller
{
    public function show(User $user)
    {
        return response()->json(['email' => $user->email]);
    }
    public function login(Request $request, UserService $userService)
    {

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        if (auth()->guard('users')->attempt($credentials)) {


            $request->session()->regenerate();
            // $user = User::findOrFail(Auth::guard('users')->id());
            // $withUser = $user->with('frikuApplicant.frikuApplicantSchedules')->first();
            return response()->json([
                'user' =>  Auth::guard('users')->user(),
                'message' => "ログインに成功しました"
            ]);

        }

        return response()->json([
            'message' => 'ログイン失敗しました'
        ], 401);
    }

    public function logout(Request $request)
    {

        Auth::guard('users')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(true);
    }
    public function register(UserRequest $request, User $user)
    {

        $user = User::create([
            'name' => $request->lastName . ' ' . $request->firstName,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        event(new Registered($user));
        return response()->json([$user]);

    }
    public function getAuthUser(Request $request, UserService $userService)
    {
        $userId = Auth::guard('users')->id();
        $user = User::with('articles')->findOrFail($userId);


        return $user;
    }
    public function update(UserUpdateRequest $request, User $user, ImageService $imageService)
    {

        $filePath = $user->img_path;
        $folderName = 'users';
        if ($request->has('imageBase64')) {
            $request->validate([
                'image' => 'nullable|string',
            ]);

            $imageFile = $request->imageBase64;

            if (!is_null($imageFile)) {
                $filePath = $imageService->uploadBase64Image($imageFile, $folderName);
            }
            // $user->update(['img_path' => $filePath ]);
            if (app()->environment('local')) {
                $awsLocalPath = config('app.aws_access_bucket') . '.s3.' . config('app.aws_default_region') . '.amazonaws.com';
                $filePath =  $awsLocalPath . $filePath;
            }
        } else {
            $imageFile = $request->image;
            if (!is_null($imageFile) && $imageFile->isValid()) {
                $filePath = $imageService->uploadImage($imageFile, $folderName);
            }
            // $user->update(['img_path' => $filePath ]);
        }


        $user->fill($request->validated() +  ['img_path' => $filePath])->save();

        return $user;
    }
}
