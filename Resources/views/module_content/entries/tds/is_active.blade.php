@if($entry->type == 'draft')
    <b style="color:red;">DRAFT</b>
@else
    <input
        class="changeable-state"
        type="checkbox"
        data-render="switchery"
        data-theme="default"
        data-id="{{ $entry->id }}"
        data-model="{{ \Modules\Content\Models\Entry::class }}"
        data-switchery="true"
        {{ $entry->is_active ? 'checked' : '' }}
    />
@endif
