<?php

namespace Framework\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FormExtension extends AbstractExtension
{

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('field', [$this, 'field'], [
                'is_safe' => ['html'],
                'needs_context' => true
            ])
        ];
    }

//    public function field(
//        array $context,
//        string $key,
//        mixed $value,
//        ?string $label = null,
//        array $options = []
//    ): string {
//        $type = $options['type'] ?? 'text';
//        $error = $this->getErrorHTML($context, $key);
//        $class = 'form-group';
//        $value = $this->convertValue($value);
//        $attributes = [
//            'class' => trim('form-control ' . ($options['class'] ?? '')),
//            'name' => $key,
//            'id' => $key
//        ];
//        if ($error) {
//            $class .= ' has-danger';
//            $attributes['class'] .= ' form-control-danger';
//        }
//        if ($type === 'textarea') {
//            $input = $this->textarea($value, $attributes);
//        } else {
//            $input = $this->input($value, $attributes);
//        }
//        return "
//            <div class=\"" . $class . "\">
//                <label for=\"name\">{$label}</label>
//                {$input}
//                {$error}
//            </div>
//            ";
//    }

    public function field(array $context, string $key, mixed $value, ?string $label = null, array $options = []): string
    {
        $type = $options['type'] ?? 'text';
        $error = $this->getErrorHtml($context, $key);
        $class = 'form-group';
        $value = $this->convertValue($value);
        $attributes = [
            'class' => trim('form-control ' . ($options['class'] ?? '')),
            'name'  => $key,
            'id'    => $key
        ];
        if ($error) {
            $attributes['class'] .= ' is-invalid';
        } /*else {
            $attributes['class'] .= 'is-valid';
        }*/
        if ($type === 'textarea') {
            $input = $this->textarea($value, $attributes);
        } elseif (array_key_exists('options', $options)) {
            $input = $this->select($value, $options['options'], $attributes);
        } else {
            $input = $this->input($value, $attributes);
        }
        return "
        <div class=\"" . $class . "\">
            <label for=\"name\">$label</label>
            {$input}
            {$error}
        </div>
        ";
    }

    private function convertValue($value): string
    {
        if ($value instanceof \DateTime) {
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

    /**
     * Generates a <input>
     */
    private function input(?string $value, array $attributes): string
    {
        return "<input type=\"text\" " . $this->getHtmlFromArray($attributes) . " value=\"$value\">";
    }

    /**
     * Generates a <textarea>
     */
    private function textarea(?string $value, array $attributes): string
    {
        return "<textarea " . $this->getHtmlFromArray($attributes) . ">$value</textarea>";
    }

    /**
     * Generates a <select>
     */
    private function select(?string $value, array $options, array $attributes): string
    {
        $htmlOptions = array_reduce(array_keys($options), function (string $html, string $key) use ($options, $value) {
            $params = ['value' => $key, 'selected' => $key === $value];
            return $html . '<option ' . $this->getHtmlFromArray($params) . '>' . $options[$key] . '</option>';
        }, "");
        return "<select " . $this->getHtmlFromArray($attributes) . ">$htmlOptions</select>";
    }

    /**
     * Transform a key/value array in html attribute
     */
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
