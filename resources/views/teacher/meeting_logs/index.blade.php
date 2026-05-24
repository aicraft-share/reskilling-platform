<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            MTG管理
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- 1. Create Form Section -->
        <div id="create" class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-slate-200">
            <div class="p-6 text-slate-900">
                <h3 class="text-lg font-bold mb-4">新規MTG作成</h3>

                <form method="POST" action="{{ route('teacher.meeting-logs.store') }}" class="space-y-4" x-data="{
                        selectedCompany: '{{ old('company_id', $selectedCompanyId ?? '') }}',
                        companies: {{ Js::from($assignedCompanies) }}
                    }">
                    @csrf

                    <!-- Company Selection -->
                    <div>
                        <x-input-label for="company_id" value="企業" />
                        <select id="company_id" name="company_id" x-model="selectedCompany"
                            class="mt-1 block w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">選択してください</option>
                            <template x-for="company in companies" :key="company.id">
                                <option :value="company.id" x-text="company.name"
                                    :selected="company.id == selectedCompany"></option>
                            </template>
                        </select>
                        <x-input-error :messages="$errors->get('company_id')" class="mt-2" />
                    </div>

                    <!-- Students Selection -->
                    <div>
                        <x-input-label for="students" value="参加生徒" />
                        <div class="mt-2 space-y-2 border p-4 rounded-md bg-slate-50 max-h-40 overflow-y-auto">
                            <template x-for="company in companies" :key="company.id">
                                <div x-show="company.id == selectedCompany">
                                    <template x-for="student in company.students" :key="student.id">
                                        <div class="flex items-center">
                                            <input type="checkbox" :id="'student_' + student.id" name="students[]"
                                                :value="student.id"
                                                class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                                :checked="{{ Js::from(old('students', isset($selectedStudentId) && $selectedStudentId ? [$selectedStudentId] : [])) }}.map(String).includes(String(student.id))">
                                            <label :for="'student_' + student.id" class="ml-2 text-sm text-slate-600"
                                                x-text="student.name"></label>
                                        </div>
                                    </template>
                                    <div x-show="!company.students.length" class="text-sm text-slate-500">
                                        この企業に所属する生徒はいません。
                                    </div>
                                </div>
                            </template>
                            <div x-show="!selectedCompany" class="text-sm text-slate-500">
                                企業を選択すると生徒一覧が表示されます。
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('students')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Title -->
                        <div>
                            <x-input-label for="title" value="MTGタイトル" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title"
                                :value="old('title')" required />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <!-- Started At -->
                        <div>
                            <x-input-label for="started_at" value="開始日時" />
                            <x-text-input id="started_at" class="block mt-1 w-full cursor-pointer" type="datetime-local"
                                name="started_at" :value="old('started_at')" required onclick="try{this.showPicker()}catch(e){}" />
                            <x-input-error :messages="$errors->get('started_at')" class="mt-2" />
                        </div>
                    </div>


                    <!-- Zoom Meeting ID (Optional) -->
                    <!-- In the previous hub logic, zoom meeting id wasn't an input because ZoomService handles it.
                         However, in original create.blade.php it was there? No, zoom_meeting_id was removed from typical MTG Logs creation since we auto-gen Zoom via API, or we leave it. Wait, the old create form had Zoom ID input but the controller ignored it. I will keep it out like in recent hub to avoid confusion. -->
                    
                    <!-- Memo -->
                    <div class="mt-4">
                        <x-input-label for="memo" value="メモ (任意)" />
                        <textarea id="memo" name="memo"
                            class="block mt-1 w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm h-20">{{ old('memo') }}</textarea>
                        <x-input-error :messages="$errors->get('memo')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <x-primary-button>
                            保存して一覧に追加
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 2. Log List Section -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-slate-200">
            <div class="p-6 text-slate-900">
                <h3 class="text-lg font-bold mb-4">直近のMTG一覧</h3>

                @if($logs->isEmpty())
                    <p class="text-slate-500 text-sm">MTGログはありません。</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        開始日時</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        タイトル</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        企業 / 参加生徒</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        Zoom</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        録画(YouTube)</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        文字起こし</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        要約</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        操作</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-200">
                                @foreach($logs as $log)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                            {{ $log->started_at->format('Y-m-d H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                                            {{ Str::limit($log->title, 20) }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-600">
                                            <div class="font-bold text-xs text-slate-500">{{ $log->company->name ?? '不明' }}</div>
                                            <div>{{ $log->students->pluck('name')->join(', ') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                            @if($log->zoom_join_url)
                                                <a href="{{ $log->zoom_join_url }}" target="_blank"
                                                    class="inline-flex items-center px-2 py-1 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded text-xs font-semibold">
                                                    参加
                                                </a>
                                            @else
                                                <span class="text-slate-400 text-xs">未発行</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                            @if($log->youtube_url)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    登録済
                                                </span>
                                            @else
                                                <span class="text-slate-400 text-xs">未登録</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                            @if($log->transcript_status === 'ready')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    登録済
                                                </span>
                                            @else
                                                <span class="text-slate-400 text-xs">未登録</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                            @if($log->transcript_summary)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    生成済
                                                </span>
                                            @else
                                                <span class="text-slate-400 text-xs">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('teacher.meeting-logs.show', $log) }}"
                                                class="inline-flex items-center px-3 py-1.5 bg-white border border-slate-300 rounded-md font-semibold text-xs text-slate-700 uppercase tracking-widest shadow-sm hover:bg-slate-50 transition ease-in-out duration-150">
                                                管理
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const startedAtInput = document.getElementById('started_at');
        if (startedAtInput && !startedAtInput.value) {
            const now = new Date();
            now.setHours(now.getHours() + 1);
            now.setMinutes(0);
            
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            
            startedAtInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;
        }
    });
</script>