<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                講師管理
            </h2>
            <a href="{{ route('admin.teachers.create') }}"
                class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition">
                講師を追加
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-credentials-card :credentials="session('generated_credentials')" />

            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            ログインID</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <a href="{{ route('admin.teachers.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                                class="group inline-flex items-center space-x-1 hover:text-slate-700">
                                <span>氏名</span>
                                @if(request('sort') === 'name')
                                    <span class="text-blue-600">{{ request('direction') === 'asc' ? '↑' : '↓' }}</span>
                                @else
                                    <span class="text-slate-300 opacity-0 group-hover:opacity-100 transition">↕</span>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            経験年数</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            得意分野</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            スキル</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            担当企業</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <a href="{{ route('admin.teachers.index', array_merge(request()->query(), ['sort' => 'created_at', 'direction' => request('sort') === 'created_at' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                                class="group inline-flex items-center space-x-1 hover:text-slate-700">
                                <span>登録日</span>
                                @if(request('sort') === 'created_at')
                                    <span class="text-blue-600">{{ request('direction') === 'asc' ? '↑' : '↓' }}</span>
                                @else
                                    <span class="text-slate-300 opacity-0 group-hover:opacity-100 transition">↕</span>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            操作</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse ($teachers as $teacher)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-blue-700 font-mono">{{ $teacher->login_id }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-slate-900">{{ $teacher->name }}</div>
                                <div class="text-xs text-slate-500">{{ $teacher->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                <a href="{{ route('admin.companies.index', ['teacher_id' => $teacher->id, 'status' => 'finished']) }}"
                                    class="text-blue-600 hover:text-blue-900 hover:underline group flex items-center gap-1"
                                    title="過去の担当企業（修了済）を表示">
                                    {{ $teacher->profile?->years_of_experience ? $teacher->profile->years_of_experience . '年' : '-' }}
                                    @if($teacher->profile?->years_of_experience)
                                        <svg class="w-3 h-3 opacity-0 group-hover:opacity-100 transition-opacity" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                            </path>
                                        </svg>
                                    @endif
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 max-w-xs truncate"
                                title="{{ $teacher->profile?->specialty_fields }}">
                                {{ Str::limit($teacher->profile?->specialty_fields, 30) ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 max-w-xs truncate"
                                title="{{ $teacher->profile?->skills }}">
                                {{ Str::limit($teacher->profile?->skills, 30) ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $activeCompanies = $teacher->assignedCompanies->whereIn('status', ['active', 'free_trial']);
                                @endphp
                                @if($activeCompanies->isEmpty())
                                    <span class="text-slate-400 text-sm">現在の担当なし</span>
                                @else
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($activeCompanies as $company)
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $company->status === 'active' ? 'bg-green-50 text-green-700 border border-green-100' : 'bg-orange-50 text-orange-700 border border-orange-100' }}">
                                                {{ $company->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ $teacher->created_at->format('Y-m-d') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.teachers.edit', $teacher) }}"
                                    class="text-blue-600 hover:text-blue-900 mr-4">編集</a>
                                <form action="{{ route('admin.teachers.destroy', $teacher) }}" method="POST"
                                    class="inline-block" onsubmit="return confirm('本当に削除しますか？');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-900">削除</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500 text-sm">
                                登録されている講師はありません。
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>