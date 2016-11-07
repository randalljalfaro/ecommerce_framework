<?php

namespace Drupal\sacra_product\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Product type entity.
 *
 * @ConfigEntityType(
 *   id = "sacra_product_type",
 *   label = @Translation("Product type"),
 *   handlers = {
 *     "list_builder" = "Drupal\sacra_product\ProductTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\sacra_product\Form\ProductTypeForm",
 *       "edit" = "Drupal\sacra_product\Form\ProductTypeForm",
 *       "delete" = "Drupal\sacra_product\Form\ProductTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\sacra_product\ProductTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "sacra_product_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "sacra_product",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/sacra_product_type/{sacra_product_type}",
 *     "add-form" = "/admin/structure/sacra_product_type/add",
 *     "edit-form" = "/admin/structure/sacra_product_type/{sacra_product_type}/edit",
 *     "delete-form" = "/admin/structure/sacra_product_type/{sacra_product_type}/delete",
 *     "collection" = "/admin/structure/sacra_product_type"
 *   }
 * )
 */
class ProductType extends ConfigEntityBundleBase implements ProductTypeInterface {

  /**
   * The Product type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Product type label.
   *
   * @var string
   */
  protected $label;

}
