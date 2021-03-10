<?php

namespace Drupal\netflix_show\Event;

use Drupal\file\Entity\File;
use Symfony\Component\EventDispatcher\Event;

/**
 * Event that is parses csv with shows and makes nodes.
 */
class ParseCSVEvent extends Event {

  const EVENT_NAME = 'parse_csv';

  protected $file;

  /**
   * Constructor.
   *
   * @param $file
   */
  public function __construct($file) {
    $this->file = $file;
  }

  /**
   * Parse csv
   */
  public function run() {
    $form_file = $this->file;
    if (isset($form_file[0]) && !empty($form_file[0])) {
      $file = File::load($form_file[0]);
      $handle = fopen($file->getFileUri(),"r");
      for ($i = 0; $i < 2; $i++) {
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

