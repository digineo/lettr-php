<?php
  /**
   * Bindet alle Notwendigen API-Klassen ein.
   */
  require_once('init.php');
  
  /**
   * Setzt die Zugangsdaten zur Lettr-API.
   */
  Lettr::set_credentials("APIKEY");
  
  /**
   * Meldet einen Newsletter-Empfänger ab.
   */
  Lettr::unsubscribe("test@example.com");
  
  /**
   * Fügt einen Newsletter-Empfänger hinzu.
   */
  Lettr::subscribe("test@example.com");
  
  /**
   * Verschickt eine E-Mail über die Lettr-API ohne Template
   * 
   * Der Empfänger der E-Mail muss nicht notwendiger Weise auch Newsletter-Empfänger sein.
   */
  Lettr::mail("test@example.com", "Test-Betreff", "Test-Text");
  
  /**
   * Verschickt eine Multipart-E-Mail über die Lettr-API ohne Template
   * 
   * Der Empfänger der E-Mail muss nicht notwendiger Weise auch Newsletter-Empfänger sein.
   */
  Lettr::multipart_mail("test@example.com", "Test-Betreff", array("text"=>"Hallo Welt", "html"=>"<h1>Hallo Welt</h1>"));
  
  /**
   * Verschickt eine E-Mail über die Lettr-API mit Template
   */
  Lettr::mail_with_template("test@example.com", "Test-Betreff", "confirmation", array("data"=>array("url"=>"http://lettr.de/")));
?>