<?php
  /**
   * Bindet alle Notwendigen API-Klassen ein.
   */
  require_once('init.php');
  
  /**
   * Setzt die Zugangsdaten zur Lettr-API.
   */
  Lettr::set_credentials("API-KEY 123");
  
  /**
   * Setzt die Zugangsdaten zur Lettr-API.
   */
  Lettr::set_credentials(array("username"=>"foobar@example.com", "password"=>"bar"));
  
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
   * Verschickt eine E-Mail über die Lettr-API ohne Template. Setze dabei die
   * Adresse des anfragenden Kunden ins Reply-To.
   * 
   * Der Empfänger der E-Mail muss nicht notwendiger Weise auch Newsletter-Empfänger sein.
   */
  Lettr::mail("info@example.org", "Test-Betreff", "Test-Text", array('reply_to'=>'customer1@example.com'));
  
  /**
   * Verschickt eine E-Mail über die Lettr-API ohne Template. Setze dabei die
   * Absender-Adresse auf eine bestätigte Absender-Adresse, die von der Standard-
   * Einstellung abweicht.
   * 
   * Der Empfänger der E-Mail muss nicht notwendiger Weise auch Newsletter-Empfänger sein.
   */
  Lettr::mail("info@example.org", "Test-Betreff", "Test-Text", array('sender_address'=>"fummbar@example.net"));
  
  /**
   * Verschickt eine Multipart-E-Mail über die Lettr-API ohne Template
   * 
   * Der Empfänger der E-Mail muss nicht notwendiger Weise auch Newsletter-Empfänger sein.
   */
  Lettr::multipart_mail("test@example.com", "Test-Betreff", array("text"=>"text/plain", "html"=>"<html><body>text/html</body></html>"));
  
  /**
   * Verschickt eine Multipart-E-Mail über die Lettr-API ohne Template mit Attachment
   * 
   * Der Empfänger der E-Mail muss nicht notwendiger Weise auch Newsletter-Empfänger sein.
   */
  Lettr::multipart_mail("test@example.com", "Test-Betreff", array("text"=>"text/plain", "html"=>"<html><body>text/html</body></html>", "files"=>array("examples.php"=>"@examples.php")));
  
  /**
   * Verschickt eine Multipart-E-Mail über die Lettr-API ohne Template mit Attachment.
   * Setze dabei die Absender-Adresse auf eine bestätigte Absender-Adresse, die
   * von der Standard-Einstellung abweicht.
   * 
   * Der Empfänger der E-Mail muss nicht notwendiger Weise auch Newsletter-Empfänger sein.
   */
  Lettr::multipart_mail("test@example.com", "Test-Betreff", array("text"=>"text/plain", "html"=>"<html><body>text/html</body></html>", "files"=>array("examples.php"=>"@examples.php")), array('sender_address'=>"fummbar@example.net"));
  
  /**
   * Verschickt eine E-Mail über die Lettr-API mit Template
   */
  Lettr::mail_with_template("test@example.com", "Test-Betreff", "confirmation", array("data"=>array("url"=>"http://lettr.de/")));
  
  /**
   * Verschickt eine E-Mail über die Lettr-API mit Template
   * Setze dabei die Absender-Adresse auf eine bestätigte Absender-Adresse, die
   * von der Standard-Einstellung abweicht.
   */
  Lettr::mail_with_template("test@example.com", "Test-Betreff", "confirmation", array("data"=>array("url"=>"http://lettr.de/")), array('sender_address'=>"fummbar@example.net"));
?>