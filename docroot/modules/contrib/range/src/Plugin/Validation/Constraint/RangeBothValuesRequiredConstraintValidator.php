<?php

namespace Drupal\range\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the RangeBothValuesRequired constraint.
 */
class RangeBothValuesRequiredConstraintValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate($value, Constraint $constraint) {
    if (isset($value)) {
      if ($value->from === '' || $value->to === '') {
        $this->context->addViolation($constraint->message);
      }
    }
  }

}
