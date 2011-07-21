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
      // Header wird von cURL generiert
      //$credentials["content_type"] = "application/json";
      self::$credentials = $credentials;
    }
    
    /**
     * Holt per GET Daten einer Resource ab.
     * 
     * @param $url string Pfad der Resource
     */
    public function get($url){
      return $this->send($url, 'GET');
    }
    
    /**
     * Erstellt per POST eine neue Resource.
     * 
     * @param $url string Pfad der Resource
     * @param $data array Daten
     */
    public function post($url, $data){
      return $this->send($url, 'POST', $data);
    }
    
    /**
     * Aktualisiert per PUT eine vorhandene Resource.
     * 
     * @param $url string Pfad der Resource
     * @param $data array Daten
     */
    public function put($url, $data){
      return $this->send($url, 'PUT', $data);
    }
    
    /**
     * Löscht per DELETE eine vorhandene Resource.
     * 
     * @param $url string Pfad der Resource
     * @param $data array (optional) Daten, die zum Löschen der Resource verwendet werden sollen.
     */
    public function delete($url, $data = null){
      return $this->send($url, 'DELETE', $data);
    }
    
    /**
     * Setzt einen REST-Request ab.
     * 
     * @param $url string Pfad der Resource
     * @param $method string REST-Methode
     * @param $data array (optional) zu übergebende Daten
     */
    protected function send($url, $method, $data = null){
      self::check_credentials();
      $this->errors = null;

      $header[] = "Accept: " . self::$credentials["content_type"];
      $header[] = "X-Lettr-API-key: " . self::$credentials["api_key"];
      // Wir lassen den Header von cURL generieren
      // $header[] = "Content-Type: " . self::$credentials["content_type"];
          
      $ch  = curl_init();
      curl_setopt($ch, CURLOPT_URL,            self::$credentials["site"] . $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_TIMEOUT,        15);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST,  $method);
      curl_setopt($ch, CURLOPT_HTTPHEADER,     $header);
      curl_setopt($ch, CURLOPT_POSTFIELDS,     $data);
      
      // Auf Benutzernamen und Kennwort prüfen, Lettr sonst nicht benutzbar!
      if(self::$credentials["username"]){
        curl_setopt($ch, CURLOPT_USERPWD, self::$credentials["username"] . ":" . self::$credentials["password"]);
      } else {
        throw new Lettr_IllegalArgumentException('Benutzerdaten für Lettr nicht gefunden');
      }
      
      /*
        Wenn hier der fehler 'error setting certificate verify locations' auftritt, 
        dann fehlt das ca-certificates-Paket. Das muss nachinstalliert werden mit:
         
        apt-get install ca-certificates
      */
      $data = curl_exec($ch);
      
      if (curl_errno($ch)) {
        // cURL-Fehler
        throw new Lettr_CurlException(curl_error($ch),curl_errno($ch));
      } else {
        $info = curl_getinfo($ch);
        curl_close($ch);

        // Behandeln der HTTP-Statuscodes
        switch($info['http_code']) {
          case 200:   // OK - Anfrage erfolgreich
          case 201:   // Created - Anfrage erfolgreich
          case 202:   // Accepted - Anfrage erfolgreich
            return true;
            break;
          case 400:   // Bad Request - Fehler in der Übermittlung
            throw new Lettr_ClientErrorException('400 Bad Request - Daten wurden nicht erfolgreich übermittelt', 400);
            break;
          case 401:   // Unauthorized - Fehlerhafte Zugangsdaten
            throw new Lettr_ClientErrorException('401 Unauthorized - Bitte Benutzerdaten für Lettr-Service überprüfen!', 401);
            break;
          case 402:   // Payment Required - Kreditlimit überschritten
            throw new Lettr_ClientErrorException('402 Payment Required - Bitte Credits des Lettr-Service überprüfen!', 402);
            break;
          case 403:   // Forbidden - Der Zugang wurde gesperrt
            throw new Lettr_ClientErrorException('403 Forbidden - Dienst temporär nicht verfügbar oder Zugang gesperrt', 403);
            break;
          case 404:   // Not Found - URL nicht mehr aktuell
            throw new Lettr_ClientErrorException('404 Not Found - URL nicht gefunden - Lettr-API auf dem neusten Stand?', 404);
            break;
          case 407:   // Proxy Authentication Required - Falls Proxy verwendet wird (Aktuell nicht relevant)
            throw new Lettr_ClientErrorException('407 Proxy Authentication Required - Bitte Benutzerdaten für Proxy-Server prüfen', 407);
            break;
          case 408:   // Request Timeout - Daten wurden zu langsam übermittelt (erneuter Versuch?)
            throw new Lettr_ClientErrorException('408 Request Timeout - Anfrage zu einem späteren Zeitpunkt neu stellen', 408);
            break;
          case 413:   // Request Entity Too Large - Anfrage zu groß (E-Mail zu groß?)
            throw new Lettr_ClientErrorException('413 Request Entity Too Large - Versendete E-Mail größer als maximum?', 413);
            break;
          case 418:   // I'm a Teapot - Soll ja vorkommen ;-)
            throw new Lettr_ClientErrorException('418 I\'m a Teapot - Sorry, wir liefern nur an Kaffeekannen ;-)', 418);
            break;
          case 500:   // Internal Server Error - Anfrage später erneut absenden
            throw new Lettr_ServerErrorException('500 Internal Server Error - Der Lettr-Service steht gerade nicht zur Verfügung', 500);
            break;
          case 502:   // Bad Gateway - Anfrage später erneut senden 
            throw new Lettr_ServerErrorException('502 Bad Gateway - Der Lettr-Service steht gerade nicht zur Verfügung', 502);
            break;
          case 503:   // Service Unavailable - Retry-After abfragen und später erneut versuchen
            throw new Lettr_ServerErrorException('503 Service Unavailable - Der Lettr-Service steht gerade nicht zur Verfügung', 503);
            break;
        }

// 422 nicht definiert - wann soll der auftreten?
//        if($http_code==422){ // Unprocessable Entity
//          #//TODO wird nur ein einzelner Fehler behandelt 
//          #$this->errors = $this->xml2object($data);
//          #throw new Lettr_UnprocessableEntityException($this->errors->errors);
//          throw new Lettr_UnprocessableEntityException($data, 422);
//        }
// Wird normalerweise nicht gebraucht, sicherheitshalber nur auskommentiert
//        return array('content_type'=>$info['content_type'], 'data'=>json_decode($data));
      }
    }
  }
?>