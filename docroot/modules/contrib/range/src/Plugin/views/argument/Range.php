<?php

namespace Drupal\range\Plugin\views\argument;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\argument\ArgumentPluginBase;

/**
 * Range views argument.
 *
 * @ingroup views_argument_handlers
 *
 * @ViewsArgument("range")
 */
class Range extends ArgumentPluginBase {

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    $options['operator'] = array('default' => 'within', 'bool' => FALSE);
    $options['include_endpoints'] = array('default' => FALSE, 'bool' => TRUE);

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    $form['operator'] = array(
      '#type' => 'radios',
      '#title' => $this->t('Operator'),
      '#options' => array(
        'within' => $this->t('Range contains'),
        'not within' => $this->t('Range does not contain'),
      ),
      '#default_value' => $this->options['operator'],
      '#group' => 'options][more',
    );
    $form['include_endpoints'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Include endpoints'),
      '#default_value' => !empty($this->options['include_endpoints']),
      '#description' => $this->t('Whether to include endpoints or not.'),
      '#group' => 'options][more',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function query($group_by = FALSE) {
    $this->ensureMyTable();
    $field_from = "$this->tableAlias.{$this->definition['additional fields']['from']}";
    $field_to = "$this->tableAlias.{$this->definition['additional fields']['to']}";

    $operator = $this->options['operator'];
    $operators = $this->operators();
    if (!empty($operators[$operator]['method'])) {
      $this->{$operators[$operator]['method']}($field_from, $field_to);
    }
  }

  /**
   * Operator callback.
   */
  protected function opWithin($field_from, $field_to) {
    $operators = array(
      '<', '>',
      '<=', '>=',
    );

    $inlude_endpoints = !($this->options['include_endpoints'] xor ( $this->options['operator'] === 'within'));
    list($op_left, $op_right) = array_slice($operators, $inlude_endpoints ? 2 : 0, 2);

    if ($this->options['operator'] === 'within') {
      $this->query->addWhere(0, db_and()->condition($field_from, $this->argument, $op_left)->condition($field_to, $this->argument, $op_right));
    }
    else {
      $this->query->addWhere(0, db_or()->condition($field_from, $this->argument, $op_right)->condition($field_to, $this->argument, $op_left));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getSortName() {
    return $this->t('Numerical', array(), array('context' => 'Sort order'));
  }

  /**
   * {@inheritdoc}
   */
  public function adminSummary() {
    $operators = $this->operators();
    $operator = $this->options['operator'];
    return $operators[$operator]['short'];
  }

  /**
   * Define the operators supported for ranges.
   */
  protected function operators() {
    $operators = array(
      'within' => array(
        'short' => $this->t('contains'),
        'method' => 'opWithin',
      ),
      'not within' => array(
        'short' => $this->t('does not contain'),
        'method' => 'opWithin',
      ),
    );

    return $operators;
  }

}
