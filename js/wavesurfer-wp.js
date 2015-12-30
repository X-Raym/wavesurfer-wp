/**
 * WaveSurfer-WP Front-End Script
 * Author: X-Raym
 * Author URl: http://www.extremraym.com
 * Date: 2015-12-29
 * Version: 2.0
 */

// No conflcit for WordPress
$j = jQuery.noConflict();

// Init table for storing wavesurfer objects
var wavesurfer = [];

// On window load
window.onload = function () {

	// Loop in each wavesurfer block
	$j('.wavesurfer-block').each(function(i) {

	  // Text selector for the player
	  var selector = '#wavesurfer-player-' + i;

	  // Get WaveSurfer block for datas attribute
	  var container = $j(this).children(container);

	  // Add unique ID to WaveSurfer Block
	  container.attr("id", "wavesurfer-player-" + i);

	  // Get data attribute
	  var wave_color = container.attr('data-wave-color');
	  var progress_color = container.attr('data-progress-color');
	  var cursor_color = container.attr('data-cursor-color');
	  var file_url = container.attr('data-url');
	  var split = container.attr('data-split-channels');
	  if (split == "true") {
	    split = true;
	  } else {
	    split = false;
	  }

	  // Init and Control
	  var options = {
	    container: selector,
	    splitChannels: split,
	    waveColor: wave_color,
	    progressColor: progress_color,
	    cursorColor: cursor_color,
			backend: 'MediaElement'
	  };

	  // Create WaveSurfer object
	  wavesurfer[i] = WaveSurfer.create(options);

	  // File
	  wavesurfer[i].load(file_url);

	});

	// Buttons
	$j('.wavesurfer-block').each(function(i) {

	  // Timecode blocks
	  var timeblock = $j(this).find('.wavesurfer-time');
	  var duration = $j(this).find('.wavesurfer-duration');

	  // Controls Definition
	  var buttonPlay = $j(this).find('button.wavesurfer-play');
	  var buttonStop = $j(this).find('button.wavesurfer-stop');
	  var buttonMute = $j(this).find('button.wavesurfer-mute');
	  var buttonDownload = $j(this).find('button.wavesurfer-download');
	  var buttonLoop = $j(this).find('button.wavesurfer-loop');

	  // Timecode and duration at Ready
	  wavesurfer[i].on('ready', function() {
	    var audio_duration = wavesurfer[i].getDuration();
	    duration.html(secondsTimeSpanToMS(audio_duration));
	    var current_time = wavesurfer[i].getCurrentTime();
	    timeblock.html(secondsTimeSpanToMS(current_time));
	  });

	  // Timecode during Play
	  wavesurfer[i].on('audioprocess', function() {
	    var current_time = wavesurfer[i].getCurrentTime();
	    timeblock.html(secondsTimeSpanToMS(current_time));
	  });

	  // Timecode during pause + seek
	  wavesurfer[i].on('seek', function() {
	    var current_time = wavesurfer[i].getCurrentTime();
	    timeblock.html(secondsTimeSpanToMS(current_time));
	  });

	  // Add Active class on all stop button at init stage
	  buttonStop.addClass('wavesurfer-active-button');

	  // Controls Functions
	  buttonPlay.click(function() {
	    wavesurfer[i].playPause();

	    // IF IS PLAYING
	    if ($j(this).hasClass('wavesurfer-active-button')) {
	      $j(this).removeClass('wavesurfer-active-button');

	      $j(this).addClass('wavesurfer-paused-button');

	      $j(this).parent().children('button.wavesurfer-play').removeClass('wavesurfer-active-button');
	      $j(this).parent().children('button.wavesurfer-stop').removeClass('wavesurfer-active-button');

	      // IF NOT PLAYING
	    } else {
	      $j(this).addClass('wavesurfer-active-button');

	      // Add an active class
	      $j(this).addClass('wavesurfer-active-button');

	      // Remove active class from the other buttons
	      $j(this).parent().children('button.wavesurfer-play').removeClass('wavesurfer-paused-button');
	      $j(this).parent().children('button.wavesurfer-stop').removeClass('wavesurfer-active-button');
	    };

	  });
	  buttonStop.click(function() {
	    wavesurfer[i].stop();

	    $j(this).addClass('wavesurfer-active-button');
	    $j(this).parent().children('button.wavesurfer-play').removeClass('wavesurfer-active-button');
	    $j(this).parent().children('button.wavesurfer-play').removeClass('wavesurfer-paused-button');
	    var current_time = wavesurfer[i].getCurrentTime();
	    timeblock.html(secondsTimeSpanToMS(current_time));
	  });

	  // Button Mute
	  buttonMute.click(function() {
	    wavesurfer[i].toggleMute();

	    // IF ACTIVE
	    if ($j(this).hasClass('wavesurfer-active-button')) {
	      $j(this).removeClass('wavesurfer-active-button');
	    } else {
	      $j(this).addClass('wavesurfer-active-button');
	    };

	  });

	  // Define Stop button
	  buttonDownload.click(function() {
	    var audio = $j(this).parent().parent('.wavesurfer-block').children('.wavesurfer-player');

	    var download_url = audio.attr('data-url');
	    // Get FileName from URL
	    var index = download_url.lastIndexOf("/") + 1;
	    var file_name = download_url.substr(index);
	    $j(this).children('a').attr('href', download_url);
	    $j(this).children('a').attr('download', file_name);

	    // then download
	    download(download_url);
	  });

	  // On finish, remove active class on play
	  wavesurfer[i].on('finish', function() {
	    if (buttonLoop.hasClass('wavesurfer-active-button') == false) {
	      buttonPlay.removeClass('wavesurfer-active-button');
	      buttonStop.addClass('wavesurfer-active-button');
	    };
	  });

	  // Button Loop
	  buttonLoop.click(function() {
	    // IF LOOP
	    if ($j(this).hasClass('wavesurfer-active-button')) {
	      $j(this).removeClass('wavesurfer-active-button');
	      wavesurfer[i].on('finish', function() {
	        wavesurfer[i].pause();
	      });
	    } else {
	      $j(this).addClass('wavesurfer-active-button');
	      wavesurfer[i].on('finish', function() {
	        wavesurfer[i].play();
	      });
	    };
	  });
	});

};

// Convert seconds into MS
function secondsTimeSpanToMS(s) {
  var m = Math.floor(s / 60); //Get remaining minutes
  s -= m * 60;
  s = Math.floor(s);
  return (m < 10 ? '0' + m : m) + ":" + (s < 10 ? '0' + s : s); //zero padding on minutes and seconds
} // End secondsTimeSpanToMS
