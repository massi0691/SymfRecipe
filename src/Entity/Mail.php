<?php

namespace App\Entity;

use Mailjet\Client;
use Mailjet\Resources;

class Mail
{

    private $api_key = '60e6c4d0acb5b201c773a93f4a3b0f77';
    private $api_key_secret = '47010e4802075454c83d4f2a61543d56';

    public function send($to_email, $to_name, $subject, $content)

    {
        $mj = new Client($this->api_key, $this->api_key_secret, true, ['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "massi9106.ait@gmail.com",
                        'Name' => "Symrecipe"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 4022493,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'content' => $content

                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success();
    }
}
