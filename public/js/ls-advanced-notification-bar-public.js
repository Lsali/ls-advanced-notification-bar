(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
jQuery(document).ready(function($) {
    var $bar = $('#ls-notification-bar');
    if ($bar.length === 0) return;

    // Ελέγχουμε αν ο χρήστης την είχε κλείσει προηγουμένως σε αυτό το session
    if (sessionStorage.getItem('ls_bar_closed') === 'true') {
        return;
    }

    var delay = parseInt($bar.attr('data-delay')) || 0;

    // Εμφάνιση με καθυστέρηση (Delay)
    setTimeout(function() {
        $bar.fadeIn();
    }, delay);

    // Λειτουργία Κλεισίματος (Close button X)
    $('#ls-close-notification').on('click', function() {
        $bar.fadeOut();
        sessionStorage.setItem('ls_bar_closed', 'true');
    });
});
})( jQuery );
