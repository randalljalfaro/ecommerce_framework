<?php

namespace Drupal\sacra_product\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\sacra_product\Entity\ProductInterface;

/**
 * Class ProductController.
 *
 *  Returns responses for Product routes.
 *
 * @package Drupal\sacra_product\Controller
 */
class ProductController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Product  revision.
   *
   * @param int $sacra_product_revision
   *   The Product  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($sacra_product_revision) {
    $sacra_product = $this->entityManager()->getStorage('sacra_product')->loadRevision($sacra_product_revision);
    $view_builder = $this->entityManager()->getViewBuilder('sacra_product');

    return $view_builder->view($sacra_product);
  }

  /**
   * Page title callback for a Product  revision.
   *
   * @param int $sacra_product_revision
   *   The Product  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($sacra_product_revision) {
    $sacra_product = $this->entityManager()->getStorage('sacra_product')->loadRevision($sacra_product_revision);
    return $this->t('Revision of %title from %date', array('%title' => $sacra_product->label(), '%date' => format_date($sacra_product->getRevisionCreationTime())));
  }

  /**
   * Generates an overview table of older revisions of a Product .
   *
   * @param \Drupal\sacra_product\Entity\ProductInterface $sacra_product
   *   A Product  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(ProductInterface $sacra_product) {
    $account = $this->currentUser();
    $langcode = $sacra_product->language()->getId();
    $langname = $sacra_product->language()->getName();
    $languages = $sacra_product->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $sacra_product_storage = $this->entityManager()->getStorage('sacra_product');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $sacra_product->label()]) : $this->t('Revisions for %title', ['%title' => $sacra_product->label()]);
    $header = array($this->t('Revision'), $this->t('Operations'));

    $revert_permission = (($account->hasPermission("revert all product revisions") || $account->hasPermission('administer product entities')));
    $delete_permission = (($account->hasPermission("delete all product revisions") || $account->hasPermission('administer product entities')));

    $rows = array();

    $vids = $sacra_product_storage->revisionIds($sacra_product);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\sacra_product\ProductInterface $revision */
      $revision = $sacra_product_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionAuthor(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->revision_timestamp->value, 'short');
        if ($vid != $sacra_product->getRevisionId()) {
          $link = $this->l($date, new Url('entity.sacra_product.revision', ['sacra_product' => $sacra_product->id(), 'sacra_product_revision' => $vid]));
        }
        else {
          $link = $sacra_product->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => \Drupal::service('renderer')->renderPlain($username),
              'message' => ['#markup' => $revision->revision_log_message->value, '#allowed_tags' => Xss::getHtmlTagList()],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('sacra_product.revision_revert_translation_confirm', ['sacra_product' => $sacra_product->id(), 'sacra_product_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('sacra_product.revision_revert_confirm', ['sacra_product' => $sacra_product->id(), 'sacra_product_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('sacra_product.revision_delete_confirm', ['sacra_product' => $sacra_product->id(), 'sacra_product_revision' => $vid]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['sacra_product_revisions_table'] = array(
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    );

    return $build;
  }

}
