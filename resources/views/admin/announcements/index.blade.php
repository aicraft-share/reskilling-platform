<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                お知らせ配信履歴
            </h2>
            <a href="{{ route('admin.announcements.create') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                新規配信
            </a>
        </div>
    </x-slot>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-400 p-4 rounded-md shadow-sm mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-emerald-700 font-medium">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
        @if($announcements->isEmpty())
            <div class="p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-slate-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                </svg>
                <h3 class="text-lg font-medium text-slate-900">配信履歴がありません</h3>
                <p class="mt-2 text-sm text-slate-500">まだお知らせが配信されていません。「新規配信」ボタンから作成してください。</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">配信日時</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">タイトル</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">配信先</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">総配信数</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">既読 / 未読</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">操作</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @foreach($announcements as $announcement)
                            <tr class="hover:bg-slate-50 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                    {{ $announcement->created_at->format('Y/m/d H:i') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-900 font-medium">
                                    <div class="truncate max-w-xs">{{ $announcement->title }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($announcement->target_scope_type === 'all')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">全体</span>
                                    @elseif($announcement->target_scope_type === 'instructors')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">講師のみ</span>
                                    @elseif($announcement->target_scope_type === 'companies')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">企業のみ</span>
                                    @elseif($announcement->target_scope_type === 'students')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">生徒のみ</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 text-center">
                                    {{ number_format($announcement->total_recipients) }} 件
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                     <div class="flex items-center justify-center space-x-2 text-sm">
                                        <span class="text-emerald-600 font-medium" title="既読">{{ number_format($announcement->read_count) }}</span>
                                        <span class="text-slate-300">/</span>
                                        <span class="text-slate-400" title="未読">{{ number_format($announcement->unread_count) }}</span>
                                    </div>
                                    @if($announcement->total_recipients > 0)
                                        <div class="w-full bg-slate-200 rounded-full h-1.5 mt-1">
                                            <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ ($announcement->read_count / $announcement->total_recipients) * 100 }}%"></div>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.announcements.show', $announcement) }}" class="text-indigo-600 hover:text-indigo-900 border border-transparent group-hover:border-slate-300 px-3 py-1.5 rounded-md transition-colors">
                                        詳細
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($announcements->hasPages())
                <div class="px-6 py-4 border-t border-slate-200">
                    {{ $announcements->links() }}
                </div>
            @endif
        @endif
    </div>
</x-app-layout>
