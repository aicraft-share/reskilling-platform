<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('チャット: ') }} {{ $chat->student->name }} &amp; {{ $chat->instructor->name }}
        </h2>
    </x-slot>

    <div class="-m-4 -mb-28 sm:-m-6 lg:-m-8 h-[calc(100vh-64px)] lg:h-[calc(100vh-64px)] flex flex-col bg-slate-50 relative">

        <div class="flex-1 flex flex-col bg-white overflow-hidden shadow-sm">

            <!-- Chat Messages Area -->
                <div class="flex-1 p-6 overflow-y-auto bg-gray-50 space-y-4" id="chatMessages">
                    @if($messages->isEmpty())
                        <div class="text-center text-gray-500 mt-10">
                            まだメッセージはありません。
                        </div>
                    @else
                        @foreach($messages as $msg)
                            @php
                                // If the current user is a participant, align their messages to the right
                                if ($user->id === $chat->student_id || $user->id === $chat->instructor_id) {
                                    $isRight = $msg->sender_id === $user->id;
                                } else {
                                    // For admins/company viewers, align student messages to the right and instructors to the left
                                    $isRight = $msg->sender_id === $chat->student_id;
                                }
                            @endphp
                            <div class="flex w-full {{ $isRight ? 'justify-end' : 'justify-start' }} mb-2">
                                <div class="flex flex-col max-w-[75%] {{ $isRight ? 'items-end' : 'items-start' }}">
                                    <!-- Sender Name (Only for others or admins viewing) -->
                                    @if(!$isRight || $user->isAdmin() || $user->isCompany())
                                        <div class="text-[11px] text-gray-500 mb-1 ml-1 mt-2">
                                            {{ $msg->sender->name }}
                                        </div>
                                    @endif

                                    <div class="flex items-end {{ $isRight ? 'flex-row-reverse' : 'flex-row' }}">
                                        <div
                                            class="rounded-2xl px-4 py-2.5 text-[14px] leading-relaxed shadow-sm {{ $isRight ? 'bg-blue-600 text-white' : 'bg-white border border-gray-200 text-slate-800' }} inline-block break-words w-fit">
                                            {!! nl2br(e(trim($msg->message))) !!}
                                        </div>

                                        <!-- Time (Placed outside bottom) -->
                                        <div
                                            class="text-[10px] text-gray-400 whitespace-nowrap pb-1 {{ $isRight ? 'mr-2 text-right' : 'ml-2 text-left' }}">
                                            {{ $msg->created_at->format('H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <!-- Chat Input Form (Admins, Students, and Instructors) -->
                @if($user->isAdmin() || $user->isStudent() || $user->isTeacher() || $user->isInstructor())
                    <div class="p-4 bg-white border-t border-gray-200">
                        <form id="chatForm" action="{{ route('chats.messages.store', $chat) }}" method="POST">
                            @csrf
                            <div class="flex space-x-3">
                                <textarea id="messageInput" name="message" rows="2"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm resize-none"
                                    placeholder="メッセージを入力..." required></textarea>
                                <div class="flex items-end">
                                    <x-primary-button id="submitBtn" type="submit"
                                        class="bg-blue-600 hover:bg-blue-700">送信</x-primary-button>
                                </div>
                            </div>
                            @error('message')
                                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </form>
                    </div>
                @else
                    <div class="p-4 bg-gray-100 border-t border-gray-200 text-center text-gray-500 text-sm">
                        ※ 現在のアカウント権限ではチャットの閲覧のみ可能です。
                    </div>
                @endif
            </div>
        </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Auto-scroll to bottom of chat
            var chatContainer = document.getElementById('chatMessages');
            if (chatContainer) {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }

            // Enter key to submit, Shift+Enter for new line
            var messageInput = document.getElementById('messageInput');
            if (messageInput) {
                messageInput.addEventListener('keydown', function (e) {
                    // IME入力中（変換中）のEnterは送信しない
                    if (e.isComposing) {
                        return;
                    }
                    
                    if (e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault(); // Prevent default new line behavior
                        if (messageInput.value.trim() !== '') {
                            document.getElementById('submitBtn').click();
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>