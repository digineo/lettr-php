<?php
  /**
   * @package Lettr
   * @subpackage REST_Client
   * @access private
   * @author Digineo GmbH
   * @copyright Digineo GmbH, 2010
   * @link http://www.digineo.de
   * @link mailto:kontakt@digineo.de
   */
  class Lettr_Client {
    /**
     * Setzt die Zugangsdaten zur Lettr-API.
     *  
     * @var array assoziativ, enthält 'api-key'
     */
    private static $credentials = array();
    
    /**
     * Überprüft, ob die Zugangsdaten zur API bereits gesetzt wurden,
     * und schmeißt ggf. eine Lettr_Exception.
     */
    private static function check_credentials(){
      if(!is_array(self::$credentials)){
        throw new Lettr_Exception("Credentials sind nicht definiert.");
      }
    }
    
    /**
     * Setzt die Zugangsdaten zur API.
     * 
     * Schmeißt Lettr_IllegalArgumentException, wenn sie unvollständig gesetzt werde
     * 
     * @param array $credentials assoziativ, enthält 'api_key'
     */
    public static function set_credentials($api_key){
      if(!$api_key)
	  {
	  	throw new Lettr_IllegalArgumentException("API KEY ist leer oder nicht definiert.");
	  }
      $credentials = array();
      $credentials["site"] = "https://lettr.de/";
	  $credentials["api_key"] = $api_key;
      $credentials["content_type"] = "application/json";
      
      self::$credentials = $credentials;
    }
    
    /**
     * Holt per GET Daten einer Resource ab.
     * 
     * @param $url string Pfad der Resource
     */
    public function get($url){
      return $this->send($url,'GET');
    }
    
    /**
     * Erstellt per POST eine neue Resource.
     * 
     * @param $url string Pfad der Resource
     * @param $data array Daten
     */
    public function post($url,$data){
      return $this->send($url,'POST',$data);
    }
    
    /**
     * Aktualisiert per PUT eine vorhandene Resource.
     * 
     * @param $url string Pfad der Resource
     * @param $data array Daten
     */
    public function put($url,$data){
      return $this->send($url,'PUT',$data);
    }
    
    /**
     * Löscht per DELETE eine vorhandene Resource.
     * 
     * @param $url string Pfad der Resource
     * @param $data array (optional) Daten, die zum Löschen der Resource verwendet werden sollen.
     */
    public function delete($url, $data=null){
      return $this->send($url,'DELETE', $data);
    }
    
    /**
     * Setzt einen REST-Request ab.
     * 
     * @param $url string Pfad der Resource
     * @param $method string REST-Methode
     * @param $data array (optional) zu übergebende Daten
     */
    protected function send($url,$method,$data=null){
      self::check_credentials();
      $this->errors = null;
      
      if(is_array($data)){
        $data = json_encode($data);
      }
      
      $header[] = "Accept: " . self::$credentials["content_type"];
      $header[] = "Content-Type: " . self::$credentials["content_type"];
	  $header[] = "X-Lettr-API-key: " . self::$credentials["api_key"];
          
      $ch  = curl_init();
      
      curl_setopt($ch, CURLOPT_URL,            self::$credentials["site"] . $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_TIMEOUT,        15);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST,  $method);
      curl_setopt($ch, CURLOPT_HTTPHEADER,     $header);
      curl_setopt($ch, CURLOPT_POSTFIELDS,     $data);
      
      if(self::$credentials["username"]){
        curl_setopt($ch, CURLOPT_USERPWD, self::$credentials["username"].":".self::$credentials["password"]);
      }
      
      /*
        Wenn hier der fehler 'error setting certificate verify locations' auftritt, 
        dann fehlt das ca-certificates-Paket. Das muss nachinstalliert werden mit:
         
        apt-get install ca-certificates
      */
      $data = curl_exec($ch);
      
      
      if (curl_errno($ch)) {
        // CURL-Fehler
        throw new Lettr_CurlException(curl_error($ch),curl_errno($ch));
      } else {
        $info = curl_getinfo($ch);
        curl_close($ch);
        
        $http_code = $info['http_code'];
                 
        if($http_code==422){ // Unprocessable Entity
          #//TODO wird nur ein einzelner Fehler behandelt 
          #$this->errors = $this->xml2object($data);
          #throw new Lettr_UnprocessableEntityException($this->errors->errors);
          throw new Lettr_UnprocessableEntityException($data, 422);
        }
        if($http_code==201){ // Created
          //TODO hier könnte noch die URL ermittelt werden, zu der weitergeleitet wird
          return true;
        }
        if($http_code >= 400){
          throw new Lettr_RestException("Response-Code $http_code",$http_code);
        }
        
        if($method=='DELETE'){
          return $http_code==200;
        }
        
        return array('content_type'=>$info['content_type'], 'data'=>json_decode($data));
      }
    }
  }
?>