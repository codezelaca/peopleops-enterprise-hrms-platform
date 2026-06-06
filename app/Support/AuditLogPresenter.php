<?php

namespace App\Support;

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AuditLogPresenter
{
    /**
     * @return array<string, mixed>
     */
    public function present(Activity $activity): array
    {
        $properties = $this->properties($activity);
        $route = Arr::get($properties, 'route');

        return [
            'id' => $activity->id,
            'event' => $activity->event ?: 'recorded',
            'event_label' => Str::headline($activity->event ?: 'recorded'),
            'description' => $activity->description,
            'summary' => $this->summary($activity),
            'log_name' => $activity->log_name,
            'actor' => $this->actor($activity),
            'subject' => $this->subject($activity),
            'route' => $route,
            'method' => Arr::get($properties, 'method'),
            'url' => Arr::get($properties, 'url'),
            'ip_address' => Arr::get($properties, 'ip_address'),
            'user_agent' => Arr::get($properties, 'user_agent'),
            'changes' => $this->changes($properties),
            'metadata' => $this->metadata($properties),
            'occurred_at' => $activity->created_at?->toIso8601String(),
            'occurred_at_readable' => $activity->created_at?->timezone(config('app.timezone'))->format('M j, Y, g:i A'),
            'occurred_at_human' => $activity->created_at?->diffForHumans(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function properties(Activity $activity): array
    {
        $properties = $activity->properties;

        if ($properties instanceof Collection) {
            return $properties->toArray();
        }

        return is_array($properties) ? $properties : [];
    }

    /**
     * @return array<string, string|null>
     */
    private function actor(Activity $activity): array
    {
        $causer = $activity->causer;

        if ($causer instanceof User) {
            return [
                'name' => $causer->name,
                'email' => $causer->email,
                'type' => 'User',
            ];
        }

        return [
            'name' => $causer ? class_basename($causer) : 'System',
            'email' => null,
            'type' => $causer ? class_basename($causer) : 'System',
        ];
    }

    /**
     * @return array<string, string|null>
     */
    private function subject(Activity $activity): array
    {
        $subject = $activity->subject;

        if ($subject instanceof User) {
            return [
                'name' => $subject->name,
                'type' => 'User',
                'detail' => $subject->email,
            ];
        }

        if ($subject instanceof Company) {
            return [
                'name' => $subject->name,
                'type' => 'Company',
                'detail' => $subject->legal_name,
            ];
        }

        if ($subject instanceof Role || $subject instanceof Permission) {
            return [
                'name' => $subject->name,
                'type' => class_basename($subject),
                'detail' => null,
            ];
        }

        return [
            'name' => $subject ? class_basename($subject).' #'.$subject->getKey() : 'No subject',
            'type' => $activity->subject_type ? class_basename($activity->subject_type) : null,
            'detail' => null,
        ];
    }

    private function summary(Activity $activity): string
    {
        $actor = $this->actor($activity)['name'];
        $subject = $this->subject($activity);
        $event = Str::lower(Str::headline($activity->event ?: 'recorded'));

        if (Str::contains($activity->description, 'viewed')) {
            return "{$actor} {$activity->description}.";
        }

        if ($subject['name'] !== 'No subject') {
            return "{$actor} {$event} {$subject['type']} {$subject['name']}.";
        }

        return "{$actor} {$activity->description}.";
    }

    /**
     * @param  array<string, mixed>  $properties
     * @return array<int, array<string, string|null>>
     */
    private function changes(array $properties): array
    {
        $new = Arr::get($properties, 'attributes', []);
        $old = Arr::get($properties, 'old', []);
        $keys = collect(array_keys(array_merge(is_array($old) ? $old : [], is_array($new) ? $new : [])))
            ->reject(fn (string $key): bool => $this->isSensitive($key))
            ->unique()
            ->values();

        $changes = $keys->map(fn (string $key): array => [
            'field' => Str::headline($key),
            'old' => $this->stringValue(Arr::get($old, $key)),
            'new' => $this->stringValue(Arr::get($new, $key)),
        ])->all();

        foreach (['roles', 'permissions'] as $key) {
            if (array_key_exists($key, $properties)) {
                $changes[] = [
                    'field' => Str::headline($key),
                    'old' => null,
                    'new' => $this->stringValue($properties[$key]),
                ];
            }
        }

        return $changes;
    }

    /**
     * @param  array<string, mixed>  $properties
     * @return array<int, array<string, string>>
     */
    private function metadata(array $properties): array
    {
        return collect(Arr::except($properties, ['attributes', 'old', 'roles', 'permissions', 'user_agent']))
            ->reject(fn (mixed $value, string $key): bool => $this->isSensitive($key) || blank($value))
            ->map(fn (mixed $value, string $key): array => [
                'label' => Str::headline($key),
                'value' => $this->stringValue($value) ?? '',
            ])
            ->values()
            ->all();
    }

    private function stringValue(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (is_array($value)) {
            return collect($value)->flatten()->join(', ');
        }

        return (string) $value;
    }

    private function isSensitive(string $key): bool
    {
        return Str::contains(Str::lower($key), [
            'password',
            'token',
            'secret',
            'credential',
            'recovery',
            'remember',
        ]);
    }
}
