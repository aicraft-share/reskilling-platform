<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            MTG管理（全件一覧）
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-slate-900">

                    @if($meetings->isEmpty())
                        <div class="text-center py-10 text-slate-500">
                            MTGはまだ作成されていません。
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                            <a href="{{ route('admin.meetings.index', array_merge(request()->query(), ['sort' => 'scheduled_at', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                                                class="group inline-flex items-center space-x-1 hover:text-slate-700">
                                                <span>開催日時 (JST)</span>
                                                @if(request('sort') === 'scheduled_at' || !request('sort'))
                                                    <span
                                                        class="text-blue-600">{{ request('direction') === 'asc' ? '↑' : '↓' }}</span>
                                                @else
                                                    <span
                                                        class="text-slate-300 opacity-0 group-hover:opacity-100 transition">↕</span>
                                                @endif
                                            </a>
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                            <a href="{{ route('admin.meetings.index', array_merge(request()->query(), ['sort' => 'title', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                                                class="group inline-flex items-center space-x-1 hover:text-slate-700">
                                                <span>タイトル</span>
                                                @if(request('sort') === 'title')
                                                    <span
                                                        class="text-blue-600">{{ request('direction') === 'asc' ? '↑' : '↓' }}</span>
                                                @else
                                                    <span
                                                        class="text-slate-300 opacity-0 group-hover:opacity-100 transition">↕</span>
                                                @endif
                                            </a>
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                            <a href="{{ route('admin.meetings.index', array_merge(request()->query(), ['sort' => 'company_name', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                                                class="group inline-flex items-center space-x-1 hover:text-slate-700">
                                                <span>企業</span>
                                                @if(request('sort') === 'company_name')
                                                    <span
                                                        class="text-blue-600">{{ request('direction') === 'asc' ? '↑' : '↓' }}</span>
                                                @else
                                                    <span
                                                        class="text-slate-300 opacity-0 group-hover:opacity-100 transition">↕</span>
                                                @endif
                                            </a>
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                            主催者 (講師)</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                            参加人数</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                            所要時間</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-200">
                                    @foreach($meetings as $meeting)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                                                {{ $meeting->scheduled_at->format('Y/m/d H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">
                                                {{ $meeting->title }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                                {{ $meeting->company?->name ?? '(削除済み企業)' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                                {{ $meeting->creator?->name ?? '(削除済みユーザー)' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $meeting->participants->count() }}名
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                                {{ $meeting->duration_minutes }}分
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $meetings->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>