<?php

namespace Modules\Shop\Support;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Modules\Shop\Models\Product;

/**
 * A product's order form: fields the admin defines on the product (stored in products.custom_fields)
 * and the customer fills in on the product page. Answers travel with the cart line and are saved
 * on the order line (order_products.attribute_description) next to the category attributes.
 *
 * Field: {key, label, type, required, options[], placeholder}
 */
class ProductOptions
{
    public const TYPES = [
        'select'   => 'Dropdown',
        'radio'    => 'Choice buttons',
        'color'    => 'Colour swatches',
        'checkbox' => 'Checkboxes (pick several)',
        'text'     => 'Text',
        'textarea' => 'Long text',
        'number'   => 'Number',
        'date'     => 'Date',
        'time'     => 'Time',
        'email'    => 'Email',
        'tel'      => 'Phone',
    ];

    /** Types that need a list of options. */
    public const CHOICE_TYPES = ['select', 'radio', 'color', 'checkbox'];

    public const MAX_FIELDS = 20;

    /** Admin form input (custom_fields[i][label|type|required|options[]|placeholder]) → stored fields. Options may also be "a, b, c". */
    public static function fromAdminInput(array $rows): array
    {
        $fields = [];
        $keys   = [];

        foreach (array_values($rows) as $row) {
            $label = trim((string) ($row['label'] ?? ''));
            $type  = (string) ($row['type'] ?? 'text');
            if ($label === '' || ! isset(self::TYPES[$type])) continue;

            $raw     = $row['options'] ?? [];
            $options = in_array($type, self::CHOICE_TYPES, true)
                ? collect(is_array($raw) ? $raw : explode(',', (string) $raw))->map(fn ($o) => trim($o))->filter(fn ($o) => $o !== '')->unique()->take(50)->values()->all()
                : [];
            if (in_array($type, self::CHOICE_TYPES, true) && ! $options) continue; // a dropdown with nothing to pick is useless

            // Stable key from the label, unique within the product.
            $base = Str::slug($label, '_') ?: 'field';
            $key  = $base;
            for ($i = 2; in_array($key, $keys, true); $i++) $key = $base.'_'.$i;
            $keys[] = $key;

            $fields[] = [
                'key'         => $key,
                'label'       => mb_substr($label, 0, 100),
                'type'        => $type,
                'required'    => filter_var($row['required'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'options'     => $options,
                'placeholder' => mb_substr(trim((string) ($row['placeholder'] ?? '')), 0, 150),
            ];
        }

        return array_slice($fields, 0, self::MAX_FIELDS);
    }

    public static function hasRequired(Product $product): bool
    {
        return collect($product->custom_fields ?? [])->contains('required', true);
    }

    /**
     * Validate the customer's answers (custom[key]) against the product's fields.
     * Returns cart attribute lines: [{id: null, title, value, type: 'custom'}].
     *
     * @throws ValidationException
     */
    public static function fromCustomerInput(Product $product, array $input): array
    {
        $fields = $product->custom_fields ?? [];
        if (! $fields) return [];

        $rules = $names = [];
        foreach ($fields as $f) {
            $name  = 'custom.'.$f['key'];
            $names[$name] = $f['label'];
            $rule  = [$f['required'] ? 'required' : 'nullable'];

            $rules[$name] = match ($f['type']) {
                'select', 'radio', 'color' => [...$rule, 'string', Rule::in($f['options'])],
                'checkbox' => [...$rule, 'array'],
                'number'   => [...$rule, 'numeric', 'max:1000000000'],
                'date'     => [...$rule, 'date'],
                'time'     => [...$rule, 'date_format:H:i'],
                'email'    => [...$rule, 'email', 'max:150'],
                'tel'      => [...$rule, 'string', 'max:30', 'regex:/^[0-9+\-\s()]+$/'],
                'textarea' => [...$rule, 'string', 'max:1000'],
                default    => [...$rule, 'string', 'max:255'],
            };
            if ($f['type'] === 'checkbox') {
                $rules[$name.'.*'] = ['string', Rule::in($f['options'])];
                $names[$name.'.*'] = $f['label'];
            }
        }

        $data = Validator::make(['custom' => $input], $rules, [], $names)->validate();

        $lines = [];
        foreach ($fields as $f) {
            $value = $data['custom'][$f['key']] ?? null;
            if (is_array($value)) $value = implode(', ', array_filter($value, 'filled'));
            if (! filled($value)) continue;

            $lines[] = ['id' => null, 'title' => $f['label'], 'value' => (string) $value, 'type' => 'custom'];
        }

        return $lines;
    }

    /**
     * Readable "Colour: Red · RAM: 8 GB" for a cart or order line. Handles the stored shape
     * [{title, value}] and older {title: value} maps.
     *
     * @return array<int, array{title:string, value:string}>
     */
    public static function describe(?array $attributes): array
    {
        $out = [];
        foreach ((array) $attributes as $k => $a) {
            if (is_array($a) && isset($a['title'])) {
                $out[] = ['title' => (string) $a['title'], 'value' => is_array($a['value'] ?? null) ? implode(', ', $a['value']) : (string) ($a['value'] ?? '')];
            } elseif (is_scalar($a)) {
                $out[] = ['title' => (string) $k, 'value' => (string) $a];
            }
        }

        return array_values(array_filter($out, fn ($a) => $a['value'] !== ''));
    }
}
