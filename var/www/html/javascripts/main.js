var theView = function(currentPage, homePageName) {
    that = this;

    this.properties = {
                        'currentPage'   :  this.properties != undefined ? this.properties.currentPage : currentPage,
                        'homePageName'  :  this.properties != undefined ? this.properties.homePageName : homePageName,
                        'contentUrl'    : ''
    };
    this.viewObjects = {
                        '$body'             : $('body'),
                        '$header'           : $('#header'),
                        '$content'          : $('#content'),
                        '$contentInfo'      : $('#contentInfo'),
                        '$footer'           : $('#footer'),
                        '$mainNavLinks'     : $('#mainNavigation a')
    };
    this.initialise = function() {
        that.setMainNavLinkEvents();
        that.checkUrlForHash();
        if (that.properties.currentPage == 'credits') {
            that.setCreditsLinksEvents();
        }
        else if (that.properties.currentPage == 'gallery') {
            that.setGalleryImageLinkEvents();
        }
    }
    this.setMainNavLinkEvents = function() {
        that.viewObjects.$mainNavLinks.each(function() {
            var thisEl = $(this);
            thisEl.attr('id', thisEl.text().toLowerCase() + 'Link');
        });

        that.viewObjects.$mainNavLinks.click(function() {
            var thisEl                  = $(this);
            var pageType                = thisEl.text().toLowerCase();
            that.properties.currentPage = pageType;
            that.properties.contentUrl  = '/' + pageType + '/' + pageType + 'Content.php';

            console.log('click');
            that.startLoading( that.getNewContent );
            
            return false;
        });
    }
    this.setCreditsLinksEvents = function() {
        $('#creditLinks').addClass('remove-bottom');
        $('#creditLinks a').each(function() {
            var $thisEl     = $(this);
            var sectionName = $thisEl.attr('href').substring(1);
            $thisEl.attr('id', sectionName + 'Link');
            if (sectionName == 'cast') { 
                $thisEl.addClass('selectedLink');
            }
        });
        $('#creditLinks a').click(function(event) {
            var $thisEl = $(this);
            var tableId = $thisEl.attr('href').substring(1);

            if (tableId == 'all') {
                $('#content').css('height', 'auto');
                $('.creditsTable').each(function() {
                    $(this).fadeIn(1000);
                });
            }
            else {
                $('.creditsTable').each(function() {
                    $(this).fadeOut();
                }).promise().done(function() {
                        $('#content').css('height', 'auto');
                        $('#'+tableId).fadeIn(1000);
                    });
                
            }

            $('#creditLinks a').each(function() {
                $(this).removeClass('selectedLink');
            });
            $('#'+tableId+'Link').addClass('selectedLink');

            return false;
        });
    }
    this.setGalleryImageLinkEvents = function() {
        
        $('.shadowWrapper a').mouseover(function() {
            $(this).stop(true,false).animate({'opacity':1.0}, 100);
            $('.shadowWrapper a').not(this).stop(true,false).animate({'opacity':0.3}, 1000);
        });
        $('.shadowWrapper a').mouseout(function() {
            $('.shadowWrapper a').not(this).stop(true,false).animate({'opacity':1.0}, 1000);
        });
        
        that.setFancyBox();
    }
    this.startLoading = function(callbackFunction) {
        console.log('startLoading');
        $('html, body').animate({scrollTop:0}, 'slow', function() {
            var callBackFnc = callbackFunction;
            that.viewObjects.$content.css('height', that.viewObjects.$content.css('height'));
            
            that.viewObjects.$contentInfo.stop(true,false).animate({'opacity':0}, 300, function () {

                that.switchPageClassType();
                that.setContentWidth(callBackFnc);
            });
        });
    }
    this.stopLoading = function() {
        that.viewObjects.$contentInfo.animate({'opacity':1}, 1000);
        $('#content').animate({
            'height' : that.viewObjects.$contentInfo.css('height')
        }, 1000);
        $('h1:first').animate({'opacity':0}, 1000, function () {
            $('h1:first').animate({'opacity':1.0},1000);
        });
        if (that.properties.currentPage == 'about' 
            || that.properties.currentPage == 'credits' 
            || that.properties.currentPage == 'contact') {

            that.viewObjects.$body.animate({
                'background-color' : '#eee',
                'color' : '#111'
            }, 1000);
            that.viewObjects.$content.animate({
                'color' : '#111'
            }, 1000);
        }
        else {
            that.viewObjects.$body.animate({
                'background-color' : '#111',
                'color' : '#eee'
            }, 1000);
            that.viewObjects.$content.animate({
                'color' : '#eee'
            }, 1000);
        }

        if (that.properties.currentPage == 'gallery') {
            that.setGalleryImageLinkEvents();
        }
        

        that.setHashUrl();
    }
    this.getNewContent = function() {
        console.log('content : ' + that.properties.currentPage);
        var contentData;
        $.ajax({
            url: that.properties.contentUrl
        }).done(function ( data ) {
            that.setNewContent(data, that.stopLoading);
        });
        return contentData;
    }
    this.setNewContent = function(contentData, callbackFunction) {
        that.viewObjects.$body.attr( 'id', that.properties.currentPage );
        $('#mainNavigation .selectedLink:first').removeClass('selectedLink');
        $('#'+that.properties.currentPage+'Link').addClass('selectedLink');

        that.viewObjects.$contentInfo.html(contentData);
        setTimeout(function(){ callbackFunction() },100);

        if (that.properties.currentPage == 'credits') {
            that.setCreditsLinksEvents();
        }
    }
    this.setContentWidth = function(callbackFunction) {
        console.log('setContentWidth');
        if (that.properties.currentPage == 'about' 
            || that.properties.currentPage == 'credits' 
            || that.properties.currentPage == 'contact') {

            $('#header, #footer').stop(true,false).animate({'width':760}, 300);
            $('#content').stop(true,false).animate({'width':760}, 300, callbackFunction);
        }
        else {
            $('#header, #footer').stop(true,false).animate({'width':900}, 300);
            $('#content').stop(true,false).animate({'width':900}, 300, callbackFunction);
        }
    }
    this.switchPageClassType = function() {
        console.log('switchPageClassType');
        if (that.properties.currentPage == 'about' 
            || that.properties.currentPage == 'credits' 
            || that.properties.currentPage == 'contact') {

            that.viewObjects.$body.switchClass('makeImageryNicer', 'makeReadingNicer', 1000);
        }
        else {
            that.viewObjects.$body.switchClass('makeReadingNicer', 'makeImageryNicer', 1000);
        }
    }
    this.setHashUrl = function() {
        var currenturl  = document.location.href;
        if (currenturl.indexOf('#') != -1) {
            var newUrl = currenturl.split('#')[0] + '#' + that.properties.currentPage + 'Page';
            document.location.href = newUrl;
        }
        else {
            var newUrl = currenturl + '#' + that.properties.currentPage +'Page';
            document.location.href = newUrl;
        }
    }
    this.checkUrlForHash = function() {
        var currenturl  = document.location.href;
        if (currenturl.indexOf('#') != -1) {
            that.properties.currentPage = currenturl.split('#')[1].split('Page')[0];
            that.properties.contentUrl  = '/' + that.properties.currentPage + '/' + that.properties.currentPage + 'Content.php';
            that.startLoading( that.getNewContent );
        }
    }
    this.setFancyBox = function() {
        $("a[rel=photo]").fancybox({
            'overlayShow'   : true,
            'transitionIn'  : 'fade',
            'transitionOut' : 'fade',
            'speedIn' : 600,
            'speedOut' : 600,
            'hideOnOverlayClick' : true,
            'overlayColor' : '#1A1919',
            'overlayOpacity' : '0.85'

        });
    }

    this.initialise();
}