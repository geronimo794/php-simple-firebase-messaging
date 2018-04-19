<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * CodeIgniter Instagram Class
 *
 *
 * @package        	CodeIgniter
 * @subpackage    	Libraries
 * @category    	Libraries
 * @author        	Achmad Rozikin
 */
Class Firebase_messaging{
    public $key = '';
    private $request_url = 'https://fcm.googleapis.com/fcm/send';
    private $destination = null;
    private $count_destionation = 0;
    /**
     * Property for message content
     * 
     */
    private $title = 'php-simple-firebase-messaging';
    private $text = 'php-simple-firebase-messaging';
    private $sound = 'default';
    private $priority = 'high';
    private $data = [];
    private $request_body = null;
    private $ci;
    private $debug_mode = true;
    public function __construct(){
		$this->ci =& get_instance();
		$this->ci->load->library('curl');
    }

    public function addDestination($inp_destination){
        if(is_array($inp_destination)){
            foreach($inp_destion as $each_destination){
                $this->destination[] = $each_destination;
                $this->count_destionation++;
            }
        }else{
            $this->destination[] = $inp_destination;
            $this->count_destionation++;
        }
    }

    public function setData($name, $value = 0){
		if(is_array($name)){
			foreach($name as $dataName => $dataValue):
				$this->data[$dataName] = $dataValue;
			endforeach;
		}else{
			$this->data[$name] = $value;
		}
    }
    public function getData(){
        return $this->data;
    }

    public function setText($inp_text){
        $this->text = $inp_text;
    }
    public function getText(){
        return $this->text;
    }
    

    public function setTitle($inp_tite){
        $this->title = $inp_tite;
    }
    public function getTitle(){
        return $this->title;
    }

    public function setSound($inp_sound){
        $this->sound = $inp_sound;
    }
    public function getSound(){
        return $this->sound;
    }
    
    public function sendMessage(){
        $this->ci->curl->setUrl($this->request_url);
        $this->ci->curl->setHeaderData('Authorization', 'key='.$this->key);
        $this->ci->curl->setHeaderData('Content-Type', 'application/json');
        $this->ci->curl->setRequestMethod('POST');

        $message_respon = [];
        $i = 0;
        foreach($this->destination as $destination){
            $this->ci->curl->setBody(json_encode($this->buildRequestBody($destination)));
            $respon = $this->ci->curl->getResponse();
            if($this->debug_mode){
                @$respon_data = json_decode($respon);
                if(json_last_error() != JSON_ERROR_NONE){
                    $respon_data['firebase_messaging_error'] = $respon;
                }  
                $message_respon[$i]['destination'] = $destination;
                $message_respon[$i]['response'] = $respon_data;

                $i++;  
            }
        }
        return $message_respon;
    }
    private function buildRequestBody($destination){
        $request_body = [];
        $request_body['to'] = $destination;
        $request_body['notification']['title'] = $this->getTitle();
        $request_body['notification']['text'] = $this->getText();
        $request_body['notification']['sound'] = $this->getSound();
        if(!empty($this->getData())){
            $request_body['data'] = $this->getData();
        }

        return $request_body;

    }

}