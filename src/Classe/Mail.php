<?php

namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;


class Mail
{
    public function send($to_email, $to_name, $subject, $template,$vars = null)
    {
       
        $mj = new Client($_ENV['MJ_APIKEY_PUBLIC'], $_ENV['MJ_APIKEY_PRIVATE'], true, ['version' => 'v3.1']);
        
        // Récupération du template
        $content = file_get_contents(dirname(__DIR__).'/Mail/'.$template);

        // Récupère les variables facultatives
        if ($vars) {
            foreach($vars as $key=>$var) {
                $content = str_replace('{'.$key.'}', $var, $content);
            }
        }

        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "rams2012@hotmail.fr",
                        'Name' => "Sonia Boutique "
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 6134124,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'content' => $content
                    ]
                ]
            ]
        ];

        $mj->post(Resources::$Email, ['body' => $body]);
    }
}

