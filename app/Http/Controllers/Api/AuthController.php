<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="API Token",
 *     description="استخدم توكن Bearer الصادر من تسجيل الدخول"
 * )
 */

/**
 * @OA\Info(
 *     title="My API",
 *     version="1.0.0",
 *     description="توثيق API باستخدام Swagger"
 * )
 * @OA\SecurityRequirement(name="sanctum")
 */

class AuthController extends Controller
{
/**
 * @OA\Post(
 *     path="/api/login",
 *     summary="تسجيل الدخول",
 *     description="تسجيل الدخول باستخدام البريد الإلكتروني وكلمة المرور.",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email","password"},
 *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="password123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="تم تسجيل الدخول بنجاح",
 *         @OA\JsonContent(
 *             @OA\Property(property="user", type="object",
 *                 @OA\Property(property="first_name", type="string", example="Ahmed"),
 *                 @OA\Property(property="last_name", type="string", example="Ali"),
 *                 @OA\Property(property="user_name", type="string", example="ahmed123"),
 *                 @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *                 @OA\Property(property="profile_image_url", type="string", example="http://example.com/images/profile.jpg")
 *             ),
 *             @OA\Property(property="token", type="string", example="token_here_123456789")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="البريد الإلكتروني أو كلمة المرور غير صحيحة",
 *         @OA\JsonContent(
 *             @OA\Property(property="msg", type="string", example="incorrect email or password")
 *         )
 *     )
 * )
 */

    public function login(Request $request) {
        $data = $request->validate([
            'email'=>'required|string',
            'password'=>'required|string',
        ]);
        $user = User::where('email' , $data['email'])->first();
        if (!$user || !Hash::check($data['password'],$user->password)) {
            return response(['msg'=>'incorrect email or password'],401);
        }
        $token = $user->createToken('apiToken')->plainTextToken;
        $userInfo = [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'user_name' => $user->user_name,
            'email' => $user->email,
            'profile_image_url' => $user->image_url,
        ];
        return response(['user'=> $userInfo , "token" =>$token] , 201);
    }

    
/**
 * @OA\Post(
 *     path="/api/register",
 *     summary="تسجيل مستخدم جديد",
 *     description="إنشاء حساب جديد للمستخدم مع تحميل صورة الملف الشخصي (اختياري).",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"first_name","last_name","user_name","email","password","password_confirmation"},
 *                 @OA\Property(property="first_name", type="string", example="Ahmed"),
 *                 @OA\Property(property="last_name", type="string", example="Ali"),
 *                 @OA\Property(property="user_name", type="string", example="ahmed123"),
 *                 @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *                 @OA\Property(property="password", type="string", format="password", example="password123"),
 *                 @OA\Property(property="password_confirmation", type="string", format="password", example="password123"),
 *                 @OA\Property(property="profile_image", type="file", description="صورة الملف الشخصي (اختياري)")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="تم إنشاء المستخدم بنجاح",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="message", type="string", example="User is created successfully."),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="token", type="string", example="token_here_123456789"),
 *                 @OA\Property(property="user", type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="first_name", type="string", example="Ahmed"),
 *                     @OA\Property(property="last_name", type="string", example="Ali"),
 *                     @OA\Property(property="user_name", type="string", example="ahmed123"),
 *                     @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *                     @OA\Property(property="profile_image", type="string", nullable=true, example="1653842938.jpg")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="خطأ في التحقق من البيانات"
 *     )
 * )
 */

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:250',
            'last_name' => 'required|string|max:250',
            'user_name' => 'required|string|max:250',
            'email' => 'required|string|email:rfc,dns|max:250|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'profile_image' => 'nullable|image',
        ]);

        $user = new User;
        $user->first_name = $validatedData['first_name'];
        $user->last_name = $validatedData['last_name'];
        $user->user_name = $validatedData['user_name'];
        $user->email = $validatedData['email'];
        $user->password = Hash::make($validatedData['password']);
        if ($validatedData["profile_image"]) {
            $image = $validatedData["profile_image"];
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images') , $imageName);
            $user->profile_image = $imageName;
        }
        $user->save();

        $token = $user->createToken($validatedData['email'])->plainTextToken;

        $userInfo = [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'user_name' => $user->user_name,
            'email' => $user->email,
            'profile_image_url' => $user->image_url,
        ];

        $response = [
            'status' => 'success',
            'message' => 'User is created successfully.',
            'data' => [
                'token' => $token,
                'user' => $userInfo,
            ],
        ];

        return response()->json($response, 201);
    }

/**
 * @OA\Post(
 *     path="/api/logout",
 *     summary="تسجيل خروج المستخدم",
 *     description="يقوم بحذف التوكن الحالي للمستخدم لتسجيل الخروج.",
 *     tags={"Authentication"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="تم تسجيل الخروج بنجاح",
 *         @OA\JsonContent(
 *             @OA\Property(property="msg", type="string", example="user logged out")
 *         )
 *     )
 * )
 */

    public function logout(Request $request) {
        auth()->user()->currentAccessToken()->delete();
        return response(["msg"=>"user logged out"]);
    }
}
