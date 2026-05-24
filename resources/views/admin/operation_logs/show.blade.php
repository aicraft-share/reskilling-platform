<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">操作ログ詳細</h2>
            <a href="{{ route('admin.operation-logs.index') }}" class="text-sm text-blue-600 hover:text-blue-800">一覧へ戻る</a>
        </div>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div><span class="font-semibold text-slate-700">操作日時:</span> {{ $operationLog->created_at?->format('Y-m-d H:i:s') }}</div>
                <div><span class="font-semibold text-slate-700">操作者:</span> {{ $operationLog->actor_admin_name }}</div>
                <div><span class="font-semibold text-slate-700">操作種別:</span> {{ $operationLog->action_type }}</div>
                <div><span class="font-semibold text-slate-700">対象種別:</span> {{ $operationLog->target_type }}</div>
                <div><span class="font-semibold text-slate-700">対象ID:</span> {{ $operationLog->target_id ?? '-' }}</div>
                <div><span class="font-semibold text-slate-700">対象名:</span> {{ $operationLog->target_label ?? '-' }}</div>
                <div><span class="font-semibold text-slate-700">Route:</span> {{ $operationLog->route_name ?? '-' }}</div>
                <div><span class="font-semibold text-slate-700">IP:</span> {{ $operationLog->ip_address ?? '-' }}</div>
            </div>

            <div>
                <h3 class="text-base font-semibold text-slate-800 mb-3">変更差分</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 border border-slate-200 rounded-lg">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">項目</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">変更前</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">変更後</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @php
                                $fields = $operationLog->changed_fields ?? [];
                                $before = $operationLog->before_values ?? [];
                                $after = $operationLog->after_values ?? [];
                            @endphp

                            @forelse ($fields as $field)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-slate-700">{{ \App\Models\AdminOperationLog::fieldLabel($field) }}</td>
                                    <td class="px-4 py-2 text-sm text-slate-600">{{ is_array($before[$field] ?? null) ? json_encode($before[$field], JSON_UNESCAPED_UNICODE) : (($before[$field] ?? null) === null ? '-' : (string) $before[$field]) }}</td>
                                    <td class="px-4 py-2 text-sm text-slate-600">{{ is_array($after[$field] ?? null) ? json_encode($after[$field], JSON_UNESCAPED_UNICODE) : (($after[$field] ?? null) === null ? '-' : (string) $after[$field]) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-4 text-center text-sm text-slate-500">差分情報はありません。</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($operationLog->user_agent)
                <div class="text-xs text-slate-500 break-all">
                    <span class="font-semibold text-slate-700">User Agent:</span> {{ $operationLog->user_agent }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
