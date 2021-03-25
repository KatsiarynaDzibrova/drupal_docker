let update = function ($) {
  Drupal.ajax({url: "/netflix-show/random"}).execute();
}

jQuery(update);

setInterval(function () {
  jQuery(update);
},  5 * 60 * 1000);
