<?php 

namespace Drupal\token_custom\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Render\Element;
use Drupal\Core\Utility;
use Drupal\Core\Url;

/**
 * Configure custom settings for this site.
 */
class Customtokenaddtype extends ConfigFormBase {
  /**
   * Constructor for ComproCustomForm.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    parent::__construct($config_factory);
  }

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'token_custom_type_settings_admin_form';
  }
  /**
   * Gets the configuration names that will be editable.
   *
   * @return array
   *   An array of configuration object names that are editable if called in
   *   conjunction with the trait's config() method.
   */
   protected function getEditableConfigNames() {
    return [
      'token_custom.settings',
    ];
  }
  /**
   * Form constructor.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The form structure.
   */
  public function buildForm(array $form, FormStateInterface $form_state,$token_custom_type = NULL) {
   //  $form_state['token_custom']['op'] = $op;
    // $form_state['token_custom']['type'] = $type;
    $token_machine = $token_custom_type;
    $token_type = '';
    if(isset($token_machine) && !empty($token_machine)){
      $token_type = db_select('token_custom_type','tc')->fields('tc')->condition('machine_name',$token_machine,'=')->execute()->fetchAll();
    }
  $form['name'] = array(
    '#type'   => 'textfield',
    '#title' => t("Token type's name"),
    '#description' => t("The token types's readable name"),
    '#default_value' => $token_type ? $token_type[0]->name : NULL,
    '#maxlength' => 255,
    '#required' => TRUE,
  );

  $form['machine_name'] = array(
    '#type'       => 'machine_name',
    '#title'     => t("Token type's machine name"),
    '#description'    => t('A unique machine-readable name for this token. It must only contain lowercase letters, numbers, and underscores.'),
    '#default_value' => $token_type ? $token_type[0]->machine_name : NULL,
    '#maxlength' => 32,
    '#disabled'   => (bool) $token_type,
    '#machine_name'  => array(
      'replace' =>  '-',
      'replace_pattern' => '[^a-z0-9\-]+',
    ),
  );

  $form['description'] = array(
    '#type'   => 'textfield',
    '#title' => t('Token description'),
    '#description' => t("The token type's description."),
    '#default_value' => $token_type ? $token_type[0]->description : NULL,
    '#required' => TRUE,
  );



    return parent::buildForm($form, $form_state);
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {


  }
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $token = array(
        'name' => $form_state->getValues()['name'],
        'machine_name' => $form_state->getValues()['machine_name'],
        'description' => $form_state->getValues()['description'],
        );
         \Drupal::database()->merge('token_custom_type')
           ->key(array('machine_name' => $form_state->getValues()['machine_name']))
           ->fields($token)
           ->execute();
        drupal_set_message("Your custom token type: ".$form_state->getValues()['name']." is saved");
        $form_state->setRedirectUrl(new Url('token_custom_type.menus'));

  }
}
