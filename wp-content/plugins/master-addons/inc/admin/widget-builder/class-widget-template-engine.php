<?php
/**
 * Twig-subset template engine for Widget Builder templates.
 *
 * Extracted from Dynamic_Widget so the widget runtime and the editor preview
 * render identically. Previously the preview only substituted {{ placeholders }}
 * and printed {% if %} / {% for %} tags as literal text.
 *
 * Control types drive per-value escaping and are injected rather than read off
 * an Elementor widget, so the engine works in an AJAX context too.
 *
 * @package MasterAddons
 * @subpackage WidgetBuilder
 */

namespace MasterAddons\Inc\Admin\WidgetBuilder;

if (!defined('ABSPATH')) {
    exit;
}

class Widget_Template_Engine {

    /** @var array control name => control type, for output escaping. */
    private $control_types = [];

    /**
     * @param array $control_types Map of control name => type (text, url, wysiwyg, ...).
     */
    public function __construct(array $control_types = []) {
        $this->control_types = array_change_key_case($control_types, CASE_LOWER);
    }

    /** Control type for a template variable; defaults to text (esc_html). */
    private function control_type($name) {
        $key = strtolower(trim((string) $name));
        return isset($this->control_types[$key]) ? $this->control_types[$key] : 'text';
    }

    /* ------------------------------------------------------------------ *
     * Twig-syntax template engine (safe subset; no eval, no compiled PHP).
     * Supports:  {{ var }}  {{ var.prop }}  {{ var|raw }}  {{ var|upper }}
     *            {% if expr %} {% elseif expr %} {% else %} {% endif %}
     *            {% for item in list %} ... {% endfor %}
     * Conditions:  ==  !=  >  <  >=  <=   and  or  not   plus bare truthiness.
     * All output is escaped per control type unless the |raw filter is used.
     * ------------------------------------------------------------------ */

    /** Render a template string against the variable context. */
    public function render($template, $context) {
        $template = (string) $template;
        if ('' === $template) {
            return '';
        }
        $tokens = $this->tokenize_template($template);
        $pos    = 0;
        $ast    = $this->parse_template($tokens, $pos, []);
        return $this->eval_nodes($ast, $context);
    }

    /** Split a template into text / {{ output }} / {% tag %} tokens. */
    private function tokenize_template($template) {
        $parts  = preg_split('/(\{%.*?%\}|\{\{.*?\}\})/s', $template, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $tokens = [];
        foreach ($parts as $part) {
            if (preg_match('/^\{%\s*(.*?)\s*%\}$/s', $part, $m)) {
                $inner   = trim($m[1]);
                $space   = strpos($inner, ' ');
                $keyword = (false === $space) ? $inner : substr($inner, 0, $space);
                $expr    = (false === $space) ? '' : trim(substr($inner, $space + 1));
                $tokens[] = ['type' => 'tag', 'kw' => $keyword, 'expr' => $expr];
            } elseif (preg_match('/^\{\{\s*(.*?)\s*\}\}$/s', $part, $m)) {
                $tokens[] = ['type' => 'out', 'expr' => trim($m[1])];
            } else {
                $tokens[] = ['type' => 'text', 'value' => $part];
            }
        }
        return $tokens;
    }

    /** Recursive-descent parse into an AST. Stops (without consuming) on a $stops keyword. */
    private function parse_template($tokens, &$pos, $stops) {
        $nodes = [];
        $count = count($tokens);
        while ($pos < $count) {
            $tok = $tokens[$pos];
            if ('text' === $tok['type']) {
                $nodes[] = ['text', $tok['value']];
                $pos++;
                continue;
            }
            if ('out' === $tok['type']) {
                $nodes[] = ['out', $tok['expr']];
                $pos++;
                continue;
            }
            // tag
            $kw = $tok['kw'];
            if (in_array($kw, $stops, true)) {
                return $nodes; // leave $pos on the stop tag for the caller
            }
            if ('if' === $kw) {
                $pos++;
                $branches = [];
                $cond     = $tok['expr'];
                while (true) {
                    $body       = $this->parse_template($tokens, $pos, ['elseif', 'else', 'endif']);
                    $branches[] = [$cond, $body];
                    if ($pos >= $count) {
                        break;
                    }
                    $next = $tokens[$pos];
                    if ('endif' === $next['kw']) {
                        $pos++;
                        break;
                    }
                    if ('elseif' === $next['kw']) {
                        $cond = $next['expr'];
                        $pos++;
                        continue;
                    }
                    if ('else' === $next['kw']) {
                        $cond = '__else__';
                        $pos++;
                        continue;
                    }
                    break;
                }
                $nodes[] = ['if', $branches];
                continue;
            }
            if ('for' === $kw) {
                $pos++;
                $body = $this->parse_template($tokens, $pos, ['endfor']);
                if ($pos < $count && 'endfor' === $tokens[$pos]['kw']) {
                    $pos++;
                }
                $nodes[] = ['for', $tok['expr'], $body];
                continue;
            }
            // stray close/else with no opener -> skip
            $pos++;
        }
        return $nodes;
    }

    /** Evaluate an AST node list to a string. */
    private function eval_nodes($nodes, $context) {
        $out = '';
        foreach ($nodes as $node) {
            switch ($node[0]) {
                case 'text':
                    $out .= $node[1];
                    break;
                case 'out':
                    $out .= $this->render_output($node[1], $context);
                    break;
                case 'if':
                    foreach ($node[1] as $branch) {
                        if ('__else__' === $branch[0] || $this->eval_condition($branch[0], $context)) {
                            $out .= $this->eval_nodes($branch[1], $context);
                            break;
                        }
                    }
                    break;
                case 'for':
                    if (preg_match('/^(\w+)\s+in\s+(.+)$/s', trim($node[1]), $m)) {
                        $list = $this->resolve_value(trim($m[2]), $context);
                        if (is_array($list)) {
                            foreach ($list as $row) {
                                $scope        = $context;
                                $scope[$m[1]] = $row;
                                $out         .= $this->eval_nodes($node[2], $scope);
                            }
                        }
                    }
                    break;
            }
        }
        return $out;
    }

    /** Resolve an expression to its raw value: literal, number, bool, or dotted var path. */
    private function resolve_value($expr, $context) {
        $expr = trim($expr);
        if ('' === $expr) {
            return null;
        }
        $first = $expr[0];
        $last  = substr($expr, -1);
        if (('"' === $first && '"' === $last) || ("'" === $first && "'" === $last)) {
            return substr($expr, 1, -1);
        }
        if (is_numeric($expr)) {
            return $expr + 0;
        }
        if ('true' === $expr) {
            return true;
        }
        if ('false' === $expr) {
            return false;
        }
        if ('null' === $expr) {
            return null;
        }
        $value = $context;
        foreach (explode('.', $expr) as $part) {
            if (is_array($value) && array_key_exists($part, $value)) {
                $value = $value[$part];
            } else {
                return null;
            }
        }
        return $value;
    }

    /** Evaluate a boolean condition (or / and / not / comparison / truthiness). */
    private function eval_condition($expr, $context) {
        $expr = trim($expr);
        if ('__else__' === $expr || 'true' === $expr) {
            return true;
        }
        if ('' === $expr || 'false' === $expr) {
            return false;
        }
        // or (lowest precedence)
        $parts = preg_split('/\s+or\s+/', $expr);
        if (count($parts) > 1) {
            foreach ($parts as $part) {
                if ($this->eval_condition($part, $context)) {
                    return true;
                }
            }
            return false;
        }
        // and
        $parts = preg_split('/\s+and\s+/', $expr);
        if (count($parts) > 1) {
            foreach ($parts as $part) {
                if (!$this->eval_condition($part, $context)) {
                    return false;
                }
            }
            return true;
        }
        // not
        if (preg_match('/^not\s+(.+)$/s', $expr, $m)) {
            return !$this->eval_condition($m[1], $context);
        }
        // comparison (longest operators tried first via alternation order)
        if (preg_match('/^(.+?)\s*(==|!=|>=|<=|>|<)\s*(.+)$/s', $expr, $m)) {
            return $this->compare(
                $this->resolve_value($m[1], $context),
                $this->resolve_value($m[3], $context),
                $m[2]
            );
        }
        // bare truthiness
        return $this->truthy($this->resolve_value($expr, $context));
    }

    /** Compare two resolved values; numeric when both numeric, else string. */
    private function compare($a, $b, $op) {
        if (is_numeric($a) && is_numeric($b)) {
            $a += 0;
            $b += 0;
        } else {
            $a = (string) $a;
            $b = (string) $b;
        }
        switch ($op) {
            case '==':
                return $a == $b; // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison -- template equality is intentionally loose
            case '!=':
                return $a != $b; // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison -- template inequality is intentionally loose
            case '>':
                return $a > $b;
            case '<':
                return $a < $b;
            case '>=':
                return $a >= $b;
            case '<=':
                return $a <= $b;
        }
        return false;
    }

    /** Twig/Handlebars truthiness: '', '0', 0, null, false, [] are falsy. */
    private function truthy($value) {
        if (null === $value || false === $value) {
            return false;
        }
        if (is_array($value)) {
            return !empty($value);
        }
        $string = (string) $value;
        return '' !== $string && '0' !== $string;
    }

    /** Render a {{ output }} expression: resolve, apply filters, escape per type. */
    private function render_output($expr, $context) {
        $segments = array_map('trim', explode('|', trim($expr)));
        $base     = array_shift($segments);
        $value    = $this->resolve_value($base, $context);

        if (is_array($value)) {
            $value = isset($value['url']) ? $value['url'] : '';
        }
        $value = (string) $value;

        $raw = false;
        foreach ($segments as $filter) {
            switch ($filter) {
                case 'raw':
                    $raw = true;
                    break;
                case 'e':
                case 'escape':
                    $raw = false;
                    break;
                case 'upper':
                    $value = strtoupper($value);
                    break;
                case 'lower':
                    $value = strtolower($value);
                    break;
                case 'trim':
                    $value = trim($value);
                    break;
            }
        }
        if ($raw) {
            return $value;
        }
        $type = strtolower($this->control_type($base));
        return $this->escape_value($value, $type);
    }

    private function escape_value($value, $type) {
        switch ($type) {
            case 'wysiwyg':
            case 'code':
                return wp_kses_post($value);
            case 'url':
                return esc_url($value);
            default:
                return esc_html($value);
        }
    }
}
