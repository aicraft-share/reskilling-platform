@if($status === 'published')
    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] sm:text-xs font-medium bg-emerald-100 text-emerald-800">
        公開中
    </span>
@elseif($status === 'draft')
    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] sm:text-xs font-medium bg-slate-100 text-slate-800">
        下書き
    </span>
@else
    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] sm:text-xs font-medium bg-amber-100 text-amber-800">
        アーカイブ
    </span>
@endif
