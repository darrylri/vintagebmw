<?php

namespace Drupal\Tests\range\Kernel;

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;

/**
 * Provides helper methods to test range fields.
 */
trait TestHelperTrait {

  /**
   * @var string
   */
  protected $entityType = 'entity_test';

  /**
   * @var string
   */
  protected $bundle = 'entity_test';

  /**
   * @var \Drupal\Core\Field\FieldStorageDefinitionInterface
   */
  protected $fieldStorage;

  /**
   * @var \Drupal\Core\Field\FieldConfigInterface
   */
  protected $field;

  /**
   * @var \Drupal\Core\Entity\Display\EntityFormDisplayInterface
   */
  protected $formDisplay;

  /**
   * @var \Drupal\Core\Entity\Display\EntityViewDisplayInterface
   */
  protected $viewDisplay;

  /**
   * Returns raw field name (without "field_" prefix added by Drupal field UI).
   *
   * @param string $field_type
   *   Range field type. Possible values:
   *     - range_decimal
   *     - range_float
   *     - range_integer
   *
   * @return string
   *   Raw field name.
   */
  protected function getFieldNameRaw($field_type = 'range_integer') {
    return "$field_type";
  }

  /**
   * Returns field name (with "field_" prefix added by Drupal field UI).
   *
   * @param string $field_type
   *   Range field type. Possible values:
   *     - range_decimal
   *     - range_float
   *     - range_integer
   *
   * @return string
   *   Field name.
   */
  protected function getFieldName($field_type = 'range_integer') {
    return 'field_' . $this->getFieldNameRaw($field_type);
  }

  /**
   * Returns test range field storage settings.
   *
   * @param string $field_type
   *   Range field type. Possible values:
   *     - range_decimal
   *     - range_float
   *     - range_integer
   *
   * @return array
   *   Range field storage settings.
   */
  protected function getFieldStorageSettings($field_type = 'range_integer') {
    switch ($field_type) {
      case 'range_integer':
      case 'range_float':
        return [];

      case 'range_decimal':
        return [
          'precision' => 12,
          'scale' => 4,
        ];
    }
  }

  /**
   * Returns test range field settings.
   *
   * @param string $field_type
   *   Range field type. Possible values:
   *     - range_decimal
   *     - range_float
   *     - range_integer
   *
   * @return array
   *   Range field settings.
   */
  protected function getFieldSettings($field_type = 'range_integer') {
    switch ($field_type) {
      case 'range_integer':
      case 'range_float':
      case 'range_decimal':
        return [
          'min' => 0,
          'max' => 100000,
          'from' => [
            'prefix' => 'from_prefix',
            'suffix' => 'from_suffix',
          ],
          'to' => [
            'prefix' => 'to_prefix',
            'suffix' => 'to_suffix',
          ],
        ];
    }
  }

  /**
   * Creates a range field of a given type.
   *
   * @param string $field_type
   *   Range field type. Possible values:
   *     - range_decimal
   *     - range_float
   *     - range_integer
   */
  protected function createField($field_type = 'range_integer') {
    $this->fieldStorage = FieldStorageConfig::create([
      'field_name' => $this->getFieldName($field_type),
      'entity_type' => $this->entityType,
      'type' => $field_type,
      'settings' => $this->getFieldStorageSettings($field_type),
    ]);
    $this->fieldStorage->save();

    $this->field = FieldConfig::create([
      'field_storage' => $this->fieldStorage,
      'bundle' => $this->bundle,
      'settings' => $this->getFieldSettings($field_type),
    ]);
    $this->field->save();
  }

  /**
   * Creates default form display.
   */
  protected function createFormDisplay() {
    $values = [
      'targetEntityType' => $this->entityType,
      'bundle' => $this->bundle,
      'mode' => 'default',
      'status' => TRUE,
    ];
    $this->formDisplay = \Drupal::entityTypeManager()
        ->getStorage('entity_form_display')
        ->create($values);
  }

  /**
   * Sets the display settings for a default form display.
   *
   * @param string $field_type
   *   Range field type. Possible values:
   *     - range_decimal
   *     - range_float
   *     - range_integer
   * @param array $settings
   *   Array of display settings.
   */
  protected function setFormDisplayComponent($field_type = 'range_integer', array $settings = []) {
    if (!$this->formDisplay) {
      $this->createFormDisplay();
    }
    $this->formDisplay->setComponent($this->getFieldName($field_type), [
      'type' => 'range',
      'settings' => $settings,
    ]);
    $this->formDisplay->save();
  }

  /**
   * Creates default view display.
   */
  protected function createViewDisplay() {
    $values = [
      'targetEntityType' => $this->entityType,
      'bundle' => $this->bundle,
      'mode' => 'default',
      'status' => TRUE,
    ];
    $this->viewDisplay = \Drupal::entityTypeManager()
        ->getStorage('entity_view_display')
        ->create($values);
  }

  /**
   * Sets the display settings for a default view display.
   *
   * @param string $field_type
   *   Range field type. Possible values:
   *     - range_decimal
   *     - range_float
   *     - range_integer
   * @param array $settings
   *   Array of display settings.
   */
  protected function setViewDisplayComponent($field_type = 'range_integer', $display_type = 'range_integer', array $settings = []) {
    if (!$this->viewDisplay) {
      $this->createViewDisplay();
    }
    $this->viewDisplay->setComponent($this->getFieldName($field_type), [
      'type' => $display_type,
      'settings' => $settings,
    ]);
    $this->viewDisplay->save();
  }

  /**
   * Deletes previously created range field.
   */
  protected function deleteField() {
    $this->field->delete();
    $this->fieldStorage->delete();
  }

}
