<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            企業を登録
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
        <div class="p-8">
            <form method="POST" action="{{ route('admin.companies.store') }}">
                @csrf

                <div>
                    <x-input-label for="name" :value="__('企業名')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')"
                        required autofocus placeholder="株式会社Example" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email -->
                <div class="mt-6">
                    <x-input-label for="email" :value="__('メールアドレス')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                        placeholder="contact@example.com" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Business Description -->
                <div class="mt-6">
                    <x-input-label for="business_description" :value="__('事業内容')" />
                    <textarea id="business_description" name="business_description" rows="10"
                        class="block mt-1 w-full border-slate-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-sm"
                        placeholder="AIを活用したDX支援事業...">{{ old('business_description', "【事業内容】\n（提供しているサービス・製品）\n\n【従業員の主な業務内容】\n（研修対象者が日常的に行っている業務）\n\n【現在の課題】\n（業務効率・品質・人材面など）\n\n【AI・DX導入の目的】\n（今回の研修で解決したいこと）") }}</textarea>
                    <x-input-error :messages="$errors->get('business_description')" class="mt-2" />
                </div>

                <!-- Teachers -->
                <div class="mt-6">
                    <x-input-label :value="__('担当講師')" />
                    <div class="mt-2 space-y-2 max-h-48 overflow-y-auto border border-slate-200 rounded-md p-3">
                        @foreach($teachers as $teacher)
                            <label class="flex items-center gap-2 cursor-pointer hover:bg-slate-50 px-2 py-1 rounded">
                                <input type="checkbox" name="teacher_ids[]" value="{{ $teacher->id }}"
                                    class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                    {{ in_array($teacher->id, old('teacher_ids', [])) ? 'checked' : '' }}>
                                <span class="text-sm text-slate-700">{{ $teacher->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    <p class="text-xs text-slate-500 mt-1">※複数の講師を選択できます。</p>
                    <x-input-error :messages="$errors->get('teacher_ids')" class="mt-2" />
                </div>

                <!-- Status -->
                <div class="mt-6">
                    <x-input-label for="status" :value="__('ステータス')" />
                    <select id="status" name="status"
                        class="block mt-1 w-full border-slate-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-sm">
                        <option value="free_trial" {{ old('status') == 'free_trial' ? 'selected' : '' }}>無料研修中</option>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>研修中</option>
                        <option value="finished" {{ old('status') == 'finished' ? 'selected' : '' }}>修了済</option>
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                </div>

                <!-- Contract Start Date -->
                <div class="mt-6">
                    <x-input-label for="contract_start_date" :value="__('研修開始日（有料契約時）')" />
                    <x-text-input id="contract_start_date" class="block mt-1 w-full" type="date"
                        name="contract_start_date" :value="old('contract_start_date')" />
                    <p class="text-xs text-slate-500 mt-1">※無料研修中は入力不要です。</p>
                    <x-input-error :messages="$errors->get('contract_start_date')" class="mt-2" />
                </div>

                <!-- Training End Date -->
                <div class="mt-6">
                    <x-input-label for="training_end_date" :value="__('研修終了日')" />
                    <x-text-input id="training_end_date" class="block mt-1 w-full" type="date"
                        name="training_end_date" :value="old('training_end_date')" />
                    <x-input-error :messages="$errors->get('training_end_date')" class="mt-2" />
                </div>

                <!-- Contract Amount -->
                <div class="mt-6">
                    <x-input-label for="contract_amount" :value="__('契約金額（円）')" />
                    <x-text-input id="contract_amount" class="block mt-1 w-full" type="number"
                        name="contract_amount" :value="old('contract_amount')" min="0" step="1"
                        placeholder="550000" />
                    <x-input-error :messages="$errors->get('contract_amount')" class="mt-2" />
                </div>

                <!-- Payment Status -->
                <div class="mt-6">
                    <x-input-label for="payment_status" :value="__('支払い状況')" />
                    <select id="payment_status" name="payment_status"
                        class="block mt-1 w-full border-slate-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-sm">
                        <option value="not_billed" {{ old('payment_status', 'not_billed') == 'not_billed' ? 'selected' : '' }}>未請求</option>
                        <option value="billed" {{ old('payment_status', 'not_billed') == 'billed' ? 'selected' : '' }}>請求済み</option>
                        <option value="waiting_payment" {{ old('payment_status', 'not_billed') == 'waiting_payment' ? 'selected' : '' }}>支払い待ち</option>
                        <option value="paid" {{ old('payment_status', 'not_billed') == 'paid' ? 'selected' : '' }}>支払済</option>
                    </select>
                    <x-input-error :messages="$errors->get('payment_status')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end mt-8">
                    <a href="{{ route('admin.companies.index') }}"
                        class="text-sm text-slate-600 hover:text-slate-900 underline mr-4">キャンセル</a>
                    <x-primary-button>
                        {{ __('登録する') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
