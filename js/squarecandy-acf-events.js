jQuery(document).ready(function($){
  // accordions - event archive, etc
  $('.accordion-content').hide();
  $('.accordion-header').on('click', function(){
    var accordionheader = $(this);
    if (accordionheader.hasClass('accordion-open')) {
      $('.accordion-open').removeClass('accordion-open');
      accordionheader.next().slideUp(500);
    }
    else {
      var openoffset = $('.accordion-open').next().outerHeight();
      var destination = accordionheader.offset().top;
      $("html,body").not(':animated').animate({ scrollTop: destination-110-openoffset}, 500);
      $('.accordion-open').not(accordionheader).removeClass('accordion-open');
      accordionheader.addClass('accordion-open').next().slideDown(500);
      $('.accordion-content').not(accordionheader.next()).slideUp(500);
    }
    return false;
  });
});
