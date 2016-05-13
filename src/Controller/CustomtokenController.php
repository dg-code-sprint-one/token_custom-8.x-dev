<?php
/**
 * @file
 * Contains \Drupal\hello_world\Controller\HelloController.
 */

namespace Drupal\token_custom\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

class CustomtokenController extends Customtokenbase {
  public function token_custom_list_page() {
    $tokens = parent::token_custom_load_multiple();
    $account = \Drupal::currentUser();     
    $token_admin = $account->hasPermission('administer custom tokens');
	
	$variables = array();

	// Build the table rows.
	foreach ($tokens as $token) {
		// Get demo value if token doesn't need external data.
		$token_service = \Drupal::token();
		$token_info = $token_service->getInfo();
		if (empty($token_info['types'][$token->type]['needs-data'])) {
			$value = \Drupal::token()->replace('[' . $token->type . ':' . $token->machine_name . ']');
			$value = '';
		}
		else {
			$value = t('Demo value not available');
		}

		$row = array(
			$token->name,
			$token->machine_name,
			$token->type,
			$token->description,
			$value,
		);

		// Add the edit/delete links if the user has the right permissions.
		if ($token_admin) {

	  $edit_url = Url::fromRoute('token_custom_edit.menus',array('token_custom'=>$token->machine_name));
      $edit_link = \Drupal::l(t('Edit'), $edit_url);
      $delete_url = Url::fromRoute('token_custom_delete.menus',array('token_custom'=>$token->machine_name));
      $delete_link = \Drupal::l(t('Delete'), $delete_url);
      $row[] = $edit_link;
      $row[] = $delete_link;
		} 

		$variables['rows'][] = $row;
	}
	
	if (empty($variables['rows'])) {
		$variables['rows'][] = array(
			array(
				'data' => t('No custom tokens available.'),
				'colspan' => array($token_admin ? 7 : 5),
			),
		);
	}

	$variables['header'] = array(
		t('Name'),
		t('Machine name'),
		t('Type'),
		t('Description'),
		t('Demo (if available)'),
		t('EDIT'),
		t('DELETE'),
	);

	// Add extra header cell if edit/delete links were printed.
	if ($token_admin) {
		$variables['header'][] = "";
	}
	$table = array(
  '#theme' => 'table__menu_overview',
  '#header' => $variables['header'],
  '#rows' => $variables['rows'],
  '#responsive' => FALSE,
);
	return $table;
}

public function token_custom_type_list_page() {
   $types = parent::token_custom_type_load_multiple();
    $account = \Drupal::currentUser();     
    $token_admin = $account->hasPermission('administer custom tokens');
	
	$variables = array();

	// Build the table rows.
	foreach ($types as $type) {

		// Get demo value if token doesn't need external data.
		$token_service = \Drupal::token();
		$token_info = $token_service->getInfo();
      foreach ($types as $type) {
    $row = array(
      $type->name,
      $type->machine_name,
      $type->description,
    );

  // Do not show edit/delete links for the custom type.
    if ($type->machine_name != 'custom') {
      $edit_url = Url::fromRoute('token_custom_type_edit.menus',array('token_custom_type'=>$type->machine_name));
      $edit_link = \Drupal::l(t('Edit'), $edit_url);
      $delete_url = Url::fromRoute('token_custom_type_delete.menus',array('token_custom_type'=>$type->machine_name));
      $delete_link = \Drupal::l(t('Delete'), $delete_url);
      $row[] = $edit_link;
      $row[] = $delete_link;
    }
    else {
      $row[] = '';
    }

    $variables['rows'][] = $row;
  }

  $variables['header'] = array(
    t('Name'),
    t('Machine name'),
    t('Description'), '');

	// Add extra header cell if edit/delete links were printed.
	if ($token_admin) {
		$variables['header'][] = "";
	}

	return $table = array(
  '#theme' => 'table__menu_overview',
  '#header' => $variables['header'],
  '#rows' => $variables['rows'],
  '#responsive' => FALSE,
);

}

}

}



