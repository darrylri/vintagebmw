<?php

namespace Drupal\Tests\range\Kernel\Formatter;

use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\range\Kernel\TestHelperTrait;
use Drupal\entity_test\Entity\EntityTest;

/**
 * Base class for range functional integration tests.
 */
abstract class FormatterTestBase extends KernelTestBase {

  use TestHelperTrait;

  /**
   * @var string[]
   */
  protected static $modules = [
    'system',
    'field',
    'text',
    'entity_test',
    'user',
    'range'
  ];

  /**
   * @var string
   */
  protected $fieldType;

  /**
   * @var string
   */
  protected $fieldName;

  /**
   * @var string
   */
  protected $displayType;

  /**
   * @var \Drupal\entity_test\Entity\EntityTest
   */
  protected $entity;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->installConfig(['system']);
    $this->installConfig(['field']);
    $this->installConfig(['text']);
    $this->installConfig(['range']);
    $this->installEntitySchema('entity_test');

    $this->fieldName = $this->getFieldName($this->fieldType);
    $this->createField($this->fieldType);
    $this->createViewDisplay();

    $this->entity = EntityTest::create([]);
  }

  /**
   * Tests formatter.
   *
   * @dataProvider formatterDataProvider
   */
  public function testFieldFormatter(array $display_settings, $from, $to, $expected_output) {
    $this->entity->{$this->fieldName} = [
      'from' => $from,
      'to' => $to,
    ];

    $this->setViewDisplayComponent($this->fieldType, $this->displayType, $display_settings);
    $this->renderEntityFields($this->entity);
    $this->assertText($expected_output);
  }

  /**
   * Data provider for testFieldFormatter().
   *
   * @return array
   *   Nested arrays of values to check:
   *     - $display_settings
   *     - $from
   *     - $to
   *     - $expected_output
   */
  abstract public function formatterDataProvider();

  /**
   * Renders fields of a given entity.
   *
   * @param \Drupal\Core\Entity\FieldableEntityInterface $entity
   *   The entity object with attached fields to render.
   */
  protected function renderEntityFields(FieldableEntityInterface $entity) {
    $content = $this->viewDisplay->build($entity);
    $this->render($content);
  }

}
