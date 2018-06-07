(function($) {

//change settings
//$('.alert').css("background-color", $('.alert').data('color') );
//$('.alert').css("color", $('.alert').data('letter') );


//show animation
var animationin=$('.alert').data('animationin');

var animationout=$('.alert').data('animationout');
$('.alert').addClass('animated '+ animationin);
animationout='bounceOut';

//close
//$('.close').addClass('animated bounce');

$('.close').click(function() {
$(".alert").removeAttr('class').addClass("alert");
  $('.alert').addClass('animated '+ animationout);
});




})( jQuery );