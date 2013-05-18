<?php

namespace Behat\Behat\Formatter;

use Behat\Behat\Definition\DefinitionInterface,
    Behat\Behat\DataCollector\LoggerDataCollector,
    Behat\Behat\Definition\DefinitionSnippet,
    Behat\Behat\Exception\UndefinedException;

use Behat\Gherkin\Node\AbstractNode,
    Behat\Gherkin\Node\FeatureNode,
    Behat\Gherkin\Node\BackgroundNode,
    Behat\Gherkin\Node\AbstractScenarioNode,
    Behat\Gherkin\Node\OutlineNode,
    Behat\Gherkin\Node\ScenarioNode,
    Behat\Gherkin\Node\StepNode,
    Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

/*
 * This file is part of the Behat.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * HTML formatter.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class ScenarioedFormatter extends HtmlFormatter

{
    /**
     * Deffered footer template part.
     *
     * @var string
     */
    protected $footer;

    /**
     * {@inheritdoc}
     */
    protected function getDefaultParameters()
    {
        return array(
            'template_path' => null
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function printSuiteHeader(LoggerDataCollector $logger)
    {
        $this->parameters->set('decorated', false);

        $template = $this->getHtmlTemplate();
        $header         = mb_substr($template, 0, mb_strpos($template, '{{content}}'));
        $this->footer   = mb_substr($template, mb_strpos($template, '{{content}}') + 11);

        $this->writeln($header);
    }

    /**
     * {@inheritdoc}
     */
    protected function printSuiteFooter(LoggerDataCollector $logger)
    {
        $this->printSummary($logger);
        $this->writeln($this->footer);
    }

    /**
     * {@inheritdoc}
     */
    protected function printFeatureHeader(FeatureNode $feature)
    {
        $this->writeln('<div class="feature">');

        parent::printFeatureHeader($feature);
    }

    /**
     * {@inheritdoc}
     */
    protected function printFeatureOrScenarioTags(AbstractNode $node)
    {
        if (count($tags = $node->getOwnTags())) {
            $this->writeln('<ul class="tags">');
            foreach ($tags as $tag) {
                $this->writeln("<li>@$tag</li>");
            }
            $this->writeln('</ul>');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function printFeatureName(FeatureNode $feature)
    {
        $this->writeln('<h2>');
        $this->writeln('<span class="keyword">' . $feature->getKeyword() . ': </span>');
        $this->writeln('<span class="title">' . $feature->getTitle() . '</span>');
        $this->writeln('</h2>');
    }

    /**
     * {@inheritdoc}
     */
    protected function printFeatureDescription(FeatureNode $feature)
    {
        $lines = explode("\n", $feature->getDescription());

        $this->writeln('<p>');
        foreach ($lines as $line) {
            $this->writeln("<div>" . htmlspecialchars($line) . "</div>");
        }
        $this->writeln('</p>');
    }

    /**
     * {@inheritdoc}
     */
    protected function printFeatureFooter(FeatureNode $feature)
    {
        $this->writeln('</div>');
    }

    /**
     * {@inheritdoc}
     */
    protected function printBackgroundHeader(BackgroundNode $background)
    {
        $this->writeln('<div class="scenario background">');

        $this->printScenarioName($background);
    }

    /**
     * {@inheritdoc}
     */
    protected function printBackgroundFooter(BackgroundNode $background)
    {
        $this->writeln('</ol>');
        $this->writeln('</div>');
    }

    /**
     * {@inheritdoc}
     */
    protected function printScenarioHeader(ScenarioNode $scenario)
    {
        $this->writeln('<div class="scenario">');

        $this->printFeatureOrScenarioTags($scenario);
        $this->printScenarioName($scenario);
    }

    /**
     * {@inheritdoc}
     */
    protected function printScenarioName(AbstractScenarioNode $scenario)
    {
        $this->writeln('<h3>');
        $this->writeln('<span class="keyword">' . $scenario->getKeyword() . ': </span>');
        if ($scenario->getTitle()) {
            $this->writeln('<span class="title">' . $scenario->getTitle() . '</span>');
        }
        $this->printScenarioPath($scenario);
        $this->writeln('</h3>');

        $this->writeln('<ol>');
    }

    /**
     * {@inheritdoc}
     */
    protected function printScenarioFooter(ScenarioNode $scenario)
    {
        $this->writeln('</ol>');
        $this->writeln('</div>');
    }

    /**
     * {@inheritdoc}
     */
    protected function printOutlineHeader(OutlineNode $outline)
    {
        $this->writeln('<div class="scenario outline">');

        $this->printFeatureOrScenarioTags($outline);
        $this->printScenarioName($outline);
    }

    /**
     * {@inheritdoc}
     */
    protected function printOutlineSteps(OutlineNode $outline)
    {
        parent::printOutlineSteps($outline);
        $this->writeln('</ol>');
    }

    /**
     * {@inheritdoc}
     */
    protected function printOutlineExamplesSectionHeader(TableNode $examples)
    {
        $this->writeln('<div class="examples">');

        if (!$this->getParameter('expand')) {
            $this->writeln('<h4>' . $examples->getKeyword() . '</h4>');
            $this->writeln('<table>');
            $this->writeln('<thead>');
            $this->printColorizedTableRow($examples->getRow(0), 'skipped');
            $this->writeln('</thead>');
            $this->writeln('<tbody>');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function printOutlineExampleResult(TableNode $examples, $iteration, $result, $isSkipped)
    {
        if (!$this->getParameter('expand')) {
            $color  = $this->getResultColorCode($result);

            $this->printColorizedTableRow($examples->getRow($iteration + 1), $color);
            $this->printOutlineExampleResultExceptions($examples, $this->delayedStepEvents);
        } else {
            $this->write('<h4>' . $examples->getKeyword() . ': ');
            foreach ($examples->getRow($iteration + 1) as $value) {
                $this->write('<span>' . $value . '</span>');
            }
            $this->writeln('</h4>');

            foreach ($this->delayedStepEvents as $event) {
                $this->writeln('<ol>');
                $this->printStep(
                    $event->getStep(),
                    $event->getResult(),
                    $event->getDefinition(),
                    $event->getSnippet(),
                    $event->getException()
                );
                $this->writeln('</ol>');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function printOutlineExampleResultExceptions(TableNode $examples, array $events)
    {
        $colCount = count($examples->getRow(0));

        foreach ($events as $event) {
            $exception = $event->getException();
            if ($exception && !$exception instanceof UndefinedException) {
                $error = $this->exceptionToString($exception);
                $error = $this->relativizePathsInString($error);

                $this->writeln('<tr class="failed exception">');
                $this->writeln('<td colspan="' . $colCount . '">');
                $this->writeln('<pre class="backtrace">' . htmlspecialchars($error) . '</pre>');
                $this->writeln('</td>');
                $this->writeln('</tr>');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function printOutlineFooter(OutlineNode $outline)
    {
        if (!$this->getParameter('expand')) {
            $this->writeln('</tbody>');
            $this->writeln('</table>');
        }
        $this->writeln('</div>');
        $this->writeln('</div>');
    }

    /**
     * {@inheritdoc}
     */
    protected function printStep(StepNode $step, $result, DefinitionInterface $definition = null,
                                 $snippet = null, \Exception $exception = null)
    {
        $this->writeln('<li class="' . $this->getResultColorCode($result) . '">');

        parent::printStep($step, $result, $definition, $snippet, $exception);

        $this->writeln('</li>');
    }

    /**
     * {@inheritdoc}
     */
    protected function printStepBlock(StepNode $step, DefinitionInterface $definition = null, $color)
    {
        $this->writeln('<div class="step">');

        $this->printStepName($step, $definition, $color);
        if (null !== $definition) {
            $this->printStepDefinitionPath($step, $definition);
        }

        $this->writeln('</div>');
    }

    /**
     * {@inheritdoc}
     */
    protected function printStepName(StepNode $step, DefinitionInterface $definition = null, $color)
    {
        $type   = $step->getType();
        $text   = $this->inOutlineSteps ? $step->getCleanText() : $step->getText();

        if (null !== $definition) {
            $text = $this->colorizeDefinitionArguments($text, $definition, $color);
        }

        $this->writeln('<span class="keyword">' . $type . ' </span>');
        $this->writeln('<span class="text">' . $text . '</span>');
    }

    /**
     * {@inheritdoc}
     */
    protected function printStepDefinitionPath(StepNode $step, DefinitionInterface $definition)
    {
        if ($this->getParameter('paths')) {
            if ($this->hasParameter('paths_base_url')) {
                $this->printPathLink($definition);
            } else {
                $this->printPathComment($this->relativizePathsInString($definition->getPath()));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function printStepPyStringArgument(PyStringNode $pystring, $color = null)
    {
        $this->writeln('<pre class="argument">' . htmlspecialchars((string) $pystring) . '</pre>');
    }

    /**
     * {@inheritdoc}
     */
    protected function printStepTableArgument(TableNode $table, $color = null)
    {
        $this->writeln('<table class="argument">');

        $this->writeln('<thead>');
        $headers = $table->getRow(0);
        $this->printColorizedTableRow($headers, 'row');
        $this->writeln('</thead>');

        $this->writeln('<tbody>');
        foreach ($table->getHash() as $row) {
            $this->printColorizedTableRow($row, 'row');
        }
        $this->writeln('</tbody>');

        $this->writeln('</table>');
    }

    /**
     * {@inheritdoc}
     */
    protected function printStepException(\Exception $exception, $color)
    {
        $error = $this->exceptionToString($exception);
        $error = $this->relativizePathsInString($error);

        $this->writeln('<pre class="backtrace">' . htmlspecialchars($error) . '</pre>');
    }

    /**
     * {@inheritdoc}
     */
    protected function printStepSnippet(DefinitionSnippet $snippet)
    {
        $this->writeln('<div class="snippet"><pre>' . htmlspecialchars($snippet) . '</pre></div>');
    }

    /**
     * {@inheritdoc}
     */
    protected function colorizeDefinitionArguments($text, DefinitionInterface $definition, $color)
    {
        $regex      = $definition->getRegex();
        $paramColor = $color . '_param';

        // If it's just a string - skip
        if ('/' !== substr($regex, 0, 1)) {
            return $text;
        }

        // Find arguments with offsets
        $matches = array();
        preg_match($regex, $text, $matches, PREG_OFFSET_CAPTURE);
        array_shift($matches);

        // Replace arguments with colorized ones
        $shift = 0;
        $lastReplacementPosition = 0;
        foreach ($matches as $key => $match) {
            if (!is_numeric($key) || -1 === $match[1] || false !== strpos($match[0], '<')) {
                continue;
            }

            $offset = $match[1] + $shift;
            $value  = $match[0];

            // Skip inner matches
            if ($lastReplacementPosition > $offset) {
                continue;
            }
            $lastReplacementPosition = $offset + strlen($value);

            $begin  = substr($text, 0, $offset);
            $end    = substr($text, $offset + strlen($value));
            $format = "{+strong class=\"$paramColor\"-}%s{+/strong-}";
            $text   = sprintf('%s'.$format.'%s', $begin, $value, $end);

            // Keep track of how many extra characters are added
            $shift += strlen($format) - 2;
            $lastReplacementPosition += strlen($format) - 2;
        }

        // Replace "<", ">" with colorized ones
        $text = preg_replace('/(<[^>]+>)/', "{+strong class=\"$paramColor\"-}\$1{+/strong-}", $text);
        $text = htmlspecialchars($text, ENT_NOQUOTES);
        $text = strtr($text, array('{+' => '<', '-}' => '>'));

        return $text;
    }

    /**
     * {@inheritdoc}
     */
    protected function printColorizedTableRow($row, $color)
    {
        $this->writeln('<tr class="' . $color . '">');

        foreach ($row as $column) {
            $this->writeln('<td>' . $column . '</td>');
        }

        $this->writeln('</tr>');
    }

    /**
     * Prints path link, which links to the source containing the step definition.
     *
     * @param DefinitionInterface $definition
     */
    protected function printPathLink(DefinitionInterface $definition)
    {
        $url = $this->getParameter('paths_base_url')
            . $this->relativizePathsInString($definition->getCallbackReflection()->getFileName());
        $path = $this->relativizePathsInString($definition->getPath());
        $this->writeln('<span class="path"><a href="' . $url . '">' . $path . '</a></span>');
    }

    /**
     * {@inheritdoc}
     */
    protected function printPathComment($path, $indentCount = 0)
    {
        $this->writeln('<span class="path">' . $path . '</span>');
    }

    /**
     * {@inheritdoc}
     */
    protected function printSummary(LoggerDataCollector $logger)
    {
        $results = $logger->getScenariosStatuses();
        $result = $results['failed'] > 0 ? 'failed' : 'passed';
        $this->writeln('<div class="summary '.$result.'">');

        $this->writeln('<div class="counters">');
        parent::printSummary($logger);
        $this->writeln('</div>');

        $this->writeln('</div>');
    }

    /**
     * {@inheritdoc}
     */
    protected function printScenariosSummary(LoggerDataCollector $logger)
    {
        $this->writeln('<p class="scenarios">');
        parent::printScenariosSummary($logger);
        $this->writeln('</p>');
    }

    /**
     * {@inheritdoc}
     */
    protected function printStepsSummary(LoggerDataCollector $logger)
    {
        $this->writeln('<p class="steps">');
        parent::printStepsSummary($logger);
        $this->writeln('</p>');
    }

    /**
     * {@inheritdoc}
     */
    protected function printTimeSummary(LoggerDataCollector $logger)
    {
        $this->writeln('<p class="time">');
        parent::printTimeSummary($logger);
        $this->writeln('</p>');
    }

    /**
     * {@inheritdoc}
     */
    protected function printStatusesSummary(array $statusesStatistics)
    {
        $statuses = array();
        $statusTpl = '<strong class="%s">%s</strong>';
        foreach ($statusesStatistics as $status => $count) {
            if ($count) {
                $transStatus = $this->translateChoice(
                    "{$status}_count", $count, array('%1%' => $count)
                );
                $statuses[] = sprintf($statusTpl, $status, $transStatus);
            }
        }
        if (count($statuses)) {
            $this->writeln(' ('.implode(', ', $statuses).')');
        }
    }

    /**
     * Get HTML template.
     *
     * @return string
     */
    protected function getHtmlTemplate()
    {
        $templatePath = $this->parameters->get('template_path')
                     ?: $this->parameters->get('support_path') . DIRECTORY_SEPARATOR . 'html.tpl';

        if (file_exists($templatePath)) {
            return file_get_contents($templatePath);
        }

        return
'   <div id="behat">
        {{content}}
    </div>';
    }
  }

