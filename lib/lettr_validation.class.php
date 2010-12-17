<?php
  /**
   * @package Lettr
   * @subpackage Validation
   * @access private
   * @author Digineo GmbH
   * @copyright Digineo GmbH, 2010
   * @link http://www.digineo.de
   * @link mailto:kontakt@digineo.de
   */
  class Lettr_Validation {
    public static function presence_of($array_name, $array, $attribute_list){
      if(!is_array($array)){
        throw new Lettr_IllegalArgumentException('Argument ist kein Array');
      }
      foreach($attribute_list as $attr){
        if(empty($array[$attr])){
          throw new Lettr_IllegalArgumentException('$'.$array_name.'["'.$attr.'"] ist leer oder nicht definiert.');
        }
      }
    }
  }
?>