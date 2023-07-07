<?php
namespace uitr;

class TemplateMailer{


    protected $mail;

    public function __construct()
    {
        require realpath(__DIR__ . '/../vendor/autoload.php');
        $this->mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    }

    public function create($data, $to, $config=array()){

        if(is_object($data)){
            $data=get_object_vars($data);
        }

        
        $mailConfig =json_decode(file_get_contents(realpath(__DIR__ . '/../mail.json')), true);
        $mailConfig=(object) array_merge(array(
            "subjectContentMd"=>__DIR__ . '/../registerSubject.md',
            "bodyContentMd"=>__DIR__ . '/../registerContent.md'
        ), $mailConfig, $config);
    

        
        $emailSubjectMarkdown = realpath($mailConfig->subjectContentMd);
        $emailBodyMarkdown = realpath($mailConfig->bodyContentMd);
        $subject = file_get_contents($emailSubjectMarkdown);
        $body=file_get_contents($emailBodyMarkdown);

        
        
        $loader = new \Twig\Loader\ArrayLoader([
            'default' => $body
        ]);
        $twig = new \Twig\Environment($loader);
        $body = $twig->render('default', $data);


    
        $Parsedown = new \Parsedown();
        $body = $Parsedown->text($body);

        $mail=$this->mail;
       
        try {
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = $mailConfig->host;                      //Set the SMTP server to send through
            $mail->Username   = $mailConfig->username;                  //SMTP username

            $mail->Port       = $mailConfig->port;

            $mail->setFrom($mailConfig->from, $mailConfig->fromName);
            $mail->addAddress($to);     //Add a recipient

            $mail->isHTML(true);                            //Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $body;
           
        } catch (PHPMailer\PHPMailer\Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

        return $this;
    }

    public function addAttachment($string, $name='file.png'){

        
        $encoding = "base64"; 
        $type = "image/png";
        $this->mail->addStringEmbeddedImage($string, $name, $name, $encoding, $type);

        return $this;

    }

    public function send(){

        try {
            $this->mail->send();
            $this->mail=null;
            
        } catch (PHPMailer\PHPMailer\Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }



}