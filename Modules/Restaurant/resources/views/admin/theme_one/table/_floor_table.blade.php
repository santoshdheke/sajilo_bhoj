@php
    $top      = $ft->chairs->where('side','top');
    $bot      = $ft->chairs->where('side','bottom');
    $lft      = $ft->chairs->where('side','left');
    $rgt      = $ft->chairs->where('side','right');
    $total    = $ft->chairs->count();
    $photos   = $ft->photos->map(fn($p) => ['id' => $p->id, 'url' => Storage::url($p->path)]);
@endphp
<div class="floor-table" id="ft-{{ $ft->id }}" data-id="{{ $ft->id }}"
     data-name="{{ $ft->name }}"
     data-description="{{ $ft->description ?? '' }}"
     data-photos="{{ htmlspecialchars($photos->toJson(), ENT_QUOTES) }}"
     style="left:{{ $ft->pos_x }}px; top:{{ $ft->pos_y }}px;">
    <div class="ft-wrap">
        <div class="chair-zone top-zone" data-tid="{{ $ft->id }}" data-side="top">
            @foreach($top as $c)
            <div class="chair-tok" data-cid="{{ $c->id }}" title="Click to remove"
                 onclick="removeChair({{ $c->id }},this)">
                <i class="bi bi-x ct-del"></i>
            </div>
            @endforeach
            <div class="drop-hint"><i class="bi bi-plus"></i></div>
        </div>
        <div class="ft-mid">
            <div class="chair-zone lft-zone" data-tid="{{ $ft->id }}" data-side="left">
                @foreach($lft as $c)
                <div class="chair-tok" data-cid="{{ $c->id }}" title="Click to remove"
                     onclick="removeChair({{ $c->id }},this)">
                    <i class="bi bi-x ct-del"></i>
                </div>
                @endforeach
                <div class="drop-hint"><i class="bi bi-plus"></i></div>
            </div>
            <div class="ft-body">
                <button class="ft-del" onclick="deleteTable({{ $ft->id }})" title="Delete table">&times;</button>
                <button class="ft-edit" title="Edit table"
                        onclick="openTableSettings({{ $ft->id }}, this.closest('.floor-table').dataset.name, this.closest('.floor-table').dataset.photos)">
                    <i class="bi bi-pencil-fill"></i>
                </button>
                <span class="ft-name">{{ $ft->name }}</span>
                <span class="ft-seats">{{ $total }} seats</span>
            </div>
            <div class="chair-zone rgt-zone" data-tid="{{ $ft->id }}" data-side="right">
                @foreach($rgt as $c)
                <div class="chair-tok" data-cid="{{ $c->id }}" title="Click to remove"
                     onclick="removeChair({{ $c->id }},this)">
                    <i class="bi bi-x ct-del"></i>
                </div>
                @endforeach
                <div class="drop-hint"><i class="bi bi-plus"></i></div>
            </div>
        </div>
        <div class="chair-zone bot-zone" data-tid="{{ $ft->id }}" data-side="bottom">
            @foreach($bot as $c)
            <div class="chair-tok" data-cid="{{ $c->id }}" title="Click to remove"
                 onclick="removeChair({{ $c->id }},this)">
                <i class="bi bi-x ct-del"></i>
            </div>
            @endforeach
            <div class="drop-hint"><i class="bi bi-plus"></i></div>
        </div>
    </div>
</div>
