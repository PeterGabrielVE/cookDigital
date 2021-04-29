<ul>
    @if(count($messages) > 0)
        @foreach($messages as $message)
            @if($message->from != Auth::user()->id)
                <li class="left">
                    {{ $message->message }}
                </li>
            @else
                <li class="right">
                    {{ $message->message }}
                </li>
            @endif
        @endforeach
    @else
        <li>{{__('No Message Found..!')}}</li>
    @endif
</ul>
