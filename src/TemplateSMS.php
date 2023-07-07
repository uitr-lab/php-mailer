<?php

namespace uitr;

class TemplateSMS{


    protected $to=null;
    protected $message=null;
    protected $twilio=null;

    public function __construct()
    {
        require realpath(__DIR__ . '/../vendor/autoload.php');
    }

    public function create($data, $to, $config=array()){


        if(is_object($data)){
            $data=get_object_vars($data);
        }

        $messageConfig =json_decode(file_get_contents(realpath(__DIR__ . '/../sms.json')), true);
        $messageConfig=(object) array_merge(array(
            "bodyContentMd"=>__DIR__ . '/../registerSMSContent.md'
        ), $messageConfig, $config);


        $bodyMarkdown = realpath($messageConfig->bodyContentMd);
        $body=file_get_contents($bodyMarkdown);

        
        
        $loader = new \Twig\Loader\ArrayLoader([
            'default' => $body
        ]);
        $twig = new \Twig\Environment($loader);
        $body = $twig->render('default', $data);

        $this->to=$to;
        $this->message=array(
            "body"=>$body,
            "from"=>$messageConfig->from
        );



        $this->twilio = new  \Twilio\Rest\Client($messageConfig->sid, $messageConfig->token);

        error_log(json_encode($messageConfig));

        return $this;
    }



    public function send(){

        error_log(json_encode($this->message));
        
        $message = $this->twilio->messages->create(
                $this->to, // to
                $this->message
            );
        
        error_log($message->sid);

         $this->to=null;
         $this->message=null;
         $this->twilio=null;               

    }



}