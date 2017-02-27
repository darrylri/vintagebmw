<?php

namespace Drupal\range\Tests\Views;

use Drupal\views\Views;

/**
 * Tests the Drupal\range\Plugin\views\filter\Range handler.
 *
 * @group range
 */
class RangeViewsFilterTest extends RangeViewsTestBase {

  /**
   * {@inheritdoc}
   */
  public static $testViews = ['test_filter_range'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    // Add some basic test nodes.
    $ranges = [
      ['from' => 0, 'to' => 10],
      ['from' => 5, 'to' => 9],
      ['from' => -5, 'to' => 15],
      ['from' => -10, 'to' => 5],
    ];
    foreach ($ranges as $range) {
      $this->nodes[] = $this->drupalCreateNode([
        'type' => $this->bundle,
        $this->field_name => $range,
      ]);
    }
  }

  /**
   * Tests range views filter.
   */
  public function testRangeViewsFilter() {
    $view = Views::getView('test_filter_range');

    // Range contains, exclude endpoints.
    $view->initHandlers();
    $view->filter[$this->field_name]->operator = 'within';
    $view->filter[$this->field_name]->value = 5;
    $view->filter[$this->field_name]->options['include_endpoints'] = FALSE;
    $view->setDisplay('default');
    $this->executeView($view);
    $expected_result = [
      ['nid' => $this->nodes[0]->id()],
      ['nid' => $this->nodes[2]->id()],
    ];
    $this->assertIdenticalResultset($view, $expected_result, $this->map, 'Range views filter (contains, exclude endpoints) produces correct results');
    $view->destroy();

    // Range contains, include endpoints.
    $view->initHandlers();
    $view->filter[$this->field_name]->operator = 'within';
    $view->filter[$this->field_name]->value = 5;
    $view->filter[$this->field_name]->options['include_endpoints'] = TRUE;
    $view->setDisplay('default');
    $this->executeView($view);
    $expected_result = [
      ['nid' => $this->nodes[0]->id()],
      ['nid' => $this->nodes[1]->id()],
      ['nid' => $this->nodes[2]->id()],
      ['nid' => $this->nodes[3]->id()],
    ];
    $this->assertIdenticalResultset($view, $expected_result, $this->map, 'Range views filter (contains, include endpoints) produces correct results');
    $view->destroy();

    // Range does contains, exclude endpoints.
    $view->initHandlers();
    $view->filter[$this->field_name]->operator = 'not within';
    $view->filter[$this->field_name]->value = 5;
    $view->filter[$this->field_name]->options['include_endpoints'] = FALSE;
    $view->setDisplay('default');
    $this->executeView($view);
    $expected_result = [
      ['nid' => $this->nodes[1]->id()],
      ['nid' => $this->nodes[3]->id()],
    ];
    $this->assertIdenticalResultset($view, $expected_result, $this->map, 'Range views filter (does not contain, exclude endpoints) produces correct results');
    $view->destroy();

    // Range does contains, include endpoints.
    $view->initHandlers();
    $view->filter[$this->field_name]->operator = 'not within';
    $view->filter[$this->field_name]->value = 5;
    $view->filter[$this->field_name]->options['include_endpoints'] = TRUE;
    $view->setDisplay('default');
    $this->executeView($view);
    $expected_result = [];
    $this->assertIdenticalResultset($view, $expected_result, $this->map, 'Range views filter (does not contain, include endpoints) produces correct results');
    $view->destroy();
  }

}
