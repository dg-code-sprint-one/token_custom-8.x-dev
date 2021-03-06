<?php

namespace Drupal\token_custom\Controller;

use Drupal\Core\Controller\ControllerBase;

abstract class Customtokenbase extends ControllerBase {
  public function token_custom_type_load_multiple() {
  	$types = array();
  	$default_type = new \stdClass();
  	$default_type->name = t('Custom');
	$default_type->machine_name = 'custom';
	$default_type->description = t('User created tokens types using the Custom Tokens module.');
	$types['custom'] = $default_type;
	  // Load token types and make sure the custom type is always there,
	  // and wasn't deleted somewhere.
  	//$types = variable_get('token_custom_types', array());
  	$token_types = db_select('token_custom_type','tt')->fields('tt')->execute()->fetchAll(); 
  	foreach($token_types as $token_type) {
  		$custom_token_type = new \stdClass();
	  	$custom_token_type->name = $token_type->name;
		$custom_token_type->machine_name = $token_type->machine_name;
		$custom_token_type->description = $token_type->description;
		$types[$token_type->machine_name] = $custom_token_type;
  	}
  	
  	return $types;
	}
	/**
	 * Loads an array of tokens from the database.
	 *
	 * Maintains a static cache with the tokens already loaded to
	 * avoid unnecessary queries.
	 *
	 * @param array $names
	 *   An array containing the machine names of the tokens to return.
	 *   If none, then loads and returns all the tokens.
	 *
	 * @return array
	 *   An array of token objects, keyed by the token's machine name.
	 */
	function token_custom_load_multiple($names = NULL) {
	
	  // FIXME Use drupal_static instead of static keyword.
	  static $tokens = array();
	  static $all_loaded = FALSE;
	
	  if ($names === NULL) {
	    if (!$all_loaded) {
	      $loaded = array_keys($tokens);
	      $query = db_select('token_custom')->fields('token_custom');
	      if (!empty($loaded)) {
	        $query->condition('machine_name', $loaded, 'NOT IN');
	      }
	      $results = $query->execute();
	      $all_loaded = TRUE;
	      foreach ($results as $token) {
	        $tokens[$token->machine_name] = $token;
	      }
	    }
	
	    return $tokens;
	  }
	
	  $to_fetch = array();
	  foreach ($names as $name) {
	    if (!array_key_exists($name, $tokens)) {
	      $to_fetch[] = $name;
	    }
	  }
	
	  if (!empty($to_fetch)) {
	    $query = db_select('token_custom')
	               ->fields('token_custom')
	               ->condition('machine_name', $to_fetch, 'IN');
	    $results = $query->execute();
	
	    foreach ($results as $token) {
	      $tokens[$token->machine_name] = $token;
	    }
	  }
	
	  $return = array();
	  foreach ($names as $name) {
	    if (isset($tokens[$name])) {
	      $return[$name] = $tokens[$name];
	    }
	  }
	
	  return $return;
	}


}
