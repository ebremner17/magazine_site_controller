<?php

/**
 * @file
 * Contains Drupal\magazine_site_controller\Form\SiteSettingsForm.
 */

namespace Drupal\magazine_site_controller\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class SiteSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'magazine.sitesettings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'site_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config('magazine.sitesettings');


    $form['theme_info'] = [
      '#title' => t('Theme Info'),
      '#type' => 'fieldset',
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    ];

    $form['theme_info']['logo'] = array(
      '#type' => 'managed_file',
      '#upload_location' => 'public://images/',
      '#title' => $this->t('Logo'),
      '#description' => t("The logo for the magazine"),
      '#default_value' => $config->get('crest') !== NULL ? $config->get('logo') : '',
      '#upload_validators' => array(
        'file_validate_extensions' => array('png jpg jpeg'),
        'file_validate_size' => array(25600000),
      ),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    parent::submitForm($form, $form_state);

    if ($form_state->getValue('logo')) {

      /* Fetch the array of the file stored temporarily in database */
      $image = $form_state->getValue('logo');

      $this->config('magazine.sitesettings')
        ->set('logo', $form_state->getValue('logo'))
        ->save();

      /* Load the object of the file by it's fid */
      $file = \Drupal\file\Entity\File::load($image[0]);

      /* Set the status flag permanent of the file object */
      $file->setPermanent();

      /* Save the file in database */
      $file->save();
    }
    else {
      $this->config('magazine.sitesettings')
        ->set('logo', NULL)
        ->save();
    }

    /*
    $values_to_process = [
      'type_of_cadets',
      'corps_number',
      'corps_name',
      'cadet_theme',
      'parade_night',
      'parade_night_start_time',
      'parade_night_end_time',
      'parade_night_address',
      'parade_night_city',
      'parade_night_province',
      'phone_number',
      'email_address',
      'mailing_address',
      'mailing_address_phone',
      'mailing_address_city',
      'mailing_address_province',
      'mailing_address_postal_code',
      'notification_email_address',
      'notification_name'
    ];

    foreach ($values_to_process as $value_to_process) {
      $this->config('cc_site_controller.sitesettings')
        ->set($value_to_process, $form_state->getValue($value_to_process))
        ->save();
    }
    */
  }

}