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
  class Lettr_Delivery extends Lettr_Resource {
    public function __construct(){
      parent::__construct("api_mailings");
    }
    
    /**
     * Verschickt eine Freitext-E-Mail ohne Template
     * 
     * Attribute der E-Mail sind:
     *  - recipient: E-Mail-Adresse des Empfängers
     *  - subject: Betreff der E-Mail
     *  - text: Freitext der E-Mail (Body) als text/plain
     *  - html (optional): Freitext der E-Mail (Body) als text/html
     * 
     * @param array $attributes
     */
    public function deliver_without_template($attributes){
      Lettr_Validation::presence_of('attributes', $attributes, array("delivery[recipient]", "delivery[subject]"), array("delivery[text]", "delivery[html]", "files\[(.*)\]"));
      $identifier = md5($attributes["delivery[subject]"]);
      return $this->customId('post', $identifier, "deliver_by_identifier", $attributes);
    }
    
    /**
     * Verschickt eine Freitext-E-Mail ohne Template
     * 
     * Attribute der E-Mail sind:
     *  - recipient: E-Mail-Adresse des Empfängers
     *  - subject: Betreff der E-Mail
     *  * Weitere Attribute sind die Werte der Platzhalter im Verwendeten Template des -Mailing
     * 
     * @param integer $mailing_id ID des zu verwendenden Mailing
     * @param array $attributes Attribute der E-Mail
     */
    public function deliver_with_template($mailing_id, $attributes){
      Lettr_Validation::presence_of('attributes', $attributes, array("delivery[recipient]", "delivery[subject]"));
      return $this->customId('post', $mailing_id, "deliveries", $attributes);
    }
    
    
  }
?>