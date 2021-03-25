<?php

namespace Drupal\netflix_show\Controller;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\media\Entity\Media;
use \Drupal\node\Entity\Node;
use Drupal\Core\Controller\ControllerBase;

class RandomNode extends ControllerBase
{
  public function content()
  {
    $node_info = $this->get();

    $response = new AjaxResponse();
    $Selector = '.netflix-show-title';
    $content = '<div class="netflix-show-title">'. $node_info['title'] .'</div>';
    $response->addCommand(new ReplaceCommand($Selector, $content));

    $Selector = '.netflix-show-poster';
    $content = '<div class="netflix-show-poster"> <img src="'. $node_info['img'] .'" alt = "Poster"></div>';
    $response->addCommand(new ReplaceCommand($Selector, $content));

    $Selector = '.netflix-show-url';
    $content = '<div class="netflix-show-url"> <a href="'. $node_info['url'] .'">Read more</a></div>';
    $response->addCommand(new ReplaceCommand($Selector, $content));
    return $response;
  }

  public function get() {
    $result = [];
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'netflix_title')
      ->addTag('sort_by_random');
    $nids = $query->execute();
    $key = array_rand($nids);
    $nid = $nids[$key];
    $node = Node::load($nid);
    $result['title'] = $node->get('title')->value;
    $image = Media::load($node->get('field_remote_image')->target_id);
    if ($image != null) {
      $result['img'] = $image->get('name')->value;
    }
    $result['url'] = "/node/" . $nid;
    return $result;
  }
}

