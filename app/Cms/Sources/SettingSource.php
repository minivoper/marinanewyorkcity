<?php

namespace App\Cms\Sources;

use Eshlink\Cms\Sources\ModelSource;
use Illuminate\Database\Eloquent\Model;

/**
 * `ModelSource` for the one column on this site whose stored value is not a
 * scalar: `settings.value` is cast to `array` by the `Setting` model.
 *
 * The field payload the CMS moves around is JSON all the way down, and a field
 * schema describes scalars, repeaters and images — not an arbitrary blob. Rather
 * than invent a field type for a single table, the value is presented to the
 * editor as the JSON text it already is and parsed back on the way in. The cast
 * on the model does the rest, so `Setting::value` keeps handing back the same
 * array it always did.
 *
 * Malformed JSON never reaches the model: the schema attaches Laravel's `json`
 * rule to the field, so a save is refused before it gets here.
 */
class SettingSource extends ModelSource
{
    /**
     * @return array<string, mixed>
     */
    public function project(Model $model): array
    {
        return array_merge(parent::project($model), [
            'value' => json_encode(
                $model->getAttribute('value'),
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
            ),
        ]);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function applyToModel(Model $model, array $payload): void
    {
        parent::applyToModel($model, $payload);

        if (! array_key_exists('value', $payload) || ! is_string($payload['value'])) {
            return;
        }

        $decoded = json_decode($payload['value'], true);

        $model->setAttribute('value', is_array($decoded) ? $decoded : []);
    }
}
