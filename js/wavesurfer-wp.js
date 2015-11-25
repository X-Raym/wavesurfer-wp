/**
 * WaveSurfer-WP Front-End Script
 * Author: X-Raym
 * Author URl: http://www.extremraym.com
 * Date: 2015-11-23
 * Version: 1.0
 */
$j = jQuery.noConflict();

jQuery( document ).ready( function( $ ) {

  // Define all wavesurfer blocks as jQuery elements
  var wavesurfer = $( '.wavesurfer-block' );

  // Add unique class to all wavesurfer blocks. Can be useful but not necessary for the render.
  wavesurfer.each( function( i ) {
    $( this ).addClass( "wavesurfer-player-" + ( i + 1 ) );
  } );

  // Set button as jQuery elements
  var buttonPlay = $( 'button.wavesurfer-play' );
  var buttonPause = $( 'button.wavesurfer-pause' );
  var buttonStop = $( 'button.wavesurfer-stop' );

  // Add Active class on all stop button at init stage
  buttonStop.addClass( 'wavesurfer-active-button' );

  // Define Buttons trigger
  // Define Play button
  buttonPlay.each( function() {
    $( this ).click( function() {

      // Get parent <audio> element
      var audio = $( this ).parent().parent( '.wavesurfer-block' ).children( 'wavesurfer' ).children( 'audio' );

      // Play the sound
      audio.trigger( 'play' );

      // Add an active class
      $( this ).addClass( 'wavesurfer-active-button' );

      // Remove active class from the other buttons
      $( this ).parent().children( 'button.wavesurfer-pause' ).removeClass( 'wavesurfer-active-button' );
      $( this ).parent().children( 'button.wavesurfer-stop' ).removeClass( 'wavesurfer-active-button' );

    } );
  } );

  // Define Pause button
  buttonPause.each( function() {
    $( this ).click( function() {

      var audio = $( this ).parent().parent( '.wavesurfer-block' ).children( 'wavesurfer' ).children( 'audio' );

      audio.trigger( 'pause' );

      $( this ).addClass( 'wavesurfer-active-button' );

      $( this ).parent().children( 'button.wavesurfer-play' ).removeClass( 'wavesurfer-active-button' );
      $( this ).parent().children( 'button.wavesurfer-stop' ).removeClass( 'wavesurfer-active-button' );


    } );
  } );

  // Define Stop button
  buttonStop.each( function() {
    $( this ).click( function() {

      var audio = $( this ).parent().parent( '.wavesurfer-block' ).children( 'wavesurfer' ).children( 'audio' );

      audio.prop( "currentTime", 0 );

      audio.trigger( 'pause' );

      $( this ).addClass( 'wavesurfer-active-button' );
      $( this ).parent().children( 'button.wavesurfer-play' ).removeClass( 'wavesurfer-active-button' );
      $( this ).parent().children( 'button.wavesurfer-pause' ).removeClass( 'wavesurfer-active-button' );

    } );
  } );

  // Check if audio are initialized
  waitForElementToDisplay( 'audio', 1000 );

} ); // End Ready



/* FUNCTIONS DECLARATIONS */

// Wait Initialization of Audio
function waitForElementToDisplay( selector, time ) {
  if ( document.querySelector( selector ) != null ) {
    $j( 'audio' ).hide(); // facultative
    updateTimeCode();
  } else {
    setTimeout( function() {
      waitForElementToDisplay( selector, time );
    }, time ); // Check again.
  }
} // End waitForElementToDisplay()

// Update Timecode
function updateTimeCode() {
  var wavesurfer = $j( '.wavesurfer-block' );
  wavesurfer.each( function() {

    // Get infos blocks
    var audio = $j( this ).children( 'wavesurfer' ).children( 'audio' );
    var timeblock = $j( this ).find( '.wavesurfer-time' );
    var duration = $j( this ).find( '.wavesurfer-duration' );

    // Get buttons
    var buttonPlay = $j( this ).find( 'button.wavesurfer-play' );
    var buttonPause = $j( this ).find( 'button.wavesurfer-pause' );
    var buttonStop = $j( this ).find( 'button.wavesurfer-stop' );

    // Write duration and timecode
    var audio_duration = waitForAudio( audio, 1000 );
    duration.html( secondsTimeSpanToMS( audio_duration ) );

    timeblock.html( secondsTimeSpanToMS( audio[0].currentTime ) );

    // Trigger events at play
    audio.bind( "timeupdate", function() {
      var audio_time = audio[0].currentTime;
      // Prevent a bug that make current time one second higher than duration at audio end
      if ( audio_time > audio_duration ) {
        audio_time = audio_duration;
      };
      timeblock.text( secondsTimeSpanToMS( audio_time ) );
    } );

    // Trigger events when audio ends
    audio.bind( "ended", function() {
      buttonPlay.removeClass( 'wavesurfer-active-button' );
      buttonPause.removeClass( 'wavesurfer-active-button' );
      buttonStop.addClass( 'wavesurfer-active-button' );
    } );

  } );
} // End updateTimeCode()

// Convert seconds into MS
function secondsTimeSpanToMS( s ) {
  var m = Math.floor( s / 60 ); //Get remaining minutes
  s -= m * 60;
  s = Math.floor( s );
  return ( m < 10 ? '0' + m : m ) + ":" + ( s < 10 ? '0' + s : s ); //zero padding on minutes and seconds
} // End secondsTimeSpanToMS

// Wait Initialization of Audio
function waitForAudio( object, time ) {
  duration_val = object[0].duration;
  // If duration is not numeric, then the audio element is not ready and it returns NaN.
  if ( $j.isNumeric( duration_val ) ) {
    return duration_val;
  } else {
    setTimeout( function() {
      waitForAudio( object, time ); // Check again.
    }, time );
  }
} // End waitForAudio()
