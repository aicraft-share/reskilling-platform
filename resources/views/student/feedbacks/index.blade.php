<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            フィードバック・評価一覧
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Feedbacks Timeline / List -->
            <div class="bg-slate-50/50 rounded-lg border border-slate-200 p-6 min-h-[500px]">
                
                @if($feedbacks->isEmpty())
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-slate-900">フィードバックはまだありません</h3>
                        <p class="mt-1 text-sm text-slate-500">
                            提出物が評価されると、ここに履歴が表示されます。
                        </p>
                    </div>
                @else
                    
                    <div class="space-y-6 max-w-4xl mx-auto">
                        @foreach($feedbacks as $feedback)
                            <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden relative">
                                <div class="absolute top-0 left-0 w-1 h-full 
                                    {{ $feedback->status === \App\Models\Submission::STATUS_PASSED ? 'bg-emerald-400' : 
                                       ($feedback->status === \App\Models\Submission::STATUS_REVISION_REQUIRED ? 'bg-red-400' : 'bg-amber-400') }}">
                                </div>
                                <div class="p-5 pl-7">
                                    <div class="flex items-start justify-between mb-4">
                                        <div>
                                            <div class="flex items-center gap-3 mb-1">
                                                @if($feedback->status === \App\Models\Submission::STATUS_PASSED)
                                                    <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">合格</span>
                                                @elseif($feedback->status === \App\Models\Submission::STATUS_REVISION_REQUIRED)
                                                    <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10">不合格</span>
                                                @else
                                                    <span class="inline-flex items-center rounded-md bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20">確認中</span>
                                                @endif
                                                
                                                @if($feedback->reviewed_at)
                                                    <span class="text-xs text-slate-500 flex items-center">
                                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        評価日時: {{ $feedback->reviewed_at->format('Y/m/d H:i') }}
                                                    </span>
                                                @else
                                                    <span class="text-xs text-slate-500 flex items-center">
                                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        提出日時: {{ $feedback->created_at->format('Y/m/d H:i') }}
                                                    </span>
                                                @endif
                                            </div>
                                            <h4 class="text-md font-bold text-slate-800">{{ $feedback->lecturePage->title }} の課題</h4>
                                        </div>
                                        
                                        @if($feedback->reviewer)
                                            <div class="flex items-center gap-2 bg-slate-50 px-3 py-1.5 rounded-full border border-slate-100">
                                                <div class="h-6 w-6 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs ring-2 ring-white">
                                                    {{ mb_substr($feedback->reviewer->name, 0, 1) }}
                                                </div>
                                                <span class="text-xs font-medium text-slate-700">{{ $feedback->reviewer->name }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    @if($feedback->teacher_comment)
                                        <div class="bg-indigo-50/40 rounded-md p-4 mt-4 border border-indigo-100 text-sm text-slate-700 leading-relaxed whitespace-pre-wrap">{{ $feedback->teacher_comment }}</div>
                                    @elseif($feedback->status !== \App\Models\Submission::STATUS_SUBMITTED)
                                        <div class="bg-slate-50 rounded-md p-4 mt-4 text-sm text-slate-500 italic border border-slate-100">コメントなし</div>
                                    @else
                                        <div class="bg-amber-50 rounded-md p-4 mt-4 text-sm text-amber-600 flex items-center border border-amber-100">
                                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            現在、講師が内容を確認中です。評価が完了するまでしばらくお待ちください。
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
