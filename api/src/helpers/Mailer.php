<?php

namespace Helpers;

use Aws\Ses\SesClient;
use Aws\Ses\Exception\SesException;
//use PHPMailer\PHPMailer\PHPMailer;
use \Exception;

class Mailer{
    
    var $templates = null;
    var $twig = null;
    
    function __construct(){

        if(!defined('RUNING_TEST') || !RUNING_TEST){
            $loader = new \Twig_Loader_Filesystem('src/emails');
            $this->twig = new \Twig_Environment($loader);
            require_once('src/emails/templates.php');
            $this->templates = $emailTemplates;
        }
    }
    
    function getTemplate($templateSlug, $args=[]){
        if(!isset($this->templates[$templateSlug])) throw new Exception('Invalid email template: '.$templateSlug);
        $template = $this->twig->load($this->templates[$templateSlug]['path']);
        $this->templates[$templateSlug]['body'] = $template->render($args);
        
        if(!$this->templates[$templateSlug]['body']) throw new Exception('Could not: '.$templateSlug);
        return $this->templates[$templateSlug];
    }
    
    function sendAPI($templateSlug,$args=[]){
        
        if(defined('RUNING_TEST') && RUNING_TEST) return true;
        
        if(!isset($args['email'])) throw new Exception('You have to specify the recipient email');
        
        $template = $this->getTemplate($templateSlug, $args);
        
        $client = SesClient::factory(array(
            'version'=> 'latest',     
            'region' => 'us-west-2',
            'credentials' => [
                'key'    => S3_KEY,
                'secret' => S3_SECRETE,
            ]
        ));
        
        try {
             $result = $client->sendEmail([
            'Destination' => [
                'ToAddresses' => [
                    $args['email'],
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