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
class Customtokenadd extends ConfigFormBase {
 
  /**
   * Constructor for ComproCustomForm.
   *s
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
    return 'token_custom_settings_admin_form';
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
  public function buildForm(array $form, FormStateInterface $form_state,$token_custom = NULL) {
  
  $token_machine = $token_custom;
  $token = '';
  if(isset($token_machine) && !empty($token_machine)){
  	$token = db_select('token_custom','tc')->fields('tc')->condition('machine_name',$token_machine,'=')->execute()->fetchAll();
  }
  $form['name'] = array(
    '#type'   => 'textfield',
    '#title' => t('Token name'),
    '#description' => t("The token's readable name"),
    '#default_value' => $token ? $token[0]->name : NULL,
    '#maxlength' => 128,
    '#required' => TRUE,
  );
  $form['machine_name'] = array(
    '#type'       => 'machine_name',
    '#title'     => t('Token machine name'),
    '#description'    => t('A unique machine-readable name for this token. It must only contain lowercase letters, numbers, and underscores.'),
    '#default_value' => $token ? $token[0]->machine_name : NULL,
    '#maxlength' => 32,
    '#disabled'   => (bool) $token,
    '#machine_name'  => array(
      'replace' =>  '-',
      'replace_pattern' => '[^a-z0-9\-]+',
    ),
  );

  $form['description'] = array(
    '#type'   => 'textfield',
    '#title' => t('Token description'),
    '#description' => t("The token's description that will appear in the token list"),
    '#default_value' => $token ? $token[0]->description : NULL,
    '#maxlength'      => 255,
    '#required' => TRUE,
  );
 $token_service = \Drupal::token();
 $token_info = $token_service->getInfo();
  $options = array('custom' => 'Custom Token');
  foreach ($token_info['types'] as $type => $info) {
    $options[$type] = $info['name'];
    if (!empty($info['needs-data'])) {
      $options[$type] .= ' [needs: ' . $info['needs-data'] . ']';
    }
  }

  $form['type'] = array(
    '#type'   => 'select',
    '#title' => 'Token type',
    '#description' => t('The token type determines the availability of the token according to the data in the $data array (ex. a token of type <em>node</em> will need $data[node].'),
    '#options' => $options,
    '#maxlength' => 128,
    '#default_value' => ($token && !empty($token[0]->type)) ? $token[0]->type : 0,
  );

  $form['content'] = array(
    '#type' => 'text_format',
    '#title' => t('Content'),
    '#description' => t('Enter the content that will be replaced with this token.'),
    '#default_value' => isset($token[0]->content) ? $token[0]->content : '',
    '#format' => isset($token[0]->format) ? $token[0]->format : filter_default_format(),
  );

  // Add help text if PHP filter is available.
  // if (module_exists('php')) {
  //   $ref = l('token_replace()', 'http://api.drupal.org/api/drupal/includes--token.inc/function/token_replace/7',
  //            array('attributes' => array('target' => '_blank')));
  //   $form['content']['#description']
  //     .= '<br />'
  //     .  t('PHP Filter : You will have access to all the arguments of !link (ex : $data[\'node\'] for node token type, $options). Be sure to carefully read the documentation regarding the security implications of using the php input filter.',
  //          array('!link' => $ref)
  //        );
  // }


  /*if ($op == 'edit') {
    $form['delete'] = array(
      '#type'   => 'submit',
      '#value' => t('Delete'),
    );
  }*/
    return parent::buildForm($form, $form_state);
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {


  }
  public function submitForm(array &$form, FormStateInterface $form_state) {
				 $token = array(
    		'name' => $form_state->getValues()['name'],
    		'description' => $form_state->getValues()['description'],
    		'type' => $form_state->getValues()['type'],
    		'content' => $form_state->getValues()['content']['value'],
    		'format' => $form_state->getValues()['content']['format'],
    		'is_new' => 1,
  		  );
  		   \Drupal::database()->merge('token_custom')
           ->key(array('machine_name' => $form_state->getValues()['machine_name']))
           ->fields($token)
           ->execute();
        drupal_set_message("Your custom token: ".$form_state->getValues()['name']." is saved");
        $form_state->setRedirectUrl(new Url('token_custom.menus'));

  }
}
