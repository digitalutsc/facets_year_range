<?php

declare(strict_types = 1);

namespace Drupal\facets_year_range\Plugin\facets\processor;

use Drupal\facets\FacetInterface;
use Drupal\facets\Processor\PreQueryProcessorInterface;
use Drupal\facets\Processor\ProcessorPluginBase;
use Drupal\facets\Processor\BuildProcessorInterface;

/**
 * Provides a processor that show all values between a range of dates.
 *
 * @FacetsProcessor(
 *   id = "year_range",
 *   label = @Translation("Year Range Picker"),
 *   description = @Translation("Show all content between min and max range dates."),
 *   stages = {
 *     "pre_query" = 60,
 *     "build" = 20
 *   }
 * )
 */
class YearRangeProcessor extends ProcessorPluginBase implements PreQueryProcessorInterface, BuildProcessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preQuery(FacetInterface $facet): void {
    $active_items = $facet->getActiveItems();

    array_walk($active_items, function (&$item) {
      if (preg_match('/\(min:([^,]*),max:(.*)\)/i', $item, $matches)) {
        if (!empty($matches[1]) && !empty($matches[2])) {
          $item = [
            0 => $matches[1],
            1 => $matches[2],
            'min' => $matches[1],
            'max' => $matches[2],
          ];
        }
        elseif (!empty($matches[1]) && empty($matches[2])) {
          $item = [
            0 => $matches[1],
            1 => date('U', strtotime('+100 years')),
            'min' => $matches[1],
          ];
        }
        elseif (empty($matches[1]) && !empty($matches[2])) {
          $item = [
            0 => date('U', 0),
            1 => $matches[2],
            'max' => $matches[2],
          ];
        }
        else {
          $item = [
            date('U', 0),
            date('U', strtotime('+100 years')),
          ];
        }
      }
      else {
        $item = [];
      }
    });
    $facet->setActiveItems($active_items);
  }

  /**
   * {@inheritdoc}
   */
  public function build(FacetInterface $facet, array $results): array {
    /** @var \Drupal\facets\Plugin\facets\processor\UrlProcessorHandler $url_processor_handler */
    $url_processor_handler = $facet->getProcessors()['url_processor_handler'];
    $url_processor = $url_processor_handler->getProcessor();
    $filter_key = $url_processor->getFilterKey();

    /** @var \Drupal\facets\Result\ResultInterface[] $results */
    foreach ($results as &$result) {
      $url = $result->getUrl();
      $query = $url->getOption('query');

      // Remove all the query filters for the field of the facet.
      if (isset($query[$filter_key])) {
        foreach ($query[$filter_key] as $id => $filter) {
          if (strpos($filter, $facet->getUrlAlias()) === 0) {
            unset($query[$filter_key][$id]);
          }
        }
      }

      $query[$filter_key][] = $facet->getUrlAlias() . $url_processor->getSeparator() . '(min:__year_range_min__,max:__year_range_max__)';
      $url->setOption('query', $query);
      $result->setUrl($url);
    }
    return $results;
  }

}
