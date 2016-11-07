<?php

/**
 * @file
 * Contains sacra_product.page.inc.
 *
 * Page callback for Product entities.
 */

use Drupal\Core\Render\Element;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Prepares variables for Product templates.
 *
 * Default template: sacra_product.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_sacra_product(array &$variables) {
  // Fetch Product Entity Object.
  $sacra_product = $variables['elements']['#sacra_product'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
