<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            課題管理
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-4">
                <div class="overflow-hidden rounded-lg bg-white shadow-sm border border-slate-200">
                    <div class="p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="truncate text-sm font-medium text-slate-500">未提出</p>
                                <p
                                    class="mt-1 text-3xl font-semibold text-slate-900 border-b-2 border-slate-300 inline-block pb-1">
                                    {{ number_format($kpis->unsubmitted_count ?? 0) }}
                                </p>
                            </div>
                            <div class="rounded-md bg-slate-50 p-3">
                                <svg class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg bg-white shadow-sm border border-slate-200">
                    <div class="p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="truncate text-sm font-medium text-amber-600">確認中</p>
                                <p
                                    class="mt-1 text-3xl font-semibold text-slate-900 border-b-2 border-amber-300 inline-block pb-1">
                                    {{ number_format($kpis->reviewing_count ?? 0) }}
                                </p>
                            </div>
                            <div class="rounded-md bg-amber-50 p-3">
                                <svg class="h-6 w-6 text-amber-500" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="overflow-hidden rounded-lg bg-white shadow-sm border border-emerald-200 ring-1 ring-emerald-500 ring-opacity-5">
                    <div class="p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="truncate text-sm font-medium text-emerald-600">合格</p>
                                <p
                                    class="mt-1 text-3xl font-semibold text-slate-900 border-b-2 border-emerald-400 inline-block pb-1">
                                    {{ number_format($kpis->passed_count ?? 0) }}
                                </p>
                            </div>
                            <div class="rounded-md bg-emerald-50 p-3">
                                <svg class="h-6 w-6 text-emerald-500" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg bg-white shadow-sm border border-red-200">
                    <div class="p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="truncate text-sm font-medium text-red-600">不合格</p>
                                <p
                                    class="mt-1 text-3xl font-semibold text-slate-900 border-b-2 border-red-300 inline-block pb-1">
                                    {{ number_format($kpis->failed_count ?? 0) }}
                                </p>
                            </div>
                            <div class="rounded-md bg-red-50 p-3">
                                <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Area -->
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-5">
                <form method="GET" action="{{ route('admin.assignments.index') }}"
                    class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">

                    <div>
                        <label for="company_id" class="block text-sm font-medium text-slate-700">企業</label>
                        <select id="company_id" name="company_id"
                            class="mt-1 block w-full rounded-md border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">すべて</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="student_name" class="block text-sm font-medium text-slate-700">生徒名</label>
                        <input type="text" id="student_name" name="student_name" value="{{ request('student_name') }}"
                            placeholder="検索..."
                            class="mt-1 block w-full rounded-md border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="course_id" class="block text-sm font-medium text-slate-700">課題 (講義)</label>
                        <select id="course_id" name="course_id"
                            class="mt-1 block w-full rounded-md border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">すべて</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-slate-700">ステータス</label>
                        <select id="status" name="status"
                            class="mt-1 block w-full rounded-md border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="all">すべて</option>
                            <option value="unsubmitted" {{ request('status') == 'unsubmitted' ? 'selected' : '' }}>未提出
                            </option>
                            <option value="reviewing" {{ request('status') == 'reviewing' ? 'selected' : '' }}>確認中
                            </option>
                            <option value="passed" {{ request('status') == 'passed' ? 'selected' : '' }}>合格</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>不合格</option>
                        </select>
                    </div>

                    <div class="flex space-x-2">
                        <button type="submit"
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            絞り込み
                        </button>
                        <a href="{{ route('admin.assignments.index') }}"
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-slate-300 rounded-md shadow-sm text-sm font-medium text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            クリア
                        </a>
                    </div>
                </form>
            </div>

            <!-- Data Table -->
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                    課題名</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                    企業名</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                    生徒名 / Email</th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                    ステータス</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                    提出日 / 評価日</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                    担当講師</th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                    操作</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @forelse($assignments as $assignment)
                                                    <tr class="hover:bg-slate-50 transition-colors">
                                                        <td
                                                            class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900 border-l-4
                                                                                            {{ !$assignment->submission_id ? 'border-l-slate-300' :
                                ($assignment->submission_status == 'passed' ? 'border-l-emerald-400' :
                                    ($assignment->submission_status == 'revision_required' ? 'border-l-red-400' : 'border-l-amber-400')) }}">
                                                            {{ Str::limit($assignment->course_name, 25) }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                                            {{ $assignment->company_name ?? '-' }}
                                                        </td>
                                                        <td class="px-6 py-4">
                                                            <div class="text-sm font-medium text-slate-900">{{ $assignment->student_name }}
                                                            </div>
                                                            <div class="text-xs text-slate-500">{{ $assignment->student_email }}</div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                                            @if(!$assignment->submission_id)
                                                                <span
                                                                    class="inline-flex items-center rounded-md bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-800">未提出</span>
                                                            @elseif($assignment->submission_status == 'submitted')
                                                                <span
                                                                    class="inline-flex items-center rounded-md bg-amber-50 px-2.5 py-0.5 text-xs font-medium text-amber-800 ring-1 ring-inset ring-amber-600/20">確認中</span>
                                                            @elseif($assignment->submission_status == 'passed')
                                                                <span
                                                                    class="inline-flex items-center rounded-md bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">合格</span>
                                                            @elseif($assignment->submission_status == 'revision_required')
                                                                <span
                                                                    class="inline-flex items-center rounded-md bg-red-50 px-2.5 py-0.5 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10">不合格</span>
                                                            @endif
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                                            @if($assignment->submitted_at)
                                                                <div class="flex items-center mb-1">
                                                                    <svg class="h-3.5 w-3.5 mr-1.5 text-slate-400" fill="none" viewBox="0 0 24 24"
                                                                        stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                                    </svg>
                                                                    {{ \Carbon\Carbon::parse($assignment->submitted_at)->format('Y/m/d H:i') }}
                                                                </div>
                                                            @else
                                                                <span class="text-slate-300">-</span>
                                                            @endif

                                                            @if($assignment->reviewed_at)
                                                                <div class="flex items-center">
                                                                    <svg class="h-3.5 w-3.5 mr-1.5 text-emerald-500" fill="none" viewBox="0 0 24 24"
                                                                        stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                    </svg>
                                                                    {{ \Carbon\Carbon::parse($assignment->reviewed_at)->format('Y/m/d H:i') }}
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                                            {{ $assignment->reviewer_name ?? '-' }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                            <a href="{{ route('admin.assignments.show', ['user' => $assignment->student_id, 'lecturePage' => $assignment->course_id]) }}"
                                                                class="text-indigo-600 hover:text-indigo-900 border border-indigo-200 px-3 py-1.5 rounded-md hover:bg-indigo-50 transition-colors">
                                                                詳細
                                                            </a>
                                                        </td>
                                                    </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                        該当する課題・提出物が見つかりませんでした。
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                    {{ $assignments->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>