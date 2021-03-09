<?php
/**
 * @file
 * Contains \Drupal\hello_world\Controller\HelloController.
 */
namespace Drupal\netflix_show\Controller;
class NetflixShowController {
  public function content() {
    return array(
      '#type' => 'markup',
      '#markup' => t('Hello, World!'),
    );
  }
}
