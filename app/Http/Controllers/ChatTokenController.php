<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use \Cache;

class ChatTokenController extends Controller
{
    public function index() {
        return view('chat_token');
    }

    public function oauth(Request $request) {
       
        $subdomain = Cache::remember('subdomain', 1, function() use ($request) {
            return $request->get('subdomain'); 
        });
        $client_id = Cache::remember('client_id', 1, function() use ($request) {
            return $request->get('client_id'); 
        });
        $client_secret = Cache::remember('client_secret', 1, function() use ($request) {
            return $request->get('client_secret'); 
        });
        // $redirect_uri = "http%3A%2F%2Flocalhost%3A8000%2Fredirect";
        $redirect_uri = "https%3A%2F%2Fjendeks-migrator.herokuapp.com%2Fredirect";
        $url = "https://www.zopim.com/oauth2/authorizations/new?response_type=code&redirect_uri={$redirect_uri}&client_id={$client_id}&scope=read%20write%20chat&subdomain={$subdomain}";
        
        return redirect()->away($url);
    }

    public function redirect(Request $request) {
        $code = $request->get('code');
        $client_id = Cache::get('client_id');
        $client_secret = Cache::get('client_secret');
        $redirect_uri = "https://jendeks-migrator.herokuapp.com/redirect";
        // $redirect_uri = "http://localhost:8000/redirect";
        // dd($code, $client_id, $client_secret);

        $client = new Client(); //GuzzleHttp\Client

        try {
            $result = $client->post('https://www.zopim.com/oauth2/token', [
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                    'client_id' => $client_id,
                    'client_secret' => $client_secret,
                    'redirect_uri' => $redirect_uri,
                    'scope' => 'read write chat'
                ]
            ]);
    
            $result = json_decode($result->getBody()->getContents(),true);        ;
            
            
            $token = Cache::remember('chats_access_token', 1, function() use ($result) {
                return $result['access_token'];
            });

            return view('token_result', compact('token'));
        } catch (\Exception $e) {
            dd($e);
            // $token = "ERROR";
            // return view('token_result', compact('token'));
        }

    }
}
