<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use CFG;
use CORE;
use LTR;

final class UsersController extends Controller
{
    /**
     *
     * @var string
     */
    private static string $token;

    public function __construct()
    {
        if (empty(self::$token)) {
            self::$token = $this->token();
        }
    }

    /**
     *
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function register(Request $request)
    {
        $minlength = CORE::defaults()['passwordminlength'];
        $regex = CORE::defaults()['passwordregex'];

        if (!CFG::exists('passwordminlength')) {
            CFG::set('passwordminlength', $minlength);
        } else {
            $minlength = CFG::get('passwordminlength');
        }

        if (!CFG::exists('passwordregex')) {
            CFG::set('passwordregex', $regex);
        } else {
            $regex = CFG::get('passwordregex');
        }

        $fields = $request->validate([
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => [
                'required',
                'string',
                'confirmed',
                'min:' . $minlength,
                'regex:/' . $regex . '/'
            ]
        ]);

        $user = User::create([
            'firstname' => $fields['firstname'],
            'lastname' => $fields['lastname'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);

        $token = $user->createToken(self::$token)->plainTextToken;

        $response = ['user' => $user, 'token' => $token];

        return response($response, 201);
    }

    /**
     *
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function login(Request $request)
    {
        $fields = $request->validate([
            'language' => 'string',
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        // Check email
        $user = User::where('email', $fields['email'])->first();

        // Check password
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response(
                [
                    'msg' => LTR::get('loginfail', in_array('language', $fields) ?
                        $fields['language'] : CORE::defaults()['language'])
                ],
                401
            );
        }

        $token = $user->createToken(self::$token)->plainTextToken;

        $response = ['user' => $user, 'token' => $token];

        return response($response, 201);
    }

    /**
     *
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return  response(['msg' => 'OK'], 201);;
    }

    /**
     *
     *
     * @return string
     */
    private function token(): string
    {
        if (CFG::exists('apptoken')) {
            return CFG::get('apptoken');
        } else {
            return CORE::defaults()['apptoken'];
        }
    }
}
