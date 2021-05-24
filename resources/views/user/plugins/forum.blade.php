@if(config('rvsitebuilder.larecipe.forum.enabled'))

@if(!isset($rvStatusCode) || $rvStatusCode != '404')

    @if(config('rvsitebuilder.larecipe.forum.default') == 'disqus' && config('rvsitebuilder.larecipe.forum.services.disqus.site_name'))
    <p>&nbsp;</p>
    <hr>
    @if($currentLang != 'en')
        <div class="alert" style=" background-color:bisque;">
            <i class="fa fa-exclamation"></i>
            <small>
                Our staffs only understand English. Comments in other languages will be translated to English and reply back in the same language using Google Translate. We are really sorry if it sounds strange to you.
            </small>
        </div>
    @endif

    <div id="disqus_thread"></div>

    <script nonce="{{ csrf_token() }}">
        if(typeof DISQUS === "undefined"){
        var disqus_shortname = '{{ config('rvsitebuilder.larecipe.forum.services.disqus.site_name') }}';

        (function() {
            var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
            dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
            (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
        })();
    } else {
        DISQUS.reset({
          reload: true,
          config: function () {
            this.language =  '{{$currentLang}}';
            this.page.url = '{{ url($canonical) }}';
            this.page.identifier = '/{{ $currentSection }}';
          }
        });
    }
    </script>
    <noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
    @endif

@endif
@endif

