<?php

namespace App\Http\Controllers;

use App\Email;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use App\TokenStore\TokenCache;
use App\TimeZones\TimeZones;
use GuzzleHttp\Client;
ini_set('max_execution_time', 3000);
class OutlookMailBoxController extends Controller
{
    public function show()
    {
        $viewData = $this->loadViewData();
        $graph = $this->getGraph();
        if(!$graph){
            return redirect('/login');
        }else{
            $queryParams = array(
                '$select' => 'subject, sender, receivedDateTime, hasAttachments, body',
                '$top' => 35,
//            '$count' => true
            );

            // Append query parameters to the '/me/calendarView' url
            $getMsgsUrl = '/me/messages?'.http_build_query($queryParams);

            $msgs = $graph->createRequest('GET', $getMsgsUrl)
                ->addHeaders(["Prefer"=>"outlook.body-content-type=\"text\"", "Content-Encoding" => "UTF-8"])
                ->setReturnType(Model\Message::class)
                ->execute();
            $viewData['msgs'] = $msgs;

            $user= User::where('email', $viewData['userEmail'])->first();
            if($user == null){
                $user= new User();
                $user->name= $viewData['userName'];
                $user->email= $viewData['userEmail'];
                $emails= $this->get_emails_for_prediction();
                $emails_array= [];

                $client = new Client();
//                $i=0;
                foreach($emails as $email) {
                    $subject= $email->getSubject();
                    $body= "";
                    if( !is_null($email->getBody())){
                        $body= $email->getBody()->getContent();
                    }
                    $category = $client->request('POST', 'http://localhost:5000/predict', [
                        'form_params' => [
                            'subject' => $subject,
                            'body' => $body,
                        ]
                    ]);

                    $db_email= new Email();
                    $db_email->email_id= $email->getId();
                    $db_email->category= $category->getBody()->getContents();;
                    array_push($emails_array, $db_email);
//                    $i++;
//                    if($i == 8){ break; }
                }
                $user->save();
                $user->emails()->saveMany( $emails_array );
            }
            return view('mailbox', $viewData);
//     return response()->json($msgs);
        }
    }

    public function showFormation()
    {
        $viewData = $this->loadViewData();
        $graph = $this->getGraph();
        if(!$graph){
            return redirect('/login');
        }else{
            $queryParams = array(
                '$select' => 'subject, sender, receivedDateTime, hasAttachments, body',
                '$top' => 50,
//            '$count' => true
            );

            // Append query parameters to the '/me/calendarView' url
            $emails = Email::where('category', "formation")->paginate(20);
            $formation_emails=[];
            foreach ($emails as $email){
                $getMsgsUrl = '/me/messages/'.$email->email_id.'?'.http_build_query($queryParams);
                $msg = $graph->createRequest('GET', $getMsgsUrl)
                    ->setReturnType(Model\Message::class)
                    ->execute();
                array_push($formation_emails, $msg);
            }

            $viewData['msgs'] = $formation_emails;
            return view('mailbox_formation', $viewData);
//     return response()->json($msgs);
        }
    }
    public function showScolarite()
    {
        $viewData = $this->loadViewData();
        $graph = $this->getGraph();
        if(!$graph){
            return redirect('/login');
        }else{
            $queryParams = array(
                '$select' => 'subject, sender, receivedDateTime, hasAttachments, body',
                '$top' => 50,
//            '$count' => true
            );

            // Append query parameters to the '/me/calendarView' url
            $emails = Email::where('category', "scolarité")->paginate(20);
            $formation_emails=[];
            foreach ($emails as $email){
                $getMsgsUrl = '/me/messages/'.$email->email_id.'?'.http_build_query($queryParams);
                $msg = $graph->createRequest('GET', $getMsgsUrl)
                    ->setReturnType(Model\Message::class)
                    ->execute();
                array_push($formation_emails, $msg);
            }

            $viewData['msgs'] = $formation_emails;
            return view('mailbox_scolarite', $viewData);
//     return response()->json($msgs);
        }
    }
    public function showSport()
    {
        $viewData = $this->loadViewData();
        $graph = $this->getGraph();
        if(!$graph){
            return redirect('/login');
        }else{
            $queryParams = array(
                '$select' => 'subject, sender, receivedDateTime, hasAttachments, body',
                '$top' => 50,
//            '$count' => true
            );

            // Append query parameters to the '/me/calendarView' url
            $emails = Email::where('category', "sport")->paginate(20);
            $formation_emails=[];
            foreach ($emails as $email){
                $getMsgsUrl = '/me/messages/'.$email->email_id.'?'.http_build_query($queryParams);
                $msg = $graph->createRequest('GET', $getMsgsUrl)
                    ->setReturnType(Model\Message::class)
                    ->execute();
                array_push($formation_emails, $msg);
            }

            $viewData['msgs'] = $formation_emails;
            return view('mailbox_sport', $viewData);
//     return response()->json($msgs);
        }
    }
    public function showEvent()
    {
        $viewData = $this->loadViewData();
        $graph = $this->getGraph();
        if(!$graph){
            return redirect('/login');
        }else{
            $queryParams = array(
                '$select' => 'subject, sender, receivedDateTime, hasAttachments, body',
                '$top' => 50,
//            '$count' => true
            );

            // Append query parameters to the '/me/calendarView' url
            $emails = Email::where('category', "événement")->paginate(20);
            $formation_emails=[];
            foreach ($emails as $email){
                $getMsgsUrl = '/me/messages/'.$email->email_id.'?'.http_build_query($queryParams);
                $msg = $graph->createRequest('GET', $getMsgsUrl)
                    ->setReturnType(Model\Message::class)
                    ->execute();
                array_push($formation_emails, $msg);
            }

            $viewData['msgs'] = $formation_emails;
            return view('mailbox_event', $viewData);
//     return response()->json($msgs);
        }
    }
    public function showAutre()
    {
        $viewData = $this->loadViewData();
        $graph = $this->getGraph();
        if(!$graph){
            return redirect('/login');
        }else{
            $queryParams = array(
                '$select' => 'subject, sender, receivedDateTime, hasAttachments, body',
                '$top' => 35,
//            '$count' => true
            );

            // Append query parameters to the '/me/calendarView' url
            $emails = Email::where('category', "autre")->paginate(20);
            $formation_emails=[];
            foreach ($emails as $email){
                $getMsgsUrl = '/me/messages/'.$email->email_id.'?'.http_build_query($queryParams);
                $msg = $graph->createRequest('GET', $getMsgsUrl)
                    ->setReturnType(Model\Message::class)
                    ->execute();
                array_push($formation_emails, $msg);
            }

            $viewData['msgs'] = $formation_emails;
            return view('mailbox_autre', $viewData);
//     return response()->json($msgs);
        }
    }

    public function get_emails_for_prediction(): array
    {
        $graph= $this->getGraph();
        $queryParams = array(
            '$select' => 'subject, body',
        );

        // Append query parameters to the '/me/calendarView' url
        $getMsgsUrl = '/me/messages?'.http_build_query($queryParams);
        $userIterator = $graph->createCollectionRequest("GET", $getMsgsUrl)
            ->addHeaders(["Prefer"=>"outlook.body-content-type=\"text\""])
            ->setReturnType(Model\Message::class)
            ->setPageSize(50);

        $msgs= array();
        while (!$userIterator->isEnd()) {
            $msgs = array_merge($msgs,$userIterator->getPage());
        }
        return $msgs;
    }
    public function showSent()
    {
        $viewData = $this->loadViewData();
        $graph = $this->getGraph();

        if(!$graph){
            return redirect('/login');
        }else{
            // Append query parameters to the '/me/calendarView' url
            $getMsgsUrl = '/me/messages?$filter=sender/emailAddress/address eq '.$viewData['userEmail'];

            $ptr = $graph->createCollectionRequest('GET', $getMsgsUrl)
                ->setReturnType(Model\Message::class)
                ->setPageSize(200);
            $msgs= $ptr->getPage();
            $viewData['msgsSent'] = $msgs;
            return view('sent', $viewData);
//     return response()->json($msgs);
        }
    }
    public function showItem($id)
    {
//      $viewData = $this->loadViewData();
        $graph = $this->getGraph();

        if(!$graph){
            return redirect('/login');
        }else{
            $queryParams = array(
                '$select' => 'subject, sender, receivedDateTime, hasAttachments, body'
            );
            $getMsgsUrl = '/me/messages/'.$id.'?'.http_build_query($queryParams);

            $msg = $graph->createRequest('GET', $getMsgsUrl)
                ->setReturnType(Model\Message::class)
                ->execute();

//            return view('mailBody', ['msg'=>$msg->()]);
            return view('mailBody', ['msg'=>$msg->getBody()->getContent()]);
//             return response()->json($msg);
        }
    }



    private function getGraph()
    {
        // Get the access token from the cache
        $tokenCache = new TokenCache();
        $accessToken = $tokenCache->getAccessToken();
        $graph = new Graph();
        if (empty($accessToken)){
            return null;
        }else{
            // Create a Graph client
            $graph->setAccessToken($accessToken);
            return $graph;
        }
    }

    public function login()
    {
        return view('login');
    }
}









