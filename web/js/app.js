var NomerIoApp = (function() {
    var results = $('#results');

    var searchIndex = 0;

    function msToTime(s) {
        var ms = s % 1000;
        s = (s - ms) / 1000;
        var secs = s % 60;
        s = (s - secs) / 60;

        return secs + '.' + ms;
    }

    var avatarsList = $('#avatars .photos');
    var namesList = $('#names .names');

    var ua = navigator.userAgent.toLowerCase();
    var isAndroid = ua.indexOf("android") > -1; //&& ua.indexOf("mobile");

    var socket = io(window.location.protocol + '//' + window.location.hostname);

    return {
        socket: function() {
            return socket;
        },
        init: function (is_vip, hash) {
            socket.on('connect', function() {

            });

            socket.on('complete', function(data) {
                $('.searchStatus').html(data.view);
                if(data.isFree === 1) {
                    $('.demo').html("Краткий поиск завершен!");
                    $('.payments-info').find('span').html("Для полноценного поиска Вам необходимо <a href='/pay'>попополнить счет</a>.");
                }
            });

            socket.on('result', function(data) {
                if(typeof data.type !== "undefined") {

                    var contentEl = $('#' + data.type + ' .resultCont');
                    if((data.type === 'google' || data.type === 'facebook') && (typeof data.progress !== 'undefined')) {
                        contentEl.html("Идет поиск... " + data.progress + '%');
                    }
                    if(isNaN(data.index) || (typeof data.index === "undefined")) { data.index = 0; }
                    if(isNaN(data.indexPhoto) || (typeof data.indexPhoto === "undefined")) { data.indexPhoto = 0; }
                    if(data.index === 0) {
                        $('#' + data.type).addClass('resultNo');
                    }
                    contentEl.html(data.view);
                    if(typeof data.time !== 'undefined') {
                        $('#' + data.type + ' .sTime').html(msToTime(data.time) + " с.").show();
                    }
                    var namesEl = $('#names');
                    var photosEl = $('#avatars');
                    var p = 0;
                    if($.inArray(data.type, ["truecaller", "numbuster", "basic", "telegram", "getcontact"]) !== -1) {
                        p = parseInt(namesEl.find('.parcent').html(), 10);
                        namesEl.find('.parcent').html(p + parseInt(data.index, 10) + '%');
                    } else {
                        $('#' + data.type + ' .parcent').html(data.index + '%');
                    }
                    if(data.indexPhoto > 0) {
                        p = parseInt(photosEl.find('.parcent').html(), 10);
                        photosEl.find('.parcent').html(p + parseInt(data.indexPhoto, 10) + '%');
                    }
                    /*$('#' + data.type + ' .parcent').html(data.index + '%');*/
                    searchIndex += parseInt(data.index, 10);
                    $('#summary').find('li:eq(1) span').html(searchIndex + '%');

                } else {
                    $('.free-result').html(data.view);
                }
                if(typeof data.elements !== "undefined") {
                    for(var k in data.elements) {
                        var element = data.elements[k];
                        var img, name;
                        if(typeof element.photo !== "undefined") {
                            var li = $("<li />");

                            if(is_vip) li.addClass("s_" + data.type);

                            var href;
                            if(element.photo.startsWith("http")) {
                                href = element.photo;
                                href = href.replace("/\'\./", '');
                            } else {
                                href = "data:image/gif;base64," + element.photo;
                            }

                            var a = $("<a />").attr({
                                "class": "swipebox",
                                "href": href
                            });

                            img = $("<img />").attr({"src": href});
                            img.appendTo(a);
                            a.appendTo(li);
                            li.appendTo(avatarsList);

                            $('.swipebox').swipebox();

                            photosEl.show();
                        }
                        if(typeof element.name !== "undefined") {
                            if(is_vip === 1) {
                                name = $("<li />").html(data.type + ": " + element.name);
                            } else {
                                name = $("<li />").html(element.name);
                            }
                            if($.inArray(data.type, ["truecaller", "numbuster", "getcontact"]) != -1) {
                                name.addClass("green");
                            }

                            name.appendTo(namesList);
                            namesEl.show();
                        }
                    }
                }
                /*
                $('.results').masonry({
                    // set itemSelector so .grid-sizer is not used in layout
                    itemSelector: '.result',
                    singleMode: false,
                    isResizable: true
                });
                */
            });

            socket.on('disconnect', function() {

            });


        }
    }
})();

$("[data-action=url]").click(function() {
    var self = $(this);
    var url = self.data("url");
    var type = self.data("type");
    $.post("/url", {
        url: url,
        type: type
    }, function() {
        self.parent().remove();
    });
});

var selector = document.getElementById("selector");

var im = new Inputmask("+7 (999) 999-99-99");

var clone = null;

jQuery(document).ready(function($) {
    $('#authchoice').authchoice();



    $('.payments button').on("click", function() {
        var c = $(this).attr('class');
        $('.payment-forms > div').hide();
        $('.payment-forms > div.' + c).show();
    });

    im.mask($("[name='phone']"));
    $('.swipebox').swipebox();

    $(window).resize(function(){
        /*
        if($(window).height() > 640) {
            $("section").css("min-height", $(window).height()-$("footer").outerHeight()-$("header").outerHeight()+"px")
        } else {
            $("section").css("min-height",window.innerHeight-$("footer").outerHeight()-$("header").outerHeight()+"px")
        }
        */


        if($(window).width() > 1000){
            $("nav").removeClass("mobNav");
            $("nav ul").removeAttr("style");
        } else {
            $("nav").addClass("mobNav");
            $("nav ul").removeAttr("style");
        }

        var scrollTopPage = function(){
            if($('.searchWrap').length == 0) { return false; }
            if(clone == null) {
                clone = $('.searchWrap').clone().hide().appendTo('body');
                clone.addClass("scroll").css("position", "fixed").css("top", "0");
            }
            if($(document).scrollTop() > $(".searchWrap form").offset().top){
                clone.show();
                im.mask($("[name='phone']"));
            } else {
                clone.remove();
                clone = null;
            }
        };

        scrollTopPage();

        $(window).scroll(function(){
            scrollTopPage();
        })

    }).resize();

    $(".menu").on("click", function(){
        $(".mobNav ul").stop().slideToggle(150)
        return false
    });

    /*
    $('.results').masonry({
        // set itemSelector so .grid-sizer is not used in layout
        itemSelector: '.result',
        singleMode: false,
        isResizable: true
    });
    */
});

$(document).on( "click", ".myProfile", function(event) {
    event.stopPropagation();
    var myProfile = $(this).parent();
    if(myProfile.hasClass("myProfileOpen")) {
        myProfile.removeClass("myProfileOpen");
    } else {
        myProfile.addClass("myProfileOpen");
        /*$(".myProfileMenu").stop().slideToggle(150);*/
        $('.myProfileMenu .close').on('click', function() {
            myProfile.addClass("myProfileClose");
            myProfile.removeClass("myProfileOpen");
        });
    }
    return false
});

$(document).on( "click", ".modal-close", function(event) {
    event.stopPropagation();

    $(this).parent().removeClass("--open");
});

/*
$(window).click(function() {
    $('.myProfile').removeClass('myProfileOpen');
});
*/




$(document).on('focus', '.form-group input', function() {
    $(this).parent().addClass('focus');
});

$(document).on('blur', '.form-group input', function() {
    $(this).parent().removeClass('focus');
});

$(document).on('focus', '[name=phone]', function() {
    $(this).val('');
});

$("[data-action=search]").on('click', function() {
    var type = $(this).data('type');
    var id = $(this).data('id');
    NomerIoApp.socket().emit('search' + type, { id: id })
    $(this).parent().parent().parent().html('<p class="loading"><img src="/img/sload.gif"><span>идет поиск...</span></p>');
});

$(document).on('paste', '[name=phone]', function(e){
    var text = (e.originalEvent || e).clipboardData.getData('text/plain');
    text = text.replace(/[^0-9]/gim, '');
    if( text.charAt( 0 ) === '7' || text.charAt( 0 ) === '8' )
        text = text.slice( 1 );
    $(this).val(text);
});