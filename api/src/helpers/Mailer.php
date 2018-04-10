<?php

namespace Helpers;

use Aws\Ses\SesClient;
use Aws\Ses\Exception\SesException;
//use PHPMailer\PHPMailer\PHPMailer;
use \Exception;

class Mailer{
    
    var $templates = null;
    
    function __construct(){
        $this->templates = [
            "password_reminder" => [
                    "subject" => "You password reminder",
                    "body" => "<p>You password reminder</p>",
                    "alt" => "Your alternative text"
                ]
        ];
    }
    
    function getTemplate($templateSlug){
        if(!isset($this->templates[$templateSlug])) throw new Exception('Invalid email template: '.$templateSlug);
        
        return $this->templates[$templateSlug];
    }
    
    function sendAPI($templateSlug,$to){
        
        $template = $this->getTemplate($templateSlug);
        
        $client = SesClient::factory(array(
            'version'=> 'latest',     
            'region' => 'us-west-2'
        ));
        
        try {
             $result = $client->sendEmail([
            'Destination' => [
                'ToAddresses' => [
                    $to['email'],
                ],
            ],
            'Message' => [
                'Body' => [
                    'Html' => [
                        'Charset' => 'UTF-8',
                        'Data' => $template['body'],
                    ],
        			'Text' => [
                        'Charset' => 'UTF-8',
                        'Data' => $template['alt'],
                    ],
                ],
                'Subject' => [
                    'Charset' => 'UTF-8',
                    'Data' => $template['subject'],
                ],
            ],
            'Source' => 'info@breatheco.de',
            //'ConfigurationSetName' => 'ConfigSet',
        ]);
             $messageId = $result->get('MessageId');
             return true;
        
        } catch (SesException $error) {
            throw new Exception("The email was not sent. Error message: ".$error->getAwsErrorMessage()."\n");
        }
    }

}