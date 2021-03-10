<?php

namespace Drupal\netflix_show\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;

/**
 * Defines a form that configures forms module settings.
 */
class ModuleUploadForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'netflix_show_upload';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['csvfile'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Table'),
      '#upload_validators' => [
        'file_validate_extensions' => ['csv', 'CSV'],
      ],
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    $form_file = $form_state->getValue('csvfile', 0);
    if (isset($form_file[0]) && !empty($form_file[0])) {
      $file = File::load($form_file[0]);
      $handle = fopen($file->getFileUri(),"r");
      for ($i = 0; $i < 11; $i++) {
        if (($data = fgetcsv($handle)) === FALSE) break;
        if ($i == 0) continue;
        $fields = [
          'type' => 'netflix_title',
          'title' => $data[2],
          'field_text_plain' => $data[3],
          'field_text_plain_long' => $data[4],
          'field_text_plain_1' => $data[5],
          'field_number_int' => $data[7],
          'field_text_plain_2' => $data[9],
          // TODO
          // 'field_image' => ,
          'field_text_plain_long_1' => $data[11],
        ];
        $node = \Drupal::entityTypeManager()
          ->getStorage('node')
          ->create($fields);
        $node->save();
      }
    }
  }
}
