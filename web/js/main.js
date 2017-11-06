
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
$(window).load(function() {
    $('.slider-wrapper .slider').owlCarousel({
        items: 6,
        pagination: true
    });    
});
$('#shcfrom').on('click',function(e){
    e.preventDefault();
    $(this).parent().toggleClass('closed')
    $('.cform').slideToggle(250);
    map.setMapTypeId(google.maps.MapTypeId.ROADMAP);

});

// search filed toggle
$('.searchButton').click(function() {
    $('body').toggleClass('openedSearchPanel');
    $('.searchContainer .search-input').focus();

})

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