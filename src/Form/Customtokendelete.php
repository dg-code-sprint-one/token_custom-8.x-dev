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
class CustomtokenDelete extends ConfirmFormBase {

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
    return 'token_custom_delete';
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
    return new Url('token_custom.menus');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $token_custom = NULL) {
     $this->machine_name = $token_custom;
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state, $token_custom = NULL) {
  $db = \Drupal::database();
     $del_confirm = $db->delete('token_custom')->condition('machine_name',$this->machine_name,'=')->execute();
     if($del_confirm){
     		drupal_set_message("successfully deleted");
     		$form_state->setRedirect('token_custom.menus');
     } else {
     		drupal_set_message("There is an error da",'error');
     }
     
  }

}
