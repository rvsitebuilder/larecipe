@extends('rvsitebuilder/larecipe::user.layouts.default')

@section('content')
<div>
    @include('rvsitebuilder/larecipe::user.partials.sidebar')
    
    <div class="documentation is-{{ config('rvsitebuilder/larecipe.ui.code_theme') }}" :class="{'expanded': ! sidebar}">
        {{ $content }}
     
        @include('rvsitebuilder/larecipe::user.plugins.forum')
    </div>
</div>
@endsection
