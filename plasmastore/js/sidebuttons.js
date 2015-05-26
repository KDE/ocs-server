/*$('body').on('click', '.sidebuttons a', function (e) {
    $(this).addClass('active');
    $(this).siblings().removeClass('active');
    
    //do any other button related things
}); */
$(document).ready(function () {
    $('.sidebuttons li a').click(function(e) {

        $('.sidebuttons li').removeClass('active');

        var $parent = $(this).parent();
        if (!$parent.hasClass('active')) {
            $parent.addClass('active');
        }
        e.preventDefault();
    });
});