
{{--
Channel: {{ $channel->name }}
Slugs:

@foreach($channel->translations as $translation)
    <b>{{ strtoupper($translation->locale) }}:</b>
    {{ $translation->slug }}
@endforeach

<br>

Updated at:

@if($channel->updated_at)
    {{ $channel->updated_at->format('d.m.Y H:i') }}
@endif

<br>

Active:

<input
    class="changeable-state"
    type="checkbox"
    data-render="switchery"
    data-theme="default"
    data-id="{{ $channel->id }}"
    data-model="{{ \Modules\Content\Models\Channel::class }}"
    data-switchery="true"
    {{ $channel->is_active ? 'checked' : '' }}
/>

<br>
--}}

<a href="{{ route('content::channels.edit', $channel) }}" class="btn btn-primary btn-xs">
    Edit channel
</a>

<a href="{{ route('content::entries.create', $channel) }}" class="btn btn-success btn-xs">
    Add new page
</a>
