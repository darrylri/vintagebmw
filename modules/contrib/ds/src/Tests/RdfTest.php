<?php

namespace Drupal\ds\Tests;

/**
 * Tests for Rdf integration.
 *
 * @group ds
 */
class RdfTest extends FastTestBase {

  /**
   * Modules to install.
   *
   * @var array
   */
  public static $modules = array(
    'node',
    'field_ui',
    'ds',
    'rdf'
  );

  /**
   * Test rdf integration.
   */
  public function testRdf() {
    \Drupal::service('module_installer')->install(array('ds_test_rdf'));

    /* @var \Drupal\node\NodeInterface $node */
    $node = $this->entitiesTestSetup();

    // Look at node and verify the rdf tags are available
    $this->drupalGet('node/' . $node->id());
    $this->assertRaw('typeof="schema:Article', 'RDF tag found on the node wrapper');
  }

}
