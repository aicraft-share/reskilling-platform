<?php

namespace App\Services;

use App\Models\AdminOperationLog;
use Illuminate\Support\Arr;

class AdminOperationLogger
{
    private const SENSITIVE_FIELDS = ['password', 'remember_token'];

    /**
     * @param  array<string, mixed>  $before
     * @param  array<string, mixed>  $after
     */
    public function log(
        string $actionType,
        string $targetType,
        int|string|null $targetId,
        ?string $targetLabel,
        array $before,
        array $after
    ): void {
        $user = auth()->user();
        if (!$user || !$user->isAdmin()) {
            return;
        }

        $normalizedBefore = $this->normalize($before);
        $normalizedAfter = $this->normalize($after);

        [$changedFields, $diffBefore, $diffAfter] = $this->diff($normalizedBefore, $normalizedAfter);

        if ($actionType === AdminOperationLog::ACTION_UPDATE && empty($changedFields)) {
            return;
        }

        if ($actionType === AdminOperationLog::ACTION_UPDATE && $this->isStatusOnlyChange($changedFields)) {
            $actionType = AdminOperationLog::ACTION_STATUS_CHANGE;
        }

        $request = request();

        AdminOperationLog::create([
            'actor_admin_id' => $user->id,
            'actor_admin_name' => $user->name,
            'action_type' => $actionType,
            'target_type' => $targetType,
            'target_id' => is_numeric($targetId) ? (int) $targetId : null,
            'target_label' => $targetLabel,
            'changed_fields' => $changedFields,
            'before_values' => $diffBefore,
            'after_values' => $diffAfter,
            'route_name' => optional($request->route())->getName(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>
     */
    private function normalize(array $values): array
    {
        $normalized = [];

        foreach ($values as $key => $value) {
            if (in_array($key, self::SENSITIVE_FIELDS, true)) {
                $normalized[$key] = $value === null ? null : '[REDACTED]';
                continue;
            }

            if ($value instanceof \DateTimeInterface) {
                $normalized[$key] = $value->format('Y-m-d H:i:s');
                continue;
            }

            if (is_bool($value)) {
                $normalized[$key] = $value;
                continue;
            }

            if (is_array($value)) {
                $normalized[$key] = Arr::sortRecursive($value);
                continue;
            }

            $normalized[$key] = $value;
        }

        ksort($normalized);

        return $normalized;
    }

    /**
     * @param  array<string, mixed>  $before
     * @param  array<string, mixed>  $after
     * @return array{0: array<int, string>, 1: array<string, mixed>, 2: array<string, mixed>}
     */
    private function diff(array $before, array $after): array
    {
        $keys = array_unique(array_merge(array_keys($before), array_keys($after)));

        $changedFields = [];
        $diffBefore = [];
        $diffAfter = [];

        foreach ($keys as $key) {
            $beforeValue = $before[$key] ?? null;
            $afterValue = $after[$key] ?? null;

            if ($beforeValue !== $afterValue) {
                $changedFields[] = $key;
                $diffBefore[$key] = $beforeValue;
                $diffAfter[$key] = $afterValue;
            }
        }

        sort($changedFields);

        return [$changedFields, $diffBefore, $diffAfter];
    }

    /**
     * @param  array<int, string>  $changedFields
     */
    private function isStatusOnlyChange(array $changedFields): bool
    {
        $statusFields = ['status', 'is_active', 'payment_status'];

        return !empty($changedFields) && count(array_diff($changedFields, $statusFields)) === 0;
    }
}
