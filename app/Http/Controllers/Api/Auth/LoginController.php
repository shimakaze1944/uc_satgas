<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use GuzzleHttp\Client as GuzzleHttpClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Client;

class LoginController extends Controller
{
    private $client;

    public function __construct()
    {
        $this->client = Client::find(2);
    }

    public function login(Request $request)
    {

        $http = new GuzzleHttpClient;

        $user = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        $check = DB::table('users')->where('email', $request->email)->first();

        if (Auth::attempt($user)) {
            $response = $http->post(' http://127.0.0.1:800/oauth/token', [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => $this->client->id,
                    'client_secret' => $this->client->secret,
                    'username' => $request->email,
                    'password' => $request->password,
                    'scope' => '*',
                ]
            ]);
            return json_decode((string)$response->getBody(), true);
        } else {
            return response([
                'message' => 'Login Failed'
            ]);
        }
    }
}
