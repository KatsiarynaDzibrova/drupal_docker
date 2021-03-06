<?php

namespace Drupal\netflix_show\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\netflix_show\Event\ParseCSVEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a form that configures forms module settings.
 */
class ModuleUploadForm extends FormBase {

  protected $dispatcher;

  public function __construct($dispatcher) {
    $this->dispatcher = $dispatcher;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
      $container->get('event_dispatcher')
    );
  }

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
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $file = $form_state->getValue('csvfile', 0);
    $e = new ParseCSVEvent($file);
    $event = $this->dispatcher->dispatch(ParseCSVEvent::EVENT_NAME, $e);
    $event->run();
  }
}
