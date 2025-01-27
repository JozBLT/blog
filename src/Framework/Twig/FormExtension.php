<?php

namespace Framework\Twig;

use DateTime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FormExtension extends AbstractExtension
{

    /** @return TwigFunction[] */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('field', [$this, 'field'], [
                'is_safe' => ['html'],
                'needs_context' => true
            ])
        ];
    }

    /** Generates the HTML code of a field */
    public function field(array $context, string $key, mixed $value, ?string $label = null, array $options = []): string
    {
        $type = $options['type'] ?? 'text';
        $error = $this->getErrorHtml($context, $key);
        $class = 'form-group ' . ($options['wrapper_class'] ?? '');
        $value = $this->convertValue($value);
        $attributes = [
            'class' => trim('form-control ' . ($options['class'] ?? '')),
            'name'  => $key,
            'id'    => $key
        ];

        if ($error) {
            $attributes['class'] .= ' is-invalid';
        }

        if (!empty($options['readonly'])) {
            $attributes['readonly'] = 'readonly';
        }

        if ($type === 'textarea') {
            $input = $this->textarea($value, $attributes);
        } elseif ($type === 'file') {
            $input = $this->file($attributes);
        } elseif ($type === 'checkbox') {
            $input = $this->checkbox($value, $attributes);
        } elseif (array_key_exists('options', $options)) {
            $input = $this->select($value, $options['options'], $attributes);
        } else {
            $attributes['type'] = $options['type'] ?? 'text';
            $input = $this->input($value, $attributes);
        }

        return "
        <div class=\"" . $class . "\">
            <label for=\"" . $key . "\">$label</label>
            {$input}
            {$error}
        </div>
        ";
    }

    private function convertValue(DateTime|string|null $value): string
    {
        if ($value instanceof DateTime) {
            return $value->format('Y-m-d H:i:s');
        }

        return (string)$value;
    }

    private function getErrorHTML($context, $key): string
    {
        $error = $context['errors'][$key] ?? false;

        if ($error) {
            return "<div class=\"invalid-feedback\">$error</div>";
        }

        return "";
    }

    /** Generates a <input> */
    private function input(?string $value, array $attributes): string
    {
        return "<input " . $this->getHtmlFromArray($attributes) . " value=\"$value\">";
    }

    /** Generates a <input type="checkbox"> */
    private function checkbox(?string $value, array $attributes): string
    {
        $html = '<input type="hidden" name="' . $attributes['name'] . '" value="0"/>';

        if ($value) {
            $attributes['checked'] = true;
        }

        $attributes['class'] = 'form-check-input';

        return '
            <div class="form-check">
                ' . $html . '<input type="checkbox" ' . $this->getHtmlFromArray($attributes) . ' value="1">
                <label class="form-check-label" for"' . $attributes['id'] . '">' . $attributes['name'] . '</label>
            </div>';
    }

    private function file(array $attributes): string
    {
        return "<input type=\"file\" " . $this->getHtmlFromArray($attributes) . ">";
    }

    /** Generates a <textarea> */
    private function textarea(?string $value, array $attributes): string
    {
        return "<textarea rows='8' " . $this->getHtmlFromArray($attributes) . ">$value</textarea>";
    }

    /** Generates a <select> */
    private function select(?string $value, array $options, array $attributes): string
    {
        $htmlOptions = array_reduce(array_keys($options), function (string $html, string $key) use ($options, $value) {
            $params = ['value' => $key, 'selected' => $key === $value];
            return $html . '<option ' . $this->getHtmlFromArray($params) . '>' . $options[$key] . '</option>';
        }, "");

        return "<select " . $this->getHtmlFromArray($attributes) . ">$htmlOptions</select>";
    }

    /** Transforms a key/value array in html attribute */
    private function getHtmlFromArray(array $attributes): string
    {
        $htmlParts = [];

        foreach ($attributes as $key => $value) {
            if ($value === true) {
                $htmlParts[] = (string)$key;
            } elseif ($value !== false) {
                $htmlParts[] = "$key=\"$value\"";
            }
        }

        return implode(' ', $htmlParts);
    }
}
