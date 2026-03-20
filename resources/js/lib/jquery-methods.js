export function hideAppPreloader() {
    // $('#preloader').show();
    $('#preloader').delay(350).fadeOut('slow');
    $('body').delay(350).css({ 'overflow': 'visible' });
}
export function showAppPreloader() {
    $('#preloader').delay(350).fadeIn('slow');
    $('body').delay(350).css({ 'overflow': 'hidden' });
}