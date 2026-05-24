<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            担当
        </h2>
    </x-slot>

    <!-- Tab Navigation -->
    <div class="mb-6 border-b border-slate-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <a href="{{ route('teacher.assignments.students') }}"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-blue-500 text-blue-600">
                生徒
            </a>
            <a href="{{ route('teacher.assignments.companies') }}"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300">
                企業
            </a>
        </nav>
    </div>

    <!-- Status Message -->
    @if (session('message'))
        <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-400 p-4 shadow-sm rounded-r-xl">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-emerald-800">
                        {{ session('message') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Search & Filter -->
    <div class="mb-6 bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
        <form method="GET" action="{{ route('teacher.assignments.students') }}"
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
                <a href="{{ route('teacher.assignments.students') }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 rounded-md font-semibold text-xs text-slate-700 uppercase tracking-widest shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
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
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <a href="{{ route('teacher.assignments.students', array_merge(request()->query(), ['sort' => 'name', 'direction' => request('sort') === 'name' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
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
                            <a href="{{ route('teacher.assignments.students', array_merge(request()->query(), ['sort' => 'email', 'direction' => request('sort') === 'email' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
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
                            <a href="{{ route('teacher.assignments.students', array_merge(request()->query(), ['sort' => 'company_name', 'direction' => request('sort') === 'company_name' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
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
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            操作</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse ($students as $student)
                        <tr class="hover:bg-slate-50 transition">
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
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('teacher.students.progress.show', $student) }}"
                                        class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-md text-xs font-semibold transition-colors duration-200 gap-1 border border-blue-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 8.25V18a2.25 2.25 0 002.25 2.25h13.5A2.25 2.25 0 0021 18V8.25m-18 0V6a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 6v2.25m-18 0h18M5.25 6h.008v.008H5.25V6zM7.5 6h.008v.008H7.5V6zm2.25 0h.008v.008H9.75V6z" />
                                        </svg>
                                        詳細
                                    </a>
                                    <a href="{{ route('teacher.students.next-action.create', $student) }}"
                                        class="inline-flex items-center px-3 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-md text-xs font-semibold transition-colors duration-200 gap-1 border border-emerald-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 18 4.5h-13.5A2.25 2.25 0 0 0 3 6.75v10.5A2.25 2.25 0 0 0 5.25 19.5h15" />
                                        </svg>
                                        宿題
                                    </a>
                                    <a href="{{ route('teacher.students.mtgs', $student) }}#create"
                                        class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-md text-xs font-semibold transition-colors duration-200 gap-1 border border-indigo-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                        </svg>
                                        MTG
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-slate-500 text-sm">
                                条件に一致する生徒はありません。
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4 px-4 py-3 border-t border-slate-200">
            {{ $students->links() }}
        </div>
    </div>
</x-app-layout>