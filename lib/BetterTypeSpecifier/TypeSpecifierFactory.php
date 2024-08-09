<?php declare(strict_types=1);
/**
 * ThGnet plugins for PHPStan - Better type specifier
 */

namespace thgnet\PHPStan\BetterTypeSpecifier;

use PHPStan\Analyser\TypeSpecifierFactory as BaseTypeSpecifierFactory;
use PHPStan\Analyser\TypeSpecifier as BaseTypeSpecifier;
use PHPStan\Analyser\TypeSpecifierAwareExtension;
use PHPStan\Broker\BrokerFactory;
use PHPStan\DependencyInjection\Container;
use PHPStan\Node\Printer\ExprPrinter;
use PHPStan\Reflection\ReflectionProvider;

/**
 * ...
 */
// @phpstan-ignore-next-line
class TypeSpecifierFactory
    extends BaseTypeSpecifierFactory
{
  /**
   * ...
   *
   * @var Container
   */
  protected $xcontainer;

  /**
   * ...
   *
   * @param Container $container ...
   */
  public function __construct(Container $container)
  {
    $this->xcontainer = $container;
  }

  /**
   * @inheritdoc
   */
  public function create(): BaseTypeSpecifier
  {
    $typeSpecifier = new TypeSpecifier(
        $this->xcontainer->getByType(ExprPrinter::class),
        $this->xcontainer->getByType(ReflectionProvider::class),
        // @phpstan-ignore-next-line
        $this->xcontainer->getServicesByTag(self::FUNCTION_TYPE_SPECIFYING_EXTENSION_TAG),
        // @phpstan-ignore-next-line
        $this->xcontainer->getServicesByTag(self::METHOD_TYPE_SPECIFYING_EXTENSION_TAG),
        // @phpstan-ignore-next-line
        $this->xcontainer->getServicesByTag(self::STATIC_METHOD_TYPE_SPECIFYING_EXTENSION_TAG),
        // @phpstan-ignore-next-line
        $this->xcontainer->getParameter('rememberPossiblyImpureFunctionValues'));

    $extensions = array_merge(
        $this->xcontainer->getServicesByTag(BrokerFactory::PROPERTIES_CLASS_REFLECTION_EXTENSION_TAG),
        $this->xcontainer->getServicesByTag(BrokerFactory::METHODS_CLASS_REFLECTION_EXTENSION_TAG),
        $this->xcontainer->getServicesByTag(BrokerFactory::DYNAMIC_METHOD_RETURN_TYPE_EXTENSION_TAG),
        $this->xcontainer->getServicesByTag(BrokerFactory::DYNAMIC_STATIC_METHOD_RETURN_TYPE_EXTENSION_TAG),
        $this->xcontainer->getServicesByTag(BrokerFactory::DYNAMIC_FUNCTION_RETURN_TYPE_EXTENSION_TAG));

    foreach ($extensions as $extension) {
      if (!$extension instanceof TypeSpecifierAwareExtension)
        continue;

      $extension->setTypeSpecifier($typeSpecifier);
    }

    return $typeSpecifier;
  }
}
