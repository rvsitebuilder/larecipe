// From: https://github.com/jeffreyguenther/vue-turbolinks/issues/16#issuecomment-369136841
// Author: Max Melentiev
// https://github.com/printercu

// Inspired by
// https://github.com/jeffreyguenther/vue-turbolinks/blob/master/index.js
// but changed to support going back to cached page with vue instance.
// Original version will keep instance created from cache along with new one.
var plugin;

plugin = {
    instances: [],
    bind: function() {
        // Destroy instances on current page when moving to another.
        document.addEventListener('up:link:follow', function() {
            console.log('****** up:link:follow ******');
            return plugin.cleanupInstances();
        });
        // Destroy left instances when previous page has disabled caching.
        document.addEventListener('up:proxy:loaded', function() {
            console.log('****** up:proxy:loaded ******');
            return plugin.cleanupInstances();
        });
        // Clear instances on curent page which are not present anymore.
        return document.addEventListener('up:fragment:inserted', function() {
            return plugin.cleanupInstances(function(x) {
                console.log('****** up:fragment:inserted ******');
                return document.contains(x.$el);
            });
        });
    },
    cleanupInstances: function(keep_if) {
        var i, instance, len, ref, result;
        result = [];
        ref = plugin.instances;
        for (i = 0, len = ref.length; i < len; i++) {
            instance = ref[i];
            if (typeof keep_if === 'function' ? keep_if(instance) : void 0) {
                result.push(instance);
            } else {
                instance.$destroy();
            }
        }
        return (plugin.instances = result);
    },
    Mixin: {
        beforeMount: function() {
            // If this is the root component, we want to cache the original element contents to replace later
            // We don't care about sub-components, just the root
            if (this === this.$root && this.$el) {
                plugin.instances.push(this);
                this.$unpolyCachedHTML = this.$el.outerHTML;
                // console.log(this.$unpolyCachedHTML);
                // console.log('*******************************');

                // register root hook to restore original element on destroy
                this.$once('hook:destroyed', function() {
                    this.$el.outerHTML = this.$unpolyCachedHTML;
                });
            }
        }
    },
    install: function(Vue, _options) {
        plugin.bind();
        return Vue.mixin(plugin.Mixin);
    }
};

export default plugin;
// // or:
// Vue.UnpolyAdapter = plugin;
// plugin.bind();
