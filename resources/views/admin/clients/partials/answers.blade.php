{{-- Rows from App\Support\FormSchema::present() --}}
<table class="table table-bordered table-sm mb-0 align-middle">
    @foreach($rows as $row)
        <tr>
            <th class="bg-light" style="width:40%">{{ $row['label'] }}</th>
            <td>
                @if(is_array($row['rows']))
                    @if(count($row['rows']))
                        @include('admin.clients.partials.answers', ['rows' => $row['rows']])
                    @else
                        <span class="text-muted">-</span>
                    @endif
                @else
                    {!! filled($row['value']) ? nl2br(e($row['value'])) : '<span class="text-muted">-</span>' !!}
                @endif
            </td>
        </tr>
    @endforeach
</table>
