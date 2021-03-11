<?php

namespace Drupal\netflix_show\Event;

use Drupal\Core\File\FileSystemInterface;
use Drupal\file\Entity\File;
use Symfony\Component\EventDispatcher\Event;
use GuzzleHttp\Client;

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
        $title = $data[2];
        $fields = [
          'type' => 'netflix_title',
          'title' => $title,
          'field_text_plain' => $data[3],
          'field_text_plain_long' => $data[4],
          'field_text_plain_1' => $data[5],
          'field_number_int' => $data[7],
          'field_text_plain_2' => $data[9],
          'field_text_plain_long_1' => $data[11],
        ];
        $node = \Drupal::entityTypeManager()
          ->getStorage('node')
          ->create($fields);
        if ($image_url = $this->getImage($title) != '') {
          $image = file_get_contents($image_url);
          $file_temp = system_retrieve_file($image, NULL, TRUE, FileSystemInterface::EXISTS_RENAME);
          if ($file_temp !== FALSE) {
            $node->set('field_image', ['target_id' => $file_temp->fid,]);
          }
          $node->save();
        }
      }
    }
  }

  private function getImage($title) {
    $client = new Client;

    $request = $client->request('GET', 'https://movie-database-imdb-alternative.p.rapidapi.com/', [
      'query' => [
        's' => $title,
        'page' => '1',
        'r' => 'json',
      ],
      'headers' => [
        'x-rapidapi-key' => 'c554ec35c5msh4eabb92fa586e9bp1e0957jsn68ea1eccfc74',
        'x-rapidapi-host' => 'movie-database-imdb-alternative.p.rapidapi.com'
      ]
    ]);

    $response = $request->getBody()->getContents();
    $json = json_decode($response);
    if ($json->Response) {
      return $json->Search[0]->Poster;
    }
    return '';
  }
}

