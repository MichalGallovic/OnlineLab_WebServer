<ul>
    @foreach(config('localization.langs') as $code  =>  $language)
        <li>
            @if($code != App::getLocale())
                <a href="{{ route('lang.switch',$code) }}">
                    <img src="{{ Module::asset('localization:images/'.$code.'.png') }}" alt="">
                </a>
            @else
                <a class="selected" href="{{ route('lang.switch',$code) }}">
                    <img src="{{ Module::asset('localization:images/'.$code.'.png') }}" alt="">
                </a>
            @endif
    @endforeach
</ul>