<?php

/**
 * @file
 * Contains \Drupal\path\Form\DeleteForm.
 */

namespace Drupal\token_custom\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Builds the form to delete a path alias.
 */
class CustomtokentypeDelete extends ConfirmFormBase {

  /**
   * The alias storage service.
   *
   * @var AliasStorageInterface $path
   */
  protected $machine_name;

  /**
   * The path alias being deleted.
   *
   * @var array $pathAlias
   */


  /**
   * Constructs a \Drupal\path\Form\DeleteForm object.
   *
   * @param \Drupal\Core\Path\AliasStorageInterface $alias_storage
   *   The alias storage service.
   */
  /**
   * {@inheritdoc}
   */
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'token_custom_type_delete';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return t('Do you want to delete %name?', array('%name' => $this->machine_name));
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('token_custom_type.menus');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $token_custom_type = NULL) {
     $this->machine_name = $token_custom_type;
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state, $token_custom_type = NULL) {
  $db = \Drupal::database();

     $del_confirm = $db->delete('token_custom_type')->condition('machine_name',$this->machine_name,'=')->execute();
     if($del_confirm){
     		drupal_set_message("successfully deleted");
     		$form_state->setRedirect('token_custom_type.menus');
     } else {
     		drupal_set_message("There is an error",'error');
     }
     
  }

}
