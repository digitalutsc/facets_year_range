<?php

declare(strict_types = 1);

namespace Drupal\facets_year_range\Plugin\facets\widget;

use Drupal\Core\Form\FormStateInterface;
use Drupal\facets\FacetInterface;
use Drupal\facets\Widget\WidgetPluginBase;

/**
 * The Date Range widget.
 *
 * @FacetsWidget(
 *   id = "year_range",
 *   label = @Translation("Year Range Picker"),
 *   description = @Translation("A widget that shows a Year Range Picker."),
 * )
 */
class YearRangeWidget extends WidgetPluginBase {

  /**
   * {@inheritdoc}
   */
  public function build(FacetInterface $facet): array {
    $build = parent::build($facet);
    $results = $facet->getResults();
    if (empty($results)) {
      return $build;
    }

    ksort($results);

    $active = $facet->getActiveItems();
    $min = reset($active)['min'] ?? NULL;
    $max = reset($active)['max'] ?? NULL;

    /*if (isset($min) || empty($min)) {
    $min = 0;
    }
    if (isset($max) || empty($max)) {
    $max = date("Y");
    }*/

    $build['#items'] = [
      'min' => [
        '#type' => 'textfield',
        '#title' => $this->t('From'),
        '#value' => $min,
        '#attributes' => [
          'class' => ['facet-yearpicker-min','facet-year-range'],
          'id' => $facet->id() . '_min',
          'name' => $facet->id() . '_min',
          'data-type' => 'year-range-min',
          'placeholder' => $this->t('From'),
          'aria-label' => $this->t('From'),
        ],
        '#theme_wrappers' => [],
      ],
      'max' => [
        '#type' => 'textfield',
        '#title' => $this->t('To'),
        '#value' => $max,
        '#attributes' => [
          'class' => ['facet-yearpicker-max', 'facet-year-range'],
          'id' => $facet->id() . '_max',
          'name' => $facet->id() . '_max',
          'data-type' => 'year-range-max',
          'placeholder' => $this->t('To'),
          'aria-label' => $this->t('To'),
        ],
        '#theme_wrappers' => [],
      ],
    ];

    $build['#items']['refine'] = [
      [
        '#type' => 'submit',
        '#attributes' => [
          'type' => 'submit',
          'class' => ['facet-yearpicker-submit', 'clipboard-button'],
          'id' => $facet->id() . '-submit',
          'name' => $facet->id() . '-submit',
          'data-type' => 'datepicker-submit',
        ],
        '#value' => "Refine",
      ],
    ];

    $url = array_shift($results)->getUrl()->toString();
    $build['#attached']['library'][] = 'facets_year_range/year-range';
    $build['#attached']['drupalSettings']['facets']['daterange'][$facet->id()] = [
      'alias' => $facet->getUrlAlias(),
      'url' => $url,
    ];
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function isPropertyRequired($name, $type): bool {
    return $name === 'year_range' && $type === 'processors';
  }

  /**
   * {@inheritdoc}
   */
  public function getQueryType(): string {
    return 'range';
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state, FacetInterface $facet): array {
    $form += parent::buildConfigurationForm($form, $form_state, $facet);

    $message = $this->t('To achieve the standard behavior of a Date Range Picker, you need to enable the facet setting below <em>"Date Range Picker"</em>.');
    $form['warning'] = [
      '#markup' => '<div class="messages messages--warning">' . $message . '</div>',
    ];

    return $form;
  }

}
