jQuery(document).ready(function ($) {
    /* Score border color
    Inspired from https://codepen.io/leandroamato/pen/jOWqrGe */
    const score = document.querySelector( "#seokey-audit-score-outter-circle" )
    const ratingScore = parseInt( $( '#seokey-audit-score' ).attr( "data-score" ), 10 );
    // Define the background gradient according to the score and color
    const gradient = `background: conic-gradient(#2CAFD9 ${ratingScore}%, #e5472b 0 100%);`; // #E5472B for red
    // Set the gradient as the rating background
    score.setAttribute("style", gradient);
});