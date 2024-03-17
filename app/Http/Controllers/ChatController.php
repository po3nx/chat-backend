<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Models\Chatsession;
use App\Models\Webbot;

class ChatController extends Controller
{
    private $session;
    private $sesi;
    public function __construct(Request $request){
        if (!$request->session()->has('sesi')) {
            $request->session()->put('sesi', date("Ymdhis"));
        }
        $this->sesi = $request->session()->get('sesi', date("Ymdhis"));
        $this->session=Chatsession::where('session_id',$this->sesi)->first();
        if(!$this->session){
            $this->session = Chatsession::create(["session_id"=>$this->sesi]);
        }
    }
    public function sendMessage(Request $request)
    {
        $text = $request->input('text');
        $image = $request->file('image');
        $imageBase64 = null;
        $msghist = Webbot::where('session_id',$this->session->session_id)->latest()->take(4)->get();;
        $messages = [["role"=>"system","content"=>"You are MaspungBot, a smart bot created by Purwanto. you are helping people to learn programming"]];
        $hist =$msghist->reverse()->values() ;
        foreach($hist as $msg){
            array_push($messages,["role"=>$msg->role,"content"=>$msg->message]);
        }
        $model = "gpt-4-1106-preview";
        $temperature = 0.65;
        $maxTokens = 2000;
        if ($image) {
            // Assuming the image should be sent as Base64 to the AI service
            $imagePath = $image->getRealPath();
            $imageData = file_get_contents($imagePath);
            $imageBase64 = base64_encode($imageData);
            $model = "gpt-4-vision-preview";
            array_push($messages,["role"=>"user","content"=>[["type"=>"text","text"=>$text],["type"=>"image_url","image_url"=>["url"=>"data:image/png;base64,".$imageBase64]]]]);
        }else{
            array_push($messages,["role"=>"user","content"=>$text]);
        }
        $usermsg = Webbot::create(['session_id'=>$this->session->session_id,'chat_date'=>date('Y-m-d H:i:s'),'role'=>'user','message'=>$text]);
        $payload = [
            'max_tokens' => $maxTokens,
            'model'=>$model,
            'temperature' => $temperature,
            'stream'=>true,
            'messages'=>$messages
        ];
        $url = 'https://api.openai.com/v1/chat/completions';
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . env('OPENAI_API_KEY'),
        ];
        // Set cURL options
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        //return response()->json($payload);
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $data) {
            echo $data; // Send data to client
            flush(); // Flush the output buffer to the client
            return strlen($data); // Return the number of bytes written
        });
        curl_exec($ch);

        if (curl_errno($ch)) {
            // Handle error
            echo json_encode(['error' => curl_error($ch)]);
            flush();
        }

        curl_close($ch);

        // End the response since we've already sent the data
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
        /*

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            // Handle error
            return response()->json(['error' => curl_error($ch)], 500);
        }
        curl_close($ch);
        $body = json_decode($response, true);

        // Parse the response and return the AI's message
        return response()->json([
            'message' => $body['choices'][0]['message']['content'] ?? 'Error processing response'
        ]);*/
    }
    public function saveMessage(Request $request){
        $text = $request->input('botmessage');
        $usermsg = Webbot::create(['session_id'=>$this->session->session_id,'chat_date'=>date('Y-m-d H:i:s'),'role'=>'assistant','message'=>$text]);
        return $usermsg;
    }
    public function loadMessage(Request $request){
        $msghist = Webbot::where('session_id',$this->session->session_id)->get();
        $response = array("status"=>"success","messages"=>$msghist);
        return $response;
    }
}