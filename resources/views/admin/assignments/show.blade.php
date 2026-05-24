<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.assignments.index') }}"
                    class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                    課題詳細管理
                </h2>
            </div>

            @if(!$submission)
                <span
                    class="inline-flex items-center rounded-md bg-slate-100 px-3 py-1 text-sm font-medium text-slate-800 ring-1 ring-inset ring-slate-500/10">未提出</span>
            @elseif($submission->status == 'submitted')
                <span
                    class="inline-flex items-center rounded-md bg-amber-50 px-3 py-1 text-sm font-medium text-amber-800 ring-1 ring-inset ring-amber-600/20">確認中</span>
            @elseif($submission->status == 'passed')
                <span
                    class="inline-flex items-center rounded-md bg-emerald-50 px-3 py-1 text-sm font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">合格</span>
            @elseif($submission->status == 'revision_required')
                <span
                    class="inline-flex items-center rounded-md bg-red-50 px-3 py-1 text-sm font-medium text-red-700 ring-1 ring-inset ring-red-600/10">不合格</span>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Context Info -->
            <div class="bg-white shadow-sm rounded-lg border border-slate-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-base font-medium leading-6 text-slate-900">基本情報</h3>
                </div>
                <div class="border-t border-slate-200">
                    <dl class="divide-y divide-slate-100">
                        <div class="px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-slate-500">講義 / 課題名</dt>
                            <dd class="mt-1 text-sm text-slate-900 sm:col-span-2 sm:mt-0 font-medium">
                                {{ $lecturePage->title }}
                            </dd>
                        </div>
                        <div class="px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-slate-500">受講企業</dt>
                            <dd class="mt-1 text-sm text-slate-900 sm:col-span-2 sm:mt-0">
                                {{ $user->company->name ?? '所属なし' }}
                            </dd>
                        </div>
                        <div class="px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-slate-500">生徒名</dt>
                            <dd class="mt-1 text-sm text-slate-900 sm:col-span-2 sm:mt-0">{{ $user->name }} <span
                                    class="text-slate-400 ml-2">({{ $user->email }})</span></dd>
                        </div>
                        @if($submission)
                            <div class="px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-slate-500">提出日時</dt>
                                <dd class="mt-1 text-sm text-slate-900 sm:col-span-2 sm:mt-0">
                                    {{ $submission->created_at->format('Y/m/d H:i:s') }}
                                </dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Submission Area -->
            @if($submission)
                <div class="bg-white shadow-sm rounded-lg border border-slate-200 overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                        <h3 class="text-base font-medium leading-6 text-slate-900">提出内容・評価履歴</h3>
                    </div>

                    <div class="p-6">
                        @if($submission->items && $submission->items->count() > 0)
                            <div class="mb-8">
                                <h4 class="text-sm font-medium text-slate-500 mb-3 border-b border-slate-100 pb-2">成果物・URL</h4>
                                <ul class="space-y-3">
                                    @foreach($submission->items as $item)
                                        <li class="flex items-start bg-slate-50 p-3 rounded-md border border-slate-200">
                                            <svg class="h-5 w-5 mr-3 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                            </svg>
                                            <div class="min-w-0 flex-1">
                                                <a href="{{ $item->url }}" target="_blank"
                                                    class="text-sm text-indigo-600 hover:text-indigo-900 font-medium break-all">
                                                    {{ $item->url }}
                                                </a>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="space-y-4">
                            <h4 class="text-sm font-medium text-slate-500 mb-2 border-b border-slate-100 pb-2">講師からのフィードバック
                            </h4>

                            @if($submission->status === 'submitted')
                                <div class="rounded-md bg-amber-50 p-4 border border-amber-200">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-amber-400" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-amber-700 font-medium">現在講師が確認中です</p>
                                        </div>
                                    </div>
                                </div>
                            @elseif($submission->teacher_comment)
                                <div class="bg-indigo-50/50 rounded-lg p-5 border border-indigo-100 relative">
                                    <!-- Decorative quotation mark -->
                                    <svg class="absolute top-4 left-4 h-8 w-8 text-indigo-200/50" fill="currentColor"
                                        viewBox="0 0 32 32" aria-hidden="true">
                                        <path
                                            d="M9.352 4C4.456 7.456 1 13.12 1 19.36c0 5.088 3.072 8.064 6.624 8.064 3.36 0 5.856-2.688 5.856-5.856 0-3.168-2.208-5.472-5.088-5.472-.576 0-1.344.096-1.536.192.48-3.264 3.552-7.104 6.624-9.024L9.352 4zm16.512 0c-4.896 3.456-8.352 9.12-8.352 15.36 0 5.088 3.072 8.064 6.624 8.064 3.264 0 5.856-2.688 5.856-5.856 0-3.168-2.304-5.472-5.184-5.472-.576 0-1.248.096-1.44.192.48-3.264 3.456-7.104 6.528-9.024L25.864 4z" />
                                    </svg>

                                    <p class="text-sm text-slate-700 whitespace-pre-wrap leading-relaxed relative z-10 pl-6">
                                        {{ $submission->teacher_comment }}
                                    </p>

                                    <div
                                        class="mt-6 pt-4 border-t border-indigo-100/50 flex items-center justify-between text-xs text-slate-500 relative z-10">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="font-medium bg-white px-2 py-1 rounded-md border border-slate-200 shadow-sm">{{ $submission->reviewer->name ?? '不明な講師' }}</span>
                                            が評価を担当しました
                                        </div>
                                        <div>
                                            {{ $submission->reviewed_at ? $submission->reviewed_at->format('Y/m/d H:i') : '' }}
                                        </div>
                                    </div>
                                </div>
                            @else
                                <p class="text-sm text-slate-500 italic px-2">コメントは未入力です。</p>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <!-- Unsubmitted State -->
                <div class="text-center py-16 bg-white rounded-lg border border-dashed border-slate-300 shadow-sm">
                    <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-slate-900">未提出</h3>
                    <p class="mt-1 text-sm text-slate-500 max-w-sm mx-auto">
                        この生徒はまだ該当の課題を提出していません。
                    </p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>