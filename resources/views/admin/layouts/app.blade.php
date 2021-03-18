@extends('admin.layouts.master')

@section('leftmenu')
    @include('admin.includes.leftmenu', ['package_name' => "rvsitebuilder/larecipe"])
@endsection

@push('package-styles')
    <!-- Larecipe package-styles -->
    {{ style(@mixcdn('css/uikitv2.css', 'vendor/rvsitebuilder/wysiwyg')) }}
    {{ style(mix('css/bootstrap.css', 'vendor/rvsitebuilder/core')) }}
 
@endpush 

@push('package-scripts')
    <!-- Larecipe package-scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js"
         integrity="sha256-CjSoeELFOcH0/uxWu6mC/Vlrc1AARqbm/jiiImDGV3s=" 
         crossorigin="anonymous"></script>

         {{ script(mix('js/bootstrap-vue-axios.js', 'vendor/rvsitebuilder/core')) }}    

@endpush

