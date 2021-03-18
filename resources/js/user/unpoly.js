// Unpoly API: https://unpoly.com/tutorial
// Tutorial: http://triskweline.de/unpoly-rugb/#/
// Dicusstion forum: https://groups.google.com/forum/#!forum/unpoly
//
// Server side middleware:
// https://github.com/webstronauts/php-unpoly
//    - member only pages: we need to handle session expiration
//    - JSON response: to handle form validation, and etc.

// Set unpoly config
(function() {
    up.log.disable();
    // up.log.enable();
    // up.log.config.collapse = true;

    // Scrolling viewports
    // https://unpoly.com/up.viewport.config
    up.viewport.config.viewports.push('.sidebar');
    up.viewport.config.fixedTop.push('.fixed');
    up.viewport.config.anchoredRight.push('.documentation');
    up.viewport.config.revealPadding = 5;

    // When the user goes back or forward in history.
    // https://unpoly.com/up.history.config
    up.history.config.restoreScroll = false; // Serveral bugs, so we do it ourself
    up.history.config.popTargets = ['.documentation', '.sidebar'];

    // AJAX acceleration
    // https://unpoly.com/up.proxy.config
    up.proxy.config.slowDelay = 10;
    up.proxy.config.preloadDelay = 100;
    up.proxy.config.maxRequests = 2;

    // To render Larecipe's Vue Component, and etc.
    window.LaRecipe = new CreateLarecipe(config);
    LaRecipe.run();

    // Keep .current URL before switch to the new one
    // Global variable here to maitain its value
    var previousMenuUrl = '';
    window.previousMenuUrl = previousMenuUrl;

    // if browser back to the 404 page, it will cause promise error. and cannot continue
    window.addEventListener(
        'unhandledrejection',
        event => {
            if (!window.location.hash) {
                window.location = window.location + '#loaded';
                window.location.reload();
            }

            event.preventDefault();
        },
        false
    );

    //Onload this function
}.call(this));

// Mark external URL on the menu, external URL will do normal HTTP
up.macro('.sidebar a[href^="http"], .sidebar #footer a', function(element) {
    element.setAttribute('external-url', true);
});

// Auto set up-target on all internal link sidebar menu
up.macro('.sidebar a', function(element) {
    if (!element.hasAttribute('external-url')) {
        // Container that fragment will be replaced
        element.setAttribute('up-dash', '.documentation');
        element.setAttribute('up-fail-target', '.documentation');

        // https://unpoly.com/up.morph#named-transitions
        element.setAttribute('up-transition', 'cross-fade');
        element.setAttribute('up-duration', '250');
    }
});

// Auto set up-target on #topnav drop-down (desktop), but not #footer drop-down (mobile)
// And do not re-render #topnav and #footer. It will destroy Vue.JS drop-down components.
up.macro('#topnav a[up-version], #topnav a[up-lang]', function(element) {
    element.setAttribute('up-dash', '.documentation, .sidebar');
    element.setAttribute('up-fail-target', '.documentation');

    element.setAttribute('up-transition', 'move-to-bottom/fade-in', {
        easing: 'ease-in-out'
    });
    // element.setAttribute('up-duration', '300');
});

up.compiler('.sidebar', function(element) {
    var linkElement = currentAhrefElement();

    // Set CSS to current list
    removeCurrentListMenu();

    if (up.util.isElement(linkElement)) {
        setCurrentListMenu(linkElement);

        // Scroll sidebar menu to <a> link if it is not visible
        if (!isInViewport(linkElement)) {
            let rect = linkElement.getBoundingClientRect();
            up.scroll(up.viewport.closest(linkElement), rect.top).catch(error =>
                console.error(error)
            );
        }
    }
});

up.compiler('.documentation', function(element) {
    // always hide sidebar, when document is laoding
    toggleSidebarMenu(true);

    LaRecipe.app.addLinksToHeaders();
    LaRecipe.app.setupSmoothScrolling();
    LaRecipe.app.parseDocsContent();
    // LaRecipe.app.handleSidebarVisibility(); // disable as it does not work
    // LaRecipe.app.activateCurrentSection(); // disable as it only set but not remove
    // LaRecipe.app.setupKeyboardShortcuts(); // disable as it does not worth memory usage
    // mediumZoom('.documentation img'); // cannot access mediumZoom loading inside LaRecipe.js

    Prism.highlightAll();

    function myScroll() {
        let targetUrl = up.history.url();
        let hash = up.util.parseUrl(targetUrl).hash;

        if (!up.util.isBlank(hash)) {
            return up.revealHash(hash);
        } else {
            return up.scroll(up.viewport.root(), 0);
        }
    }

    myScroll().catch(error => console.error(error));
});

// if switching version fail, need to reload .sidebar by call to known URL
up.compiler('#f20e0c25-d928-42cc-98d1-13cc230663ea', function() {
    // Run first to update currentVersion, currentLang
    dynamicDropdownLinks();

    var reloadUrl =
        larecipeRoute + '/' + currentVersion + '/' + currentLang + '/index';

    console.log('Reload .sidebar from: ' + reloadUrl);

    up.replace('.sidebar', reloadUrl, {
        history: false,
        failTarget: '#displayNone'
    })
        .then(() => toggleSidebarMenu(true))
        .catch(e => {});

    return function() {};
});

// Toggle hamburger menu on mobile, on desktop always on
up.compiler('#topnav', function() {
    up.on('click', 'i.jswitch', function() {
        toggleSidebarMenu(false);
    });

    up.on('click', 'i.jsiconlose', function() {
        toggleSidebarMenu(true);
    });
});

//TODO: disqus forum comment
up.compiler('#disqus_thread', function() {
    if (typeof DISQUS != 'undefined') {
        DISQUS.reset({
            reload: true,
            config: function() {
                this.language = currentLang;
                this.page.identifier = '/' + currentLang + currenPage;
                this.page.url = window.location;
            }
        });
    }
});

up.on('up:link:follow', function() {
    removeCurrentListMenu();
    setCurrentListMenu(event.target);
});

up.on('up:history:restored', function() {
    toggleSidebarMenu(true);
});

up.on('up:history:pushed', function() {
    // Change drop-down <a> on #topnav to new url
    let currentUrl = up.util.parseUrl(event.url).pathname;
    dynamicDropdownLinks(currentUrl);
});

// Unpoly does not manage state for the hashed URL. We need to do it manually
window.onpopstate = function(event) {
    var targetUrl = up.history.url();
    var hash = up.util.parseUrl(targetUrl).hash;

    // Non-hash URL is handling properly on up:history
    if (up.util.isBlank(hash)) {
        return function() {};
    }

    var pathname = up.util.parseUrl(targetUrl).pathname;
    var currentElement = currentAhrefElement();

    // Run removeCurrentListMenu() to update `previousMenuUrl` global var before compare it
    if (up.util.isDefined(currentElement)) {
        removeCurrentListMenu();
        setCurrentListMenu(currentElement);
    }

    // To the hash from the different page, need to force restore
    if (pathname != up.util.parseUrl(previousMenuUrl).pathname) {
        console.log(
            '++ Scroll 1: Browser history to hash URL but on DIFFERENT page from %s to %s. need to force restore.',
            previousMenuUrl,
            targetUrl
        );

        function mySidebarScroll() {
            // Scroll sidebar menu to <a> link position if it is not visible
            if (!isInViewport(currentElement)) {
                let rect = currentElement.getBoundingClientRect();
                let scrollTo = rect.top;
                console.log('Menu is not visible, scroll to ' + scrollTo);

                return up.scroll(up.viewport.closest(currentElement), scrollTo);
            }

            return Promise.resolve(true);
        }

        function myRevealHash() {
            if (!up.util.isBlank(hash)) {
                return up.revealHash(hash);
            } else {
                return up.scroll(up.viewport.root(), 0);
            }
        }

        // mySidebarScroll().catch(error => console.error(error));
        up.replace('.documentation', targetUrl)
            .then(() => mySidebarScroll())
            .then(() => myRevealHash())
            .catch(e => {});

        dynamicDropdownLinks();
    } else {
        // To the hash from the same page
        console.log(
            '++ Scroll 2: Browser history to hash URL and on SAME page to %s',
            targetUrl
        );
        up.revealHash(hash);
    }

    return function() {};
};

// Remove a class: element.classList.toggle("classToRemove", false);
// Add a class: element.classList.toggle("classToAdd", true);
function toggleSidebarMenu(forceHide) {
    let sidebar = up.element.get('.sidebar');
    sidebar.classList.toggle('is-hidden', !!forceHide);
}

// Whenever switching languages and version, need to update drop-down link and button name
function dynamicDropdownLinks(target = window.location.pathname) {
    console.log('Set drop-down links to ' + target);

    let routePath = up.util.parseUrl(larecipeRoute);
    var page = target.replace(routePath.pathname + '/', '').split('/');
    var [pageVersion, pageLang] = page.splice(0, 2);
    var currentPage = page.join('');

    // for version drop-down
    var dropDownLinks = up.element.all('a[up-version]');
    if (up.util.isDefined(dropDownLinks)) {
        var i;
        for (i = 0; i < dropDownLinks.length; i++) {
            let dropDown = dropDownLinks[i].getAttribute('up-version');
            dropDownLinks[i].setAttribute(
                'href',
                larecipeRoute +
                    '/' +
                    dropDown +
                    '/' +
                    pageLang +
                    '/' +
                    currentPage
            );
        }
    }

    // for language drop-down
    var dropDownLinks = up.element.all('a[up-lang]');
    if (up.util.isDefined(dropDownLinks)) {
        var i;
        for (i = 0; i < dropDownLinks.length; i++) {
            let dropDown = dropDownLinks[i].getAttribute('up-lang');
            dropDownLinks[i].setAttribute(
                'href',
                larecipeRoute +
                    '/' +
                    pageVersion +
                    '/' +
                    dropDown +
                    '/' +
                    currentPage
            );
        }
    }

    // Change name on the list button to new one
    if (currentVersion != pageVersion) {
        up.element.get('#currentVersion').innerHTML =
            larecipeVersion[pageVersion];
        currentVersion = pageVersion;
    }

    if (currentLang != pageLang) {
        up.element.get('#currentLang').innerHTML = larecipeLang[pageLang];
        currentLang = pageLang;
    }

    return function() {};
}

// https://www.javascripttutorial.net/dom/css/check-if-an-element-is-visible-in-the-viewport/
function isInViewport(element) {
    const rect = element.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <=
            (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <=
            (window.innerWidth || document.documentElement.clientWidth)
    );
}

// Remove active class on li menu if exist
function removeCurrentListMenu() {
    let oldElements = up.element.all('li.active');
    if (up.util.isDefined(oldElements)) {
        var i;
        for (i = 0; i < oldElements.length; i++) {
            up.element.toggleClass(oldElements[i], 'active');
            let alink = up.element.first(oldElements[i], 'a');
            alink.style.removeProperty('font-weight');

            // Update `previousMenuUrl` to recheck in case browser back
            let href = oldElements[i].querySelector('a').getAttribute('href');
            previousMenuUrl = up.util.normalizeUrl(href);
        }
    }
}

// Set active class on li menu on the current one
function setCurrentListMenu(element) {
    let liElement = up.element.closest(element, 'li');
    if (up.util.isDefined(liElement)) {
        up.element.toggleClass(liElement, 'active');
        element.style.fontWeight = 'bold';
    }
}

function currentAhrefElement() {
    let currentUrl = window.location.pathname.match(/[^/]+$/g)[0];
    return up.element.get(`.sidebar li a[href="${currentUrl}"]`);
}
