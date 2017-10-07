<div class="table-primary">

    <div class="table-header clearfix">
        <div class="table-caption">Channels</div>
    </div>

    <table
        class="table table-bordered datatable"
        id="channels-datatable"
    >
        <thead>
        <tr>
            <th>Name</th>
            <th>Slug</th>
            <th>Entries</th>
            <th>Updated at</th>
            <th>Active</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($channels as $channel)
            <tr>
                <td>
                    {{ $channel->name }}
                </td>
                <td>
                    @foreach($channel->translations as $translation)
                        <b>{{ strtoupper($translation->locale) }}:</b>
                        {{ $translation->slug }}
                        <br>
                    @endforeach
                </td>
                <td>
                    {{ $channel->entries()->count() }}
                </td>
                <td width="150px">
                    @if($channel->updated_at)
                        {{ $channel->updated_at->format('d.m.Y H:i') }}
                    @endif
                </td>
                <td class="text-center vertical-align-middle" style="width:100px;">
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
                </td>
                <td class="text-center vertical-align-middle" style="width:150px;">
                    <a href="{{ route('content::channels.edit', $channel) }}" class="btn btn-primary btn-xs">
                        Edit channel
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
