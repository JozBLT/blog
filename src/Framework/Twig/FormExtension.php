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

//    public function field(array $context, string $key, mixed $value, ?string $label = null, array $options = []): string
//    {
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

    private function input(?string $value, array $attributes): string
    {
        return "<input type=\"text\" " . $this->getHtmlFromArray($attributes) . " value=\"$value\">";
    }

    private function textarea(?string $value, array $attributes): string
    {
        return "<textarea " . $this->getHtmlFromArray($attributes) . ">$value</textarea>";
    }

    private function getHtmlFromArray(array $attributes): string
    {
        return implode(' ', array_map(function ($key, $value) {
            return "$key=\"$value\"";
        }, array_keys($attributes), $attributes));
    }
}
