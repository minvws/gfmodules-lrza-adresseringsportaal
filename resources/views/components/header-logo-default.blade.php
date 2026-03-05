@if(config('app.env') === "production")
<a href="/" class="le-logo logo" aria-label="{{__('Rijksoverheid logo, go to the homepage')}}">
    <img src="{{ asset('img/ro-logo.svg') }}" alt="Logo Rijksoverheid">Rijksoverheid
</a>
@else
<a href="/" class="le-logo logo" aria-label="{{__('Rijksoverheid logo, go to the homepage')}}">
    <img src="{{ asset('img/llama-dos.png') }}" alt="LLama">gfModules of Llamas
</a>
@endif
