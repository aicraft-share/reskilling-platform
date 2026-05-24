<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                生徒管理
            </h2>
            <a href="{{ route('admin.students.create') }}"
                class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition">
                生徒を追加
            </a>
        </div>
    </x-slot>

    <!-- Search & Filter -->
    <div class="mb-6 bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
        <form method="GET" action="{{ route('admin.students.index') }}"
            class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1 w-full">
                <x-input-label for="search" :value="__('生徒名検索')" />
                <x-text-input id="search" name="search" type="text" class="mt-1 block w-full" :value="request('search')"
                    placeholder="生徒名を入力..." />
            </div>
            <div class="w-full md:w-48">
                <x-input-label for="company_id" :value="__('所属企業')" />
                <select id="company_id" name="company_id"
                    class="mt-1 block w-full border-slate-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-sm">
                    <option value="">全て</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <x-primary-button>
                    {{ __('検索') }}
                </x-primary-button>
                <a href="{{ route('admin.students.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 rounded-md font-semibold text-xs text-slate-700 uppercase tracking-widest shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    リセット
                </a>
            </div>
        </form>
    </div>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-credentials-card :credentials="session('generated_credentials')" />

            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            ログインID</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <a href="{{ route('admin.students.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => request('sort') === 'name' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                                class="group inline-flex items-center space-x-1 hover:text-slate-700">
                                <span>氏名</span>
                                @if(request('sort') === 'name')
                                    <span
                                        class="text-blue-600 font-bold">{{ request('direction') === 'asc' ? '↑' : '↓' }}</span>
                                @else
                                    <span class="text-slate-300 opacity-0 group-hover:opacity-100 transition">↕</span>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <a href="{{ route('admin.students.index', array_merge(request()->query(), ['sort' => 'email', 'direction' => request('sort') === 'email' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                                class="group inline-flex items-center space-x-1 hover:text-slate-700">
                                <span>メールアドレス</span>
                                @if(request('sort') === 'email')
                                    <span
                                        class="text-blue-600 font-bold">{{ request('direction') === 'asc' ? '↑' : '↓' }}</span>
                                @else
                                    <span class="text-slate-300 opacity-0 group-hover:opacity-100 transition">↕</span>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <a href="{{ route('admin.students.index', array_merge(request()->query(), ['sort' => 'company_name', 'direction' => request('sort') === 'company_name' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                                class="group inline-flex items-center space-x-1 hover:text-slate-700">
                                <span>所属企業</span>
                                @if(request('sort') === 'company_name')
                                    <span
                                        class="text-blue-600 font-bold">{{ request('direction') === 'asc' ? '↑' : '↓' }}</span>
                                @else
                                    <span class="text-slate-300 opacity-0 group-hover:opacity-100 transition">↕</span>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <a href="{{ route('admin.students.index', array_merge(request()->query(), ['sort' => 'companies.status', 'direction' => request('sort') === 'companies.status' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                                class="group inline-flex items-center space-x-1 hover:text-slate-700">
                                <span>ステータス</span>
                                @if(request('sort') === 'companies.status')
                                    <span
                                        class="text-blue-600 font-bold">{{ request('direction') === 'asc' ? '↑' : '↓' }}</span>
                                @else
                                    <span class="text-slate-300 opacity-0 group-hover:opacity-100 transition">↕</span>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <a href="{{ route('admin.students.index', array_merge(request()->query(), ['sort' => 'companies.contract_start_date', 'direction' => request('sort') === 'companies.contract_start_date' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                                class="group inline-flex items-center space-x-1 hover:text-slate-700">
                                <span>研修開始日</span>
                                @if(request('sort') === 'companies.contract_start_date')
                                    <span
                                        class="text-blue-600 font-bold">{{ request('direction') === 'asc' ? '↑' : '↓' }}</span>
                                @else
                                    <span class="text-slate-300 opacity-0 group-hover:opacity-100 transition">↕</span>
                                @endif
                            </a>
                            </a>
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            操作</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse ($students as $student)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-blue-700 font-mono">
                                {{ $student->login_id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">{{ $student->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">{{ $student->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                @if($student->company)
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-800">
                                        {{ $student->company->name }}
                                    </span>
                                @else
                                    <span class="text-slate-400">未所属</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                @if($student->company)
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $student->company->status_class }}">
                                        {{ $student->company->status_label }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                {{ $student->company && $student->company->contract_start_date ? $student->company->contract_start_date->format('Y-m-d') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.students.edit', $student) }}"
                                    class="text-blue-600 hover:text-blue-900 mr-4">編集</a>
                                <form action="{{ route('admin.students.destroy', $student) }}" method="POST"
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
                                条件に一致する生徒はありません。
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