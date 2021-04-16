<?php

namespace App\Exports;

use App\Email;
use App\TokenStore\TokenCache;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use App\TimeZones\TimeZones;
ini_set('max_execution_time', 1800);
class EmailsExport implements FromArray, withHeadings
{

    public function array(): array
    {
        return [
            $this->getEmails()
        ];
    }

    public function headings(): array
    {
        return [
            'hasAttachments',
            'sender name',
            'sender email',
            'subject',
            'body'
        ];
    }



    private function getEmails(): array
    {
        $graph= $this->getGraph();
        $queryParams = array(
            '$select' => 'subject, sender, hasAttachments, body',
        );

        // Append query parameters to the '/me/calendarView' url
        $getMsgsUrl = '/me/messages?'.http_build_query($queryParams);


        $userIterator = $graph->createCollectionRequest("GET", $getMsgsUrl)
            ->addHeaders(["Prefer"=>"outlook.body-content-type=\"text\""])
            ->setReturnType(Model\Message::class)
            ->setPageSize(20);


        $msgs= array();
        while (!$userIterator->isEnd()) {
            $msgs = array_merge($msgs,$userIterator->getPage());
        }

        $emails_array= array();
        foreach($msgs as $msg) {
            if( !is_null($msg->getSender()) ){
                if($msg->getSender()->getEmailAddress()->getName() != 'Omar ElALAOUI'){
                    $hasAttachments= $msg->getHasAttachments();
                    $senderName= $msg->getSender()->getEmailAddress()->getName();
                    $senderAdresse= $msg->getSender()->getEmailAddress()->getAddress();
                    $subject= $msg->getSubject();
                    $body= "";
                    if( !is_null($msg->getBody())){
                        $body= $msg->getBody()->getContent();
                    }
                    $emails_array[]= array($hasAttachments, $senderName, $senderAdresse, $subject, $body);
                }
            }
        }
        return $emails_array;
    }

    private function getGraph(): Graph
    {
        // Get the access token from the cache
        $tokenCache = new TokenCache();
        $accessToken = $tokenCache->getAccessToken();

        // Create a Graph client
        $graph = new Graph();
        $graph->setAccessToken($accessToken);
        return $graph;
    }
}
