<?php

namespace Drupal\sacra_product;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\sacra_product\Entity\ProductInterface;

/**
 * Defines the storage handler class for Product entities.
 *
 * This extends the base storage class, adding required special handling for
 * Product entities.
 *
 * @ingroup sacra_product
 */
class ProductStorage extends SqlContentEntityStorage implements ProductStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(ProductInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {sacra_product_revision} WHERE id=:id ORDER BY vid',
      array(':id' => $entity->id())
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {sacra_product_field_revision} WHERE uid = :uid ORDER BY vid',
      array(':uid' => $account->id())
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(ProductInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {sacra_product_field_revision} WHERE id = :id AND default_langcode = 1', array(':id' => $entity->id()))
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('sacra_product_revision')
      ->fields(array('langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED))
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
