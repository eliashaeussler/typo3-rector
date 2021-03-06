<?php

declare(strict_types=1);

namespace Ssch\TYPO3Rector\Rector\v11\v0;

use PhpParser\Builder\Method;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use Rector\Core\Exception\ShouldNotHappenException;
use Rector\Defluent\Rector\AbstractFluentChainMethodCallRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * @see https://docs.typo3.org/c/typo3/cms-core/master/en-us/Changelog/11.0/Breaking-93041-RemoveTypoScriptOptionAddQueryStringmethod.html
 */
final class RemoveAddQueryStringMethodRector extends AbstractFluentChainMethodCallRector
{
    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /**
     * @param MethodCall $node
     */
    public function refactor(Node $node): ?Node
    {
        if ($this->shouldSkip($node)) {
            return null;
        }

        if ($this->isMethodCallOnContentObjectRenderer($node)) {
            $this->refactorGetQueryArgumentsMethodCall($node);
            return null;
        }

        return $this->refactorSetAddQueryStringMethodCall($node);
    }

    /**
     * @codeCoverageIgnore
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Remove TypoScript option addQueryString.method', [
            new CodeSample(<<<'CODE_SAMPLE'
$this->uriBuilder->setUseCacheHash(true)
                         ->setCreateAbsoluteUri(true)
                         ->setAddQueryString(true)
                         ->setAddQueryStringMethod('GET')
                         ->build();
CODE_SAMPLE
                , <<<'CODE_SAMPLE'
$this->uriBuilder->setUseCacheHash(true)
                         ->setCreateAbsoluteUri(true)
                         ->setAddQueryString(true)
                         ->build();
CODE_SAMPLE
            ),
        ]);
    }

    private function shouldSkip(MethodCall $node): bool
    {
        if ($this->isMethodCallOnUriBuilder($node)) {
            return false;
        }
        return ! $this->isMethodCallOnContentObjectRenderer($node);
    }

    private function isMethodCallOnUriBuilder(MethodCall $node): bool
    {
        if (! $this->nodeTypeResolver->isMethodStaticCallOrClassMethodObjectType($node, UriBuilder::class)) {
            return false;
        }
        return $this->isName($node->name, 'setAddQueryStringMethod');
    }

    private function isMethodCallOnContentObjectRenderer(MethodCall $node): bool
    {
        if (! $this->nodeTypeResolver->isMethodStaticCallOrClassMethodObjectType($node, ContentObjectRenderer::class)) {
            return false;
        }
        return $this->isName($node->name, 'getQueryArguments');
    }

    private function refactorSetAddQueryStringMethodCall(MethodCall $node): ?Node
    {
        try {
            // If it is the only method call, we can safely delete the node here.
            $this->removeNode($node);

            return $node;
        } catch (ShouldNotHappenException $shouldNotHappenException) {
            $chainMethodCalls = $this->fluentChainMethodCallNodeAnalyzer->collectAllMethodCallsInChain($node);

            if (! $this->sameClassMethodCallAnalyzer->haveSingleClass($chainMethodCalls)) {
                return null;
            }

            foreach ($chainMethodCalls as $chainMethodCall) {
                if ($this->isName($node->name, 'setAddQueryStringMethod')) {
                    continue;
                }

                $node->var = new MethodCall($chainMethodCall->var, $chainMethodCall->name, $chainMethodCall->args);
            }

            return $node->var;
        }
    }

    private function refactorGetQueryArgumentsMethodCall(MethodCall $node): void
    {
        unset($node->args[1], $node->args[2]);
    }
}
