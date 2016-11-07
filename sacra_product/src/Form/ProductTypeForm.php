<?php

namespace Drupal\sacra_product\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ProductTypeForm.
 *
 * @package Drupal\sacra_product\Form
 */
class ProductTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $sacra_product_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $sacra_product_type->label(),
      '#description' => $this->t("Label for the Product type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $sacra_product_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\sacra_product\Entity\ProductType::load',
      ],
      '#disabled' => !$sacra_product_type->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $sacra_product_type = $this->entity;
    $status = $sacra_product_type->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Product type.', [
          '%label' => $sacra_product_type->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Product type.', [
          '%label' => $sacra_product_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($sacra_product_type->urlInfo('collection'));
  }

}
