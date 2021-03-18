<div class="sidebar" :class="[{'is-hidden': ! sidebar}]">
    <div class="box-close"><i class="hidden-desktop visible-mobile fa fa-close fa-1x jsiconlose"></i></div>
    {!! $index !!}

    {{-- Display For mobile only --}}
    <div class="footer hidden-desktop" id="footer" up-keep>
        @include('rvsitebuilder/larecipe::user.partials.dropdown')
    </div>
</div>