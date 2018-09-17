<?php 
use Illuminate\Http\Request;
use Zendesk\API\HttpClient as ZendeskAPI;


function downloadChats($token) {
    $client = new GuzzleHttp\Client();

    //'visitor.email', 'visitor.name', “agent_names', “session.start_date', “session.end_date', “duration', “missed”, “unread”, “rating”, “response_time.first”, “response_time.avg”, “response_time.max”, “started_by”, “duration
      $chat_results = [];
      $next_url = "https://www.zopim.com/api/v2/chats";
      $count = 0;
      do {
        $response = $client->request('GET', $next_url, ['headers' =>
            [
              'Authorization' => "Bearer $token",
              'Content-type' => 'application/json'        ]
        ])->getBody()->getContents();
        $result = json_decode($response);
        $chats = $result->chats;
        $next_url = isset($result->next_url) ? $result->next_url : null;
    
        foreach($chats as $chat) {
          $detail = [];
          $detail['visitor.email'] = $chat->visitor->email;
          $detail['visitor.name'] = $chat->visitor->name;
          $detail['agent_names'] = isset($chat->agent_names[0]) ? implode(", ", $chat->agent_names) : null;
          $detail['session.start_date'] = $chat->session->start_date;
          $detail['session.end_date'] = $chat->session->end_date;
          $detail['duration'] = isset($chat->duration) ? $chat->duration : 0;
          $detail['missed'] = isset($chat->missed) ? $chat->missed : null;
          $detail['unread'] = isset($chat->unread) ? $chat->unread : null;
          $detail['rating'] = isset($chat->rating) ? $chat->rating : null;
          $detail['response_time.first'] = isset($chat->response_time->first) ?$chat->response_time->first : 0;
          $detail['response_itme.avg'] = isset($chat->response_time->avg) ? $chat->response_time->avg : 0;
          $detail['response_itme.max'] = isset($chat->response_time->max)  ? $chat->response_time->max : 0;
          $detail['started_by'] = isset($chat->started_by) ? $chat->started_by : null;
    
          $chat_results[] = $detail;
        }
    
      } while ($next_url != null && $count != 250);
    
    
      return \Excel::create('watsons_all_chats_dump', function($excel) use ($chat_results){
        $excel->sheet('watsons_all_chats', function($sheet)  use ($chat_results) {
          $sheet->row(1, ['visitor.email', 'visitor.name', 'agent_names', 'session.start_date', 'session.end_date', 'duration', 'missed', 'unread', 'rating', 'response_time.first', 'response_time.avg', 'response_time.max', 'started_by']);
    
          //populate each row
          $current_row = 2;
          foreach($chat_results as $chat) {
            $row_data = [];
            foreach($chat as $key => $value) {
              $row_data[] = $value;
            }
            $sheet->row($current_row, $row_data);
            $current_row++;
          }
        });
    
    
      })->download();
}

function downloadAgentTimeline($token)
{
    $client = new GuzzleHttp\Client();

    //'visitor.email', 'visitor.name', “agent_names', “session.start_date', “session.end_date', “duration', “missed”, “unread”, “rating”, “response_time.first”, “response_time.avg”, “response_time.max”, “started_by”, “duration
      $chat_results = [];
      $next_page = "https://www.zopim.com/api/v2/incremental/agent_timeline";
      $count = 0;
      do {
        // $response = $client->request('GET', $next_page, ['headers' =>
        // [
        //     'Authorization' => "Bearer t0oU3rpaMOHPfTOwz57kCXlGyrJv5gCwJneE0UOAOu8apyRvizpbjxvaGHhZwjEh",
        //     'Content-type' => 'application/json'
        // ]
        $response = $client->request('GET', $next_page, ['headers' =>
            [
                'Authorization' => "Bearer $token",
                'Content-type' => 'application/json'
            ]
        ])->getBody()->getContents();
        $result = json_decode($response);
        $chats = $result->agent_timeline;
        $next_page = isset($result->next_page) ? $result->next_page : null;
    
        foreach($chats as $chat) {
          $detail = [];
          $detail['agent_id'] = $chat->agent_id;
          $detail['start_time'] = $chat->start_time;
          $detail['duration'] = $chat->duration;
          $detail['status'] = $chat->status;
          $detail['engagement_count'] = $chat->engagement_count;
    
          $chat_results[] = $detail;
    
        }
        $count++;
      } while ($next_page != null && $count != 250);
    
      return \Excel::create('watsons_timeline_dump', function($excel) use ($chat_results){
        $excel->sheet('watsons_timeline_dump', function($sheet)  use ($chat_results) {
          $sheet->row(1, ['Agent ID', 'Login Time', 'Duration', 'Status', 'Engagement Count']);
    
          //populate each row
          $current_row = 2;
          foreach($chat_results as $chat) {
            $row_data = [];
            foreach($chat as $key => $value) {
              $row_data[] = $value;
            }
            $sheet->row($current_row, $row_data);
            $current_row++;
          }
        });
    
    
      })->download('xlsx');    
}

function downloadAgents($token) {
    $client = new GuzzleHttp\Client();

    //'visitor.email', 'visitor.name', “agent_names', “session.start_date', “session.end_date', “duration', “missed”, “unread”, “rating”, “response_time.first”, “response_time.avg”, “response_time.max”, “started_by”, “duration
      $chat_results = [];
      $next_url = "https://www.zopim.com/api/v2/agents";
      $count = 0;
      do {
        $response = $client->request('GET', $next_url, ['headers' =>
            [
              'Authorization' => "Bearer $token",
              'Content-type' => 'application/json'
            ]
        ])->getBody()->getContents();
        $result = json_decode($response);
    
        $chats = $result;
        $next_url = isset($result->next_url) ? $result->next_url : null;
    
        foreach($chats as $chat) {
          $detail = [];
          $detail['id'] = $chat->id;
          $detail['name'] = $chat->display_name;
          $detail['email'] = $chat->email;
          $chat_results[] = $detail;
        }
    
      } while ($next_url != null && $count != 110);
    
      return \Excel::create('watsons_agents_dump', function($excel) use ($chat_results){
        $excel->sheet('watsons_agents_dump', function($sheet)  use ($chat_results) {
          $sheet->row(1, ['ID', 'Name', 'Email']);
    
          //populate each row
          $current_row = 2;
          foreach($chat_results as $chat) {
            $row_data = [];
            foreach($chat as $key => $value) {
              $row_data[] = $value;
            }
            $sheet->row($current_row, $row_data);
            $current_row++;
          }
        });
    
    
      })->download();    
}