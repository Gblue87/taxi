
jQuery.fn.disablePageScroll = function(offset) {
    if (typeof offset == "undefined")
        offset = 30;
    var evts = 'mousewheel.cmessy DOMMouseScroll.cmessy';
    $(this).unbind(evts).bind(evts, function(e) {
        var e0 = e.originalEvent,
            delta = e0.wheelDelta || -e0.deltaY || -e0.detail;
        this.scrollTop += ((delta < 0) ? 1 : -1) * offset;
        e.preventDefault();
    });
};

$(function() {
    $('.picker__holder').disablePageScroll(31);
});

setTimeout(function(){$('.car').removeClass('incoming')}, 500);
// Dropdown scroller click fix
$('body').on('mousedown','.tf-menu',function(){ return false;});

$(function(){
if ($('#date').length){
    (function(){

        var minTime = [0, 0],
            maxTime = [23, 55],
            minReturnTime = 15, // minutes
            orderTime = 10, // hours

        seconds = function(secs) {
            return secs * 1000;
        },

        minutes = function(mins) {
            return mins * seconds(60);
        },

        hours = function(hours) {
            return hours * minutes(60);
        },

        today = function() {
            var date = new Date();
            date.setHours(0);
            date.setMinutes(0);
            date.setSeconds(0);
            date.setMilliseconds(0);
            return date;
        },

        yesterday = function() {
            return new Date(today().getTime() - hours(24));
        },

        tomorrow = function() {
            return new Date(today().getTime() + hours(24));
        },

        enabledFrom = function(offsetMins) {
            if (!offsetMins) offsetMins = 0;
            return new Date(new Date().getTime() + hours(orderTime) + minutes(offsetMins));
        },

        lastDisabledDay = function(offsetMins) {
            var date = enabledFrom(offsetMins) - hours(24);
            date = new Date(date);
            date.setHours(0);
            date.setMinutes(0);
            date.setSeconds(0);
            date.setMilliseconds(0);
            return date;
        },

        firstAvailableDay = function(offsetMins) {
            return new Date(lastDisabledDay(offsetMins).getTime() + hours(24));
        },

        disableDatesTo = function(picker, to) {
            picker.component.item.disable = [];
            picker.set('disable', [{
                from: [0, 0, 0],
                to: to
            }]);
        },

        dateOptions = {
            format: 'dd/mm/yyyy',
            formatSubmit: 'yyyy-mm-dd',
            monthsFull: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            weekdaysFull: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
            weekdaysShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            today: 'Today',
            clear: 'Clear',
            close: 'Close'
        },

        timeOptions = {
            format: 'hh:i A',
            formatSubmit: 'HH:i',
            interval: 5,
            min: minTime,
            max: maxTime,
            clear: 'Clear',
        },
        date1 = $('#date').pickadate(dateOptions).pickadate('picker'),
        time1 = $('#time').pickatime(timeOptions).pickatime('picker');

        date1.on({

            open: function() {
                disableDatesTo(date1, lastDisabledDay());
            },

            set: function(set) {
                if (typeof set.select != "number")
                    return;

                var fday = firstAvailableDay().getTime();

                if (set.select < fday) {
                    date1.trigger('open');
                    date1.set('select', fday);
                }
            }
        });

        time1.on({

            open: function() {

                try {
                    date1.set('select', date1.get('select').pick);
                } catch (e) {
                    console.log('wrong date');
                    return;
                }

                var date = date1.get('select').pick,
                    from = enabledFrom(),
                    fday = firstAvailableDay().getTime();

                if (date == fday) {
                    var hours = from.getHours(),
                        mins = from.getMinutes();

                    mins = Math.round(mins / 5) * 5;

                    time1.set('min', [hours, mins]);
                    time1.set('select', [hours, mins]);

                } else
                    time1.set('min', minTime);
            }

        });

        if ($('#date2').length) {

            var time2 = $('#time2').pickatime(timeOptions).pickatime('picker'),

                date2 = $('#date2').pickadate(dateOptions).pickadate('picker').on({

                open: function() {
                    var ts = date1.get('select');
                    if ((ts === null) || (typeof ts.pick == "undefined")) {
                        disableDatesTo(date2, lastDisabledDay(minReturnTime));
                        return;
                    }

                    ts = ts.pick;

                    var tts = time1.get('select');
                    if ((tts === null) || (typeof tts.pick == "undefined")) {
                        disableDatesTo(date2, new Date(ts - hours(24)));
                        return;
                    }

                    tts = ts + minutes(tts.pick + minReturnTime);
                    var tts2 = ts + hours(maxTime[0]) + minutes(maxTime[1]);

                    if (tts > tts2) {
                        disableDatesTo(date2, new Date(ts));
                        return;
                    }

                    disableDatesTo(date2, new Date(ts - hours(24)));
                },

                set: function(set) {
                    if (typeof set.select != "number")
                        return;

                    var ts = date1.get('select');
                    if ((ts === null) || (typeof ts.pick == "undefined")) {
                        time2.set('min', minTime);
                        return;
                    }

                    ts = ts.pick;

                    if (set.select == ts){
                        var tts = time1.get('select');
                        if ((tts === null) ||
                            (typeof tts.pick == "undefined")
                        ) {
                            time2.set('min', minTime);
                            return;
                        }
                        tts = ts + minutes(tts.pick + minReturnTime);
                        time2.set('min', new Date(tts));

                    } else
                        time2.set('min', minTime);
                }
            });
        }

        var $ps = $('#passengers');

        if ($ps.length) {
            $ps.pickatime({
                format: 'h',
                min: [1, 0],
                max: [8, 0],
                interval: 60,
                klass: {
                    buttonClear: 'hidden'
                }
            }).pickatime('picker').set('select', [$ps.val(), 0]);
        }
    })();
}

$('form input:checkbox, form input:radio, form select').transForm();

$('.disabling').on('change',function(){
    //console.log($(this).closest('.ffield').find('input'));
    if($(this).is(':checked'))  {
        $(this).parent().next('.ffield').find('input').attr('disabled',false)
    } else {
        $(this).parent().next('.ffield').find('input').attr('disabled',true)
    }
});

$('.index-top a.mbtn.mbtn-orange.mbtn-big').on('click',function(e){
    e.preventDefault();
    $('.car').addClass('moveout');
    setTimeout(function(){
        $('.index-top form').submit();
    }, 1200);
});

$('.slider-wrapper .slider').owlCarousel({
    items: 6,
    pagination: true
})
$('#shcfrom').on('click',function(e){
    e.preventDefault();
    $(this).parent().toggleClass('closed')
    $('.cform').slideToggle(250);
    map.setMapTypeId(google.maps.MapTypeId.ROADMAP);

});

$('.shcfrom').on('click',function(e){
    if ($('#shcfrom').parent().hasClass('closed')){
        e.preventDefault();
        $('#shcfrom').trigger('click');
    }
});

var b, a = b = false;
if ($('.car').length)
    a = true;
if ($('.checkout').length || $('#map').length || $('.list').length || $('#dark').length)
    b = true;

$(window).scroll(function(){
    changeNav(72,'body','fixed');
    if (a)
        changeNav(930,'.forfixing','dark');
    if (b)
        changeNav(72,'.forfixing','dark');
});
$(window).trigger('scroll');

function changeNav(width,targetname,classname) {
    if($(window).scrollTop() > width && !$(targetname).hasClass(classname)) {
       $(targetname).addClass(classname);
    } else if ($(window).scrollTop() < width && $(targetname).hasClass(classname)) {
       $(targetname).removeClass(classname);
    }
}

$('input[disabled="disabled"]').on('click',function(e){
    e.preventDefault();
    //console.log('ahhah');
});

function generror(a) {
    var c = '<div id="generror"><div class="bodyerror"><div class="icon"></div><div><h6>Attention</h6><p>'
    +a+'</p><a href="#" class="close">x</a></div></div></div>';
    $('body').append(c);
    $('input,textarea').blur();
}

showErrors = function(errors) {
    $('.validate-note').detach();
    for (var i in errors) {
        var sticky = $('<div class="alert alert-danger validate-note" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only"></span></div>');
        sticky.prepend('<span class="text">' + errors[i] + '&nbsp; &nbsp; </span>').appendTo('body').animate({
            top: 110 + (60 * i),
            opacity: 1
        });
    }
}
showError = function(error){
    if ($('.errorMsg').length) {
        $('.errorMsg').text(error);
        $.colorbox({
            inline: true,
            transition: "elastic",
            arrowKey: true,
            maxWidth: '90%',
            maxHeight: '90%',
            href: '#popUp',
            closeButton: false
        });
    }
}
$('.close.colorbox').on('click', function(){
    $.colorbox.close();
});
});