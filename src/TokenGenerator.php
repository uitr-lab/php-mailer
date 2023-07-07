<?php

namespace uitr;

class TokenGenerator{

   

    protected $options=array();

    public function __construct($options=array())
    {
        require realpath(__DIR__ . '/../vendor/autoload.php');

        $this->options=array_merge(array(

        ), $options);
    }

    protected function toLetters($str){

        if(!isset($this->options['map'])){
            return $str;
        }

        foreach($this->options['map'] as $i=>$v){
            $str=str_replace("".$i, $v, $str);
        }
        return $str;

    }

    protected function fromLetters($str){

        if(!isset($this->options['map'])){
            return $str;
        }

        foreach($this->options['map'] as $i=>$v){
            $str=str_replace($v, "".$i, $str);
        }
        return $str;

    }

    public function plain(){
        $str=md5(microtime());

        $chunks=3;
        $chunkLen=4;
        $len=$chunks*$chunkLen;

        $str=substr($str, 0, $len);

        $parts=trim(chunk_split($str, $chunkLen));
        
        $parts=explode("\r\n", $parts);
        shuffle($parts);
        array_slice($parts, 3);

        $parts[1]=substr($parts[1], 0, 3);

        $str=implode('-', $parts);
        $str=substr($str, 0, 11);
        $str=$this->toLetters($str);
      
        return $str;
    }



    public function generate(){


        $str=$this->plain();
        $str=$this->fromLetters($str);
        $parts=explode('-', $str);

        for ($i=0; $i<2 ; $i++) { 
            $parts[$i+1][$i]=dechex(floor(hexdec($parts[$i])/hexdec(''.pow(10, strlen($parts[$i])-1))));
        }

        $str=implode('-', $parts);
        $str=$this->toLetters($str);
        
        return $str;

    }

    public function check($token){

        $str=$this->fromLetters($token);

        $parts=explode('-', $str);


        for ($i=0; $i<2 ; $i++) { 
            if($parts[$i+1][$i]!==dechex(floor(hexdec($parts[$i])/hexdec(''.pow(10, strlen($parts[$i])-1))))){
                return false;
            }
        }


        return true;

    }

}