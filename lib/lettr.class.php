<?php
  /**
   * @package Lettr
   * @subpackage API
   * @access public
   * @author Digineo GmbH
   * @copyright Digineo GmbH, 2010
   * @link http://www.digineo.de
   * @link mailto:kontakt@digineo.de
   */
  class Lettr {
    /**
     * Setzt die Zugangsdaten zur Lettr-API.
     *  
     * @param $credentials array assoziativ, enthält 'username' und 'password'
     */
    public static function set_credentials($credentials){
      return Lettr_Client::set_credentials($credentials);
    }
    
    /**
     * Fügt einen Newsletter-Empfänger hinzu.
     * 
     * Über den zweiten Parameter können bei Bedarf zusätzliche Informationen zum Empfänger angegeben werden:
     *  - name: Benutzername (Alternative zu firstname+lastname)
     *  - firstname: Vorname
     *  - lastname: Nachname
     *  - gender: Geschlecht (m/f)
     *  - birthdate: Geburtsdatum (YYYY-MM-DD)
     *  - street: Straße + Hausnummer
     *  - city: Stadt
     *  - ccode: Land nach <a href="http://www.iso.org/iso/english_country_names_and_code_elements">ISO 3166-1</a>
     * 
     * @param $email string E-Mail-Adresse des Empfängers
     * @param $additional_info array (optional) assoziativ, weitergehende Informationen
     */
    public static function subscribe($email, $additional_info = array()){
      $recipient = new Lettr_Recipient();
      return $recipient->create(array_merge($additional_info, array("email" => $email)));
    }
    
    /**
     * Meldet einen Newsletter-Empfänger ab.
     * 
     * @param $email string E-Mail-Adresse des Empfängers
     */
    public static function unsubscribe($email){
      $recipient = new Lettr_Recipient();	  
      return $recipient->delete_by_email($email);
    }
    
    /**
     * Verschickt eine E-Mail über die Lettr-API ohne Template
     * 
     * Der Empfänger der E-Mail muss nicht notwendiger Weise auch Newsletter-Empfänger sein.
     * 
     * @param $to string E-Mail-Adresse des Empfängers
     * @param $subject string Betreff der E-Mail
     * @param $message string Text der E-Mail
     */
    public static function mail($to, $subject, $message){
      $delivery = new Lettr_Delivery();
      return $delivery->deliver_without_template(array("delivery[recipient]" => $to, "delivery[subject]" => $subject, "delivery[text]" => $message));
    }
    
    /**
     * Verschickt eine Multipart-E-Mail über die Lettr-API ohne Template
     * 
     * Der Empfänger der E-Mail muss nicht notwendiger Weise auch Newsletter-Empfänger sein.
     * 
     * @param $to string E-Mail-Adresse des Empfängers
     * @param $subject string Betreff der E-Mail
     * @param $message string Text der E-Mail
     */
    public static function multipart_mail($to, $subject, $multiparts=array()){
      if (empty($multiparts["delivery[text]"]) && empty($multiparts["delivery[html]"])) {
        throw new Lettr_IllegalArgumentException("Als multipart muss mindestens 'text' oder 'html' angegeben werden.");
      }
      $delivery = new Lettr_Delivery();
      return $delivery->deliver_without_template(array_merge($multiparts, array("delivery[recipient]" => $to, "delivery[subject]" => $subject)));
    }
    
    /**
     * Verschickt eine E-Mail über diie Lettr-API mit Template
     * 
     * @param $to string E-Mail-Adresse des Empfängers
     * @param $subject string Betreff der E-Mail
     * @param $mailing_identifier string Selbstgesetzter Identifier des zu verwendenden Templates
     * @param $placeholders array assozativ, verwendete Platzhalter im zu verwendenden Template
     */
    public static function mail_with_template($to, $subject, $mailing_identifier, $placeholders = array()){
      $delivery = new Lettr_Delivery();
      return $delivery->deliver_with_template($mailing_identifier, array_merge($placeholders, array("delivery[recipient]" => $to, "delivery[subject]" => $subject)));
    }
  }
?>