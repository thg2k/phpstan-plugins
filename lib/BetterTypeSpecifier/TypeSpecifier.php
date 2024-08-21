<?php declare(strict_types=1);
/**
 * ThGnet plugins for PHPStan - Better type specifier
 */

namespace thgnet\PHPStan\BetterTypeSpecifier;

use PHPStan\Analyser\TypeSpecifier as BaseTypeSpecifier;
use PHPStan\Analyser\TypeSpecifierContext;
use PHPStan\Analyser\SpecifiedTypes;
use PHPStan\Analyser\Scope;
use PHPStan\Node\Expr\AlwaysRememberedExpr;
use PHPStan\Type;
use PhpParser\Node;

/**
 * ...
 */
// @phpstan-ignore-next-line
class TypeSpecifier
    extends BaseTypeSpecifier
{
  /**
   * @inheritdoc
   */
  public function specifyTypesInCondition(Scope $scope, Node\Expr $expr,
      TypeSpecifierContext $context,
      ?Node\Expr $rootExpr = null): SpecifiedTypes
  {
    if ($expr instanceof Node\Expr\BinaryOp\Equal) {
      /* gather constant expressions */
      $leftType = $scope->getType($expr->left);
      $rightType = $scope->getType($expr->right);

      $rightExpr = $expr->right;
      if ($rightExpr instanceof AlwaysRememberedExpr) {
        // @phpstan-ignore-next-line (missing @api)
        $rightExpr = $rightExpr->getExpr();
      }

      $leftExpr = $expr->left;
      if ($leftExpr instanceof AlwaysRememberedExpr) {
        // @phpstan-ignore-next-line (missing @api)
        $leftExpr = $leftExpr->getExpr();
      }

      $exprNode = null;
      $constantType = null;

      if (($rightType instanceof Type\ConstantScalarType) &&
          (!$leftExpr instanceof Node\Expr\ConstFetch) &&
          (!$leftExpr instanceof Node\Expr\ClassConstFetch)) {
        $exprNode = $expr->left;
        $constantType = $rightType;
      }

      if (($leftType instanceof Type\ConstantScalarType) &&
          (!$rightExpr instanceof Node\Expr\ConstFetch) &&
          (!$rightExpr instanceof Node\Expr\ClassConstFetch)) {
        $exprNode = $expr->right;
        $constantType = $leftType;
      }

      if ($exprNode && $constantType) {
        if (!$context->null() && $constantType->getValue() === null) {
          $trueTypes = array(
            new Type\NullType(),
            new Type\Constant\ConstantBooleanType(false),
            new Type\Constant\ConstantIntegerType(0),
            new Type\Constant\ConstantFloatType(0.0),
            new Type\Constant\ConstantStringType(''),
            new Type\Constant\ConstantArrayType([], []),
          );
          return $this->create($exprNode, new Type\UnionType($trueTypes), $context, false, $scope, $rootExpr);
        }

        if (!$context->null() && $constantType->getValue() === '') {
          /* There is a difference between php 7.x and 8.x on the equality
           * behavior between zero and the empty string, so to be conservative
           * we leave it untouched regardless of the language version */
          if ($context->true()) {
            $trueTypes = array(
              new Type\NullType(),
              new Type\Constant\ConstantBooleanType(false),
              new Type\Constant\ConstantIntegerType(0),
              new Type\Constant\ConstantFloatType(0.0),
              new Type\Constant\ConstantStringType(''),
            );
          }
          else {
            $trueTypes = array(
              new Type\NullType(),
              new Type\Constant\ConstantBooleanType(false),
              new Type\Constant\ConstantStringType(''),
            );
          }
          return $this->create($exprNode, new Type\UnionType($trueTypes), $context, false, $scope, $rootExpr);
        }
      }
    }

    return parent::specifyTypesInCondition($scope, $expr, $context,
        $rootExpr);
  }
}
