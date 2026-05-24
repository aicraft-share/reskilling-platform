<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">操作ログ</h2>
    </x-slot>

    <div class="mb-6 bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
        <form method="GET" action="{{ route('admin.operation-logs.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
            <div>
                <x-input-label for="actor_admin_id" :value="__('操作者')" />
                <select id="actor_admin_id" name="actor_admin_id" class="mt-1 block w-full border-slate-300 rounded-md shadow-sm text-sm">
                    <option value="">全て</option>
                    @foreach($admins as $admin)
                        <option value="{{ $admin->id }}" {{ request('actor_admin_id') == $admin->id ? 'selected' : '' }}>{{ $admin->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <x-input-label for="action_type" :value="__('操作種別')" />
                <select id="action_type" name="action_type" class="mt-1 block w-full border-slate-300 rounded-md shadow-sm text-sm">
                    <option value="">全て</option>
                    <option value="create" {{ request('action_type') === 'create' ? 'selected' : '' }}>create</option>
                    <option value="update" {{ request('action_type') === 'update' ? 'selected' : '' }}>update</option>
                    <option value="delete" {{ request('action_type') === 'delete' ? 'selected' : '' }}>delete</option>
                    <option value="status_change" {{ request('action_type') === 'status_change' ? 'selected' : '' }}>status_change</option>
                </select>
            </div>

            <div>
                <x-input-label for="target_type" :value="__('対象種別')" />
                <select id="target_type" name="target_type" class="mt-1 block w-full border-slate-300 rounded-md shadow-sm text-sm">
                    <option value="">全て</option>
                    <option value="company" {{ request('target_type') === 'company' ? 'selected' : '' }}>company</option>
                    <option value="student" {{ request('target_type') === 'student' ? 'selected' : '' }}>student</option>
                    <option value="instructor" {{ request('target_type') === 'instructor' ? 'selected' : '' }}>instructor</option>
                    <option value="assignment" {{ request('target_type') === 'assignment' ? 'selected' : '' }}>assignment</option>
                    <option value="curriculum" {{ request('target_type') === 'curriculum' ? 'selected' : '' }}>curriculum</option>
                    <option value="admin_user" {{ request('target_type') === 'admin_user' ? 'selected' : '' }}>admin_user</option>
                    <option value="announcement" {{ request('target_type') === 'announcement' ? 'selected' : '' }}>announcement</option>
                    <option value="setting" {{ request('target_type') === 'setting' ? 'selected' : '' }}>setting</option>
                </select>
            </div>

            <div>
                <x-input-label for="date_from" :value="__('期間(開始)')" />
                <x-text-input id="date_from" name="date_from" type="date" class="mt-1 block w-full" :value="request('date_from')" />
            </div>

            <div>
                <x-input-label for="date_to" :value="__('期間(終了)')" />
                <x-text-input id="date_to" name="date_to" type="date" class="mt-1 block w-full" :value="request('date_to')" />
            </div>

            <div>
                <x-input-label for="keyword" :value="__('キーワード')" />
                <x-text-input id="keyword" name="keyword" type="text" class="mt-1 block w-full" :value="request('keyword')" placeholder="対象名など" />
            </div>

            <div class="md:col-span-6 flex gap-2">
                <x-primary-button>検索</x-primary-button>
                <a href="{{ route('admin.operation-logs.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 rounded-md font-semibold text-xs text-slate-700 uppercase tracking-widest shadow-sm hover:bg-slate-50">
                    リセット
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">操作日時</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">操作者</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">操作種別</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">対象種別</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">対象名</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">変更項目</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">操作</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-700">{{ $log->created_at?->format('Y-m-d H:i:s') }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-700">{{ $log->actor_admin_name }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-700">{{ $log->action_type }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-700">{{ $log->target_type }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-700">{{ $log->target_label ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-slate-700">
                                {{ !empty($log->changed_fields) ? implode(', ', $log->changed_fields) : '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm">
                                <a href="{{ route('admin.operation-logs.show', $log) }}" class="text-blue-600 hover:text-blue-900">詳細</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500 text-sm">ログはありません。</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-200">
            {{ $logs->links() }}
        </div>
    </div>
</x-app-layout>
