<?php

namespace App\Support;

use App\Models\OnboardingMaster;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Config-driven form definitions shared by onboarding chapters (config/onboarding.php)
 * and pathway protocols (config/protocols.php).
 *
 * - describe(): field list with resolved options, sent to the app to render the form
 * - validate(): normalises + validates submitted answers, returns the cleaned answers
 * - present():  label/value rows used by the admin panel to display saved answers
 */
class FormSchema
{
    /** @var array<string, \Illuminate\Support\Collection> master options cache keyed by category */
    protected static array $masters = [];

    /** Cache owner: the cache is dropped when a new application instance boots (tests, long-running processes) */
    protected static ?int $cacheOwner = null;

    /**
     * Validate answers against the schema.
     * Returns [array $answers, ?string $error]. When $draft is true required rules are skipped.
     */
    public static function validate(array $fields, array $input, bool $draft = false): array
    {
        $answers = self::normalize($fields, $input);

        $rules = [];
        $attributes = [];
        self::buildRules($fields, '', $draft, $rules, $attributes);

        $validator = Validator::make($answers, $rules, [], $attributes);
        if ($validator->fails()) {
            return [$answers, $validator->errors()->first()];
        }

        $error = self::validateMasterOptions($fields, $answers);

        return [$answers, $error];
    }

    /**
     * Field descriptors for the app (options resolved from onboarding_masters when needed).
     */
    public static function describe(array $fields): array
    {
        self::loadMasters($fields);

        $out = [];
        foreach ($fields as $key => $field) {
            $item = [
                'key'      => $key,
                'label'    => $field['label'] ?? formatName($key),
                'type'     => $field['type'],
                'required' => (bool) ($field['required'] ?? false),
            ];
            if (isset($field['required_if'])) {
                $item['required_if'] = ['field' => $field['required_if'][0], 'value' => $field['required_if'][1]];
            }
            foreach (['min', 'max'] as $limit) {
                if (isset($field[$limit])) {
                    $item[$limit] = $field[$limit];
                }
            }
            if (in_array($field['type'], ['single', 'multi'])) {
                $item['options'] = self::options($field);
            }
            if (in_array($field['type'], ['group', 'repeater'])) {
                $item['fields'] = self::describe($field['fields']);
            }
            $out[] = $item;
        }

        return $out;
    }

    /**
     * Human readable rows for the admin panel.
     * Each row: ['label' => string, 'value' => string|null, 'rows' => array|null]
     */
    public static function present(array $fields, ?array $answers): array
    {
        self::loadMasters($fields);
        $answers = $answers ?? [];

        $rows = [];
        foreach ($fields as $key => $field) {
            $value = $answers[$key] ?? null;
            $row = ['label' => $field['label'] ?? formatName($key), 'value' => null, 'rows' => null];

            switch ($field['type']) {
                case 'group':
                    $row['rows'] = is_array($value) ? self::present($field['fields'], $value) : [];
                    break;
                case 'repeater':
                    $row['rows'] = [];
                    foreach (is_array($value) ? $value : [] as $i => $item) {
                        $row['rows'][] = [
                            'label' => '#' . ($i + 1),
                            'value' => null,
                            'rows'  => self::present($field['fields'], is_array($item) ? $item : []),
                        ];
                    }
                    break;
                case 'boolean':
                    $row['value'] = is_null($value) ? null : ($value ? 'Yes' : 'No');
                    break;
                case 'single':
                    $row['value'] = self::optionLabel($field, $value);
                    break;
                case 'multi':
                    $labels = array_map(fn ($v) => self::optionLabel($field, $v), is_array($value) ? $value : []);
                    $row['value'] = $labels ? implode(', ', $labels) : null;
                    break;
                case 'list':
                    $row['value'] = is_array($value) && $value ? implode(' | ', $value) : null;
                    break;
                default:
                    $row['value'] = is_scalar($value) ? (string) $value : null;
            }

            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * Drop unknown keys and cast values to their schema types.
     */
    public static function normalize(array $fields, array $input): array
    {
        $out = [];
        foreach ($fields as $key => $field) {
            if (!array_key_exists($key, $input)) {
                continue;
            }
            $value = $input[$key];

            if (is_string($value)) {
                $value = trim($value);
                if ($value === '') {
                    $value = null;
                }
            }

            switch ($field['type']) {
                case 'boolean':
                    if (!is_null($value) && !is_bool($value)) {
                        $bool = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                        $value = $bool ?? $value;
                    }
                    break;
                case 'number':
                    if (is_string($value) && is_numeric($value)) {
                        $value = $value + 0;
                    }
                    break;
                case 'multi':
                case 'list':
                    if (is_string($value)) {
                        $value = array_values(array_filter(array_map('trim', explode(',', $value)), 'strlen'));
                    }
                    if (is_array($value)) {
                        $value = array_values(array_unique(array_filter($value, fn ($v) => !is_null($v) && $v !== '')));
                    }
                    break;
                case 'group':
                    $value = is_array($value) ? self::normalize($field['fields'], $value) : $value;
                    break;
                case 'repeater':
                    if (is_array($value)) {
                        $value = array_values(array_map(
                            fn ($item) => is_array($item) ? self::normalize($field['fields'], $item) : $item,
                            $value
                        ));
                    }
                    break;
            }

            $out[$key] = $value;
        }

        return $out;
    }

    protected static function buildRules(array $fields, string $prefix, bool $draft, array &$rules, array &$attributes): void
    {
        foreach ($fields as $key => $field) {
            $path = $prefix . $key;
            $attributes[$path] = strtolower($field['label'] ?? formatName($key));

            $presence = 'nullable';
            if (!$draft && !empty($field['required'])) {
                $presence = 'required';
            } elseif (!$draft && isset($field['required_if'])) {
                [$other, $expected] = $field['required_if'];
                $expected = is_bool($expected) ? ($expected ? 'true' : 'false') : $expected;
                $presence = 'required_if:' . $prefix . $other . ',' . $expected;
            }

            $type = [];
            switch ($field['type']) {
                case 'text':
                    $type = ['string', 'max:' . ($field['max'] ?? 255)];
                    break;
                case 'textarea':
                    $type = ['string', 'max:' . ($field['max'] ?? 5000)];
                    if (!empty($field['min']) && !$draft) {
                        $type[] = 'min:' . $field['min'];
                    }
                    break;
                case 'email':
                    $type = ['email', 'max:255'];
                    break;
                case 'number':
                    $type = ['numeric'];
                    if (isset($field['min'])) {
                        $type[] = 'min:' . $field['min'];
                    }
                    if (isset($field['max'])) {
                        $type[] = 'max:' . $field['max'];
                    }
                    break;
                case 'date':
                    $type = ['date'];
                    break;
                case 'time':
                    $type = ['string', 'max:20'];
                    break;
                case 'boolean':
                    $type = ['boolean'];
                    break;
                case 'single':
                    $type = isset($field['options']) ? [Rule::in(array_map('strval', array_keys($field['options'])))] : ['string', 'max:64'];
                    break;
                case 'multi':
                case 'list':
                    $type = ['array'];
                    if (isset($field['max'])) {
                        $type[] = 'max:' . $field['max'];
                    }
                    $rules[$path . '.*'] = ($field['type'] === 'multi' && isset($field['options']))
                        ? [Rule::in(array_map('strval', array_keys($field['options'])))]
                        : ['string', 'max:255'];
                    break;
                case 'group':
                    $type = ['array'];
                    self::buildRules($field['fields'], $path . '.', $draft || empty($field['required']), $rules, $attributes);
                    break;
                case 'repeater':
                    $type = ['array'];
                    self::buildRules($field['fields'], $path . '.*.', $draft, $rules, $attributes);
                    break;
            }

            $rules[$path] = array_merge([$presence], $type);
        }
    }

    /**
     * Ensure master-backed answers reference active onboarding_masters rows of the right category.
     */
    protected static function validateMasterOptions(array $fields, array $answers): ?string
    {
        self::loadMasters($fields);

        foreach ($fields as $key => $field) {
            $value = $answers[$key] ?? null;
            if (is_null($value)) {
                continue;
            }

            if (in_array($field['type'], ['group']) && is_array($value)) {
                if ($error = self::validateMasterOptions($field['fields'], $value)) {
                    return $error;
                }
                continue;
            }

            if (empty($field['master'])) {
                continue;
            }

            $valid = (self::$masters[$field['master']] ?? collect())->where('status', 'Active')->pluck('uuid')->all();
            foreach ((array) $value as $selected) {
                if (!in_array($selected, $valid, true)) {
                    return __('api.invalid_option_selected', ['field' => strtolower($field['label'] ?? formatName($key))]);
                }
            }
        }

        return null;
    }

    protected static function options(array $field): array
    {
        if (isset($field['options'])) {
            return collect($field['options'])
                ->map(fn ($label, $value) => ['value' => (string) $value, 'label' => $label])
                ->values()
                ->all();
        }

        return (self::$masters[$field['master']] ?? collect())
            ->where('status', 'Active')
            ->map(fn ($master) => [
                'value'       => $master->uuid,
                'label'       => $master->title,
                'description' => $master->description,
                'image'       => $master->image ? getFileUrl($master->image) : null,
            ])
            ->values()
            ->all();
    }

    protected static function optionLabel(array $field, $value): ?string
    {
        if (is_null($value)) {
            return null;
        }
        if (isset($field['options'])) {
            return $field['options'][$value] ?? (string) $value;
        }

        $master = (self::$masters[$field['master'] ?? ''] ?? collect())->firstWhere('uuid', $value);

        return $master?->title ?? (string) $value;
    }

    protected static function loadMasters(array $fields): void
    {
        if (self::$cacheOwner !== spl_object_id(app())) {
            self::$masters = [];
            self::$cacheOwner = spl_object_id(app());
        }

        $categories = self::masterCategories($fields);
        $missing = array_values(array_diff($categories, array_keys(self::$masters)));
        if (!$missing) {
            return;
        }

        // Inactive options are kept so previously saved answers still render, but only active ones are offered/accepted
        $rows = OnboardingMaster::whereIn('category', $missing)->orderBy('sort_order')->orderBy('id')->get()->groupBy('category');
        foreach ($missing as $category) {
            self::$masters[$category] = ($rows[$category] ?? collect())->values();
        }
    }

    protected static function masterCategories(array $fields): array
    {
        $categories = [];
        foreach ($fields as $field) {
            if (!empty($field['master'])) {
                $categories[] = $field['master'];
            }
            if (!empty($field['fields'])) {
                $categories = array_merge($categories, self::masterCategories($field['fields']));
            }
        }

        return array_values(array_unique($categories));
    }
}
