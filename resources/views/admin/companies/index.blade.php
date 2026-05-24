<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                企業管理
            </h2>
            <a href="{{ route('admin.companies.create') }}"
                class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition">
                企業を登録
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-credentials-card :credentials="session('generated_credentials')" />

            <!-- Search & Filter -->
    <div class="mb-6 bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
        <form method="GET" action="{{ route('admin.companies.index') }}"
            class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1 w-full">
                <x-input-label for="search" :value="__('企業名検索')" />
                <x-text-input id="search" name="search" type="text" class="mt-1 block w-full" :value="request('search')"
                    placeholder="企業名を入力..." />
            </div>
            <div class="w-full md:w-48">
                <x-input-label for="status" :value="__('ステータス')" />
                <select id="status" name="status"
                    class="mt-1 block w-full border-slate-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-sm">
                    <option value="">全て</option>
                    <option value="free_trial" {{ request('status') == 'free_trial' ? 'selected' : '' }}>無料研修中</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>研修中</option>
                    <option value="finished" {{ request('status') == 'finished' ? 'selected' : '' }}>修了済</option>
                </select>
            </div>
            <div class="w-full md:w-48">
                <x-input-label for="teacher_id" :value="__('担当講師')" />
                <select id="teacher_id" name="teacher_id"
                    class="mt-1 block w-full border-slate-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-sm">
                    <option value="">全て</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="w-full md:w-48">
                <x-input-label for="payment_status" :value="__('支払い状況')" />
                <select id="payment_status" name="payment_status"
                    class="mt-1 block w-full border-slate-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-sm">
                    <option value="">全て</option>
                    <option value="not_billed" {{ request('payment_status') == 'not_billed' ? 'selected' : '' }}>未請求</option>
                    <option value="billed" {{ request('payment_status') == 'billed' ? 'selected' : '' }}>請求済み</option>
                    <option value="waiting_payment" {{ request('payment_status') == 'waiting_payment' ? 'selected' : '' }}>支払い待ち</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>支払済</option>
                </select>
            </div>
            <div class="flex gap-2">
                <x-primary-button>
                    {{ __('検索') }}
                </x-primary-button>
                <a href="{{ route('admin.companies.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 rounded-md font-semibold text-xs text-slate-700 uppercase tracking-widest shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    リセット
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200"
        x-data="{ showModal: false, selectedCompany: null }">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <a href="{{ route('admin.companies.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                                class="group inline-flex items-center space-x-1 hover:text-slate-700">
                                <span>企業名</span>
                                @if(request('sort') === 'name')
                                    <span class="text-blue-600">{{ request('direction') === 'asc' ? '↑' : '↓' }}</span>
                                @else
                                    <span class="text-slate-300 opacity-0 group-hover:opacity-100 transition">↕</span>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <a href="{{ route('admin.companies.index', array_merge(request()->query(), ['sort' => 'status', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                                class="group inline-flex items-center space-x-1 hover:text-slate-700">
                                <span>ステータス</span>
                                @if(request('sort') === 'status')
                                    <span class="text-blue-600">{{ request('direction') === 'asc' ? '↑' : '↓' }}</span>
                                @else
                                    <span class="text-slate-300 opacity-0 group-hover:opacity-100 transition">↕</span>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            担当講師</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            受講人数</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <a href="{{ route('admin.companies.index', array_merge(request()->query(), ['sort' => 'contract_start_date', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                                class="group inline-flex items-center space-x-1 hover:text-slate-700">
                                <span>研修開始日</span>
                                @if(request('sort') === 'contract_start_date')
                                    <span class="text-blue-600">{{ request('direction') === 'asc' ? '↑' : '↓' }}</span>
                                @else
                                    <span class="text-slate-300 opacity-0 group-hover:opacity-100 transition">↕</span>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <a href="{{ route('admin.companies.index', array_merge(request()->query(), ['sort' => 'training_end_date', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                                class="group inline-flex items-center space-x-1 hover:text-slate-700">
                                <span>研修終了日</span>
                                @if(request('sort') === 'training_end_date')
                                    <span class="text-blue-600">{{ request('direction') === 'asc' ? '↑' : '↓' }}</span>
                                @else
                                    <span class="text-slate-300 opacity-0 group-hover:opacity-100 transition">↕</span>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <a href="{{ route('admin.companies.index', array_merge(request()->query(), ['sort' => 'contract_amount', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                                class="group inline-flex items-center space-x-1 hover:text-slate-700">
                                <span>契約金額</span>
                                @if(request('sort') === 'contract_amount')
                                    <span class="text-blue-600">{{ request('direction') === 'asc' ? '↑' : '↓' }}</span>
                                @else
                                    <span class="text-slate-300 opacity-0 group-hover:opacity-100 transition">↕</span>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <a href="{{ route('admin.companies.index', array_merge(request()->query(), ['sort' => 'payment_status', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                                class="group inline-flex items-center space-x-1 hover:text-slate-700">
                                <span>支払い状況</span>
                                @if(request('sort') === 'payment_status')
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
                    @forelse ($companies as $company)
                        @php
                            $teacherDisplayNames = $company->teacher_names;
                        @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900 cursor-pointer text-blue-600 hover:text-blue-800 hover:underline"
                                @click="showModal = true; selectedCompany = {{ json_encode([
                                    'name' => $company->name,
                                    'description' => $company->business_description ?? '未設定',
                                    'status_label' => $company->status_label,
                                    'teacher_name' => $teacherDisplayNames,
                                    'student_count' => $company->students_count . '名',
                                    'start_date' => $company->contract_start_date ? $company->contract_start_date->format('Y-m-d') : '-',
                                    'end_date' => $company->training_end_date ? $company->training_end_date->format('Y-m-d') : '-',
                                    'contract_amount' => $company->contract_amount !== null ? number_format($company->contract_amount) . '円' : '-',
                                    'payment_status' => $company->payment_status_label,
                                ]) }}">
                                {{ $company->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $company->status_class }}">
                                    {{ $company->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">
                                {{ $teacherDisplayNames }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ $company->students_count }} 名
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ $company->contract_start_date ? $company->contract_start_date->format('Y-m-d') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ $company->training_end_date ? $company->training_end_date->format('Y-m-d') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 font-medium">
                                {{ $company->contract_amount !== null ? number_format($company->contract_amount) . '円' : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $company->payment_status_class }}">
                                    {{ $company->payment_status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.companies.edit', $company) }}"
                                    class="text-blue-600 hover:text-blue-900 mr-4">編集</a>
                                <form action="{{ route('admin.companies.destroy', $company) }}" method="POST"
                                    class="inline-block" onsubmit="return confirm('本当に削除しますか？');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-900">削除</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-slate-500 text-sm">
                                条件に一致する企業はありません。
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

                <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showModal = false"
                    aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-slate-900" id="modal-title"
                                    x-text="selectedCompany?.name"></h3>
                                <div class="mt-2">
                                    <div class="grid grid-cols-1 gap-2 text-sm text-slate-500">
                                        <div class="flex justify-between border-b pb-2">
                                            <span class="font-bold text-slate-700">ステータス</span>
                                            <span x-text="selectedCompany?.status_label"></span>
                                        </div>
                                        <div class="flex justify-between border-b py-2">
                                            <span class="font-bold text-slate-700">担当講師</span>
                                            <span x-text="selectedCompany?.teacher_name"></span>
                                        </div>
                                        <div class="flex justify-between border-b py-2">
                                            <span class="font-bold text-slate-700">受講人数</span>
                                            <span x-text="selectedCompany?.student_count"></span>
                                        </div>
                                        <div class="flex justify-between border-b py-2">
                                            <span class="font-bold text-slate-700">研修開始日</span>
                                            <span x-text="selectedCompany?.start_date"></span>
                                        </div>
                                        <div class="flex justify-between border-b py-2">
                                            <span class="font-bold text-slate-700">研修終了日</span>
                                            <span x-text="selectedCompany?.end_date"></span>
                                        </div>
                                        <div class="flex justify-between border-b py-2">
                                            <span class="font-bold text-slate-700">契約金額</span>
                                            <span x-text="selectedCompany?.contract_amount"></span>
                                        </div>
                                        <div class="flex justify-between border-b py-2">
                                            <span class="font-bold text-slate-700">支払い状況</span>
                                            <span x-text="selectedCompany?.payment_status"></span>
                                        </div>
                                        <div class="pt-2">
                                            <span class="font-bold text-slate-700 block mb-1">事業内容</span>
                                            <p x-text="selectedCompany?.description"
                                                class="whitespace-pre-wrap bg-slate-50 p-2 rounded text-slate-600"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                            @click="showModal = false">
                            閉じる
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
        </div>
    </div>
</x-app-layout>
