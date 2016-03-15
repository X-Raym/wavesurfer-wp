/**
 * WaveSurfer-WP Front-End Script
 * Author: X-Raym
 * Author URl: http://www.extremraym.com
 * Date: 2016-03-15
 * Version: 2.2
 */

// TO DO
// PLAYLIST : Repeat playlist button ?
// PLAYLIST : Navigation buttons ? First, Next, Previous
// PLAYLIST : Download button on every track ?
// PLAYLIST : Shuffle Button ?
// Overide Download Link ?
// Permanent Play Bar ?
// Extract infos from ID3v2 ?
// Regions plugins + WebTT Integration ?

// No conflict for WordPress
var $j = jQuery.noConflict();

// On Document Ready and Ajax Complete
$j(document).on("ready ajaxComplete", function() {
    if ( $j( "#wavesurfer-player-0" ).find('canvas').length == 0) {
	    WaveSurferInit();
    }
});


/* FUNCTIONS */

// WaveSurfer Init
function WaveSurferInit() {

	// Init table for storing wavesurfer objects
	var wavesurfer = [];

	// Loop in each wavesurfer block
	$j('.wavesurfer-block').each(function(i) {

    // Text selector for the player
    var selector = '#wavesurfer-player-' + i;

    // Get WaveSurfer block for datas attribute
    var container = $j(this).children('.wavesurfer-player');

    // Add unique ID to WaveSurfer Block
    container.attr("id", "wavesurfer-player-" + i);

    // Get data attribute
    var wave_color = container.data('wave-color');
    var progress_color = container.data('progress-color');
    var cursor_color = container.data('cursor-color');
    var file_url = container.data('url');
    var split = container.data('split-channels');
		var height = container.data('height');

		// Split channels
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
			height: height,
			backend: 'MediaElement'
    };

    // Create WaveSurfer object
    wavesurfer[i] = WaveSurfer.create(options);

    // File
    wavesurfer[i].load(file_url);

    // Responsive Waveform
    $j(window).resize(function() {
      wavesurfer[i].drawer.containerWidth = wavesurfer[i].drawer.container.clientWidth;
      wavesurfer[i].drawBuffer();
    });


		// Buttons

    // Timecode blocks
    var timeblock = $j(this).find('.wavesurfer-time');
    var duration = $j(this).find('.wavesurfer-duration');

    // Controls Definition
    var buttonPlay = $j(this).find('button.wavesurfer-play');
    var buttonStop = $j(this).find('button.wavesurfer-stop');
    var buttonMute = $j(this).find('button.wavesurfer-mute');
    var buttonDownload = $j(this).find('button.wavesurfer-download');
    var buttonLoop = $j(this).find('button.wavesurfer-loop');
    var debugBlock = $j(this).find('.debug');
    var progressBar = $j(this).find('progress');

    wavesurfer[i].on('loading', function(percent) {
			progressBar.attr('value', percent);
    });
    wavesurfer[i].on('error', function() {
      progressBar.hide();
    });

    // Timecode and duration at Ready
    wavesurfer[i].on('ready', function() {
      progressBar.hide();
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

      // IF IS NOT PLAYING
      if ($j(this).hasClass('wavesurfer-active-button')) {
        $j(this).removeClass('wavesurfer-active-button');

        $j(this).addClass('wavesurfer-paused-button');

				$j(this).children('span').text('Play');

        $j(this).parent().children('button.wavesurfer-play').removeClass('wavesurfer-active-button');
        $j(this).parent().children('button.wavesurfer-stop').removeClass('wavesurfer-active-button');

      // IF PLAYING
      } else {
				$j(this).children('span').text('Pause');

        // Add an active class
        $j(this).addClass('wavesurfer-active-button');

        // Remove active class from the other buttons
        $j(this).parent().children('button.wavesurfer-play').removeClass('wavesurfer-paused-button');
        $j(this).parent().children('button.wavesurfer-stop').removeClass('wavesurfer-active-button');
      }

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
				$j(this).children('span').text('Mute');
      } else {
        $j(this).addClass('wavesurfer-active-button');
				$j(this).children('span').text('Unmute');
      }

    });

    // Define Stop button
    buttonDownload.click(function() {
      var audio = $j(this).parent().parent('.wavesurfer-block').children('.wavesurfer-player');

      var download_url = audio.data('url');
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
      }
    });

    // Button Loop
    buttonLoop.click(function() {
      // IF LOOP
      if ($j(this).hasClass('wavesurfer-active-button')) {
        $j(this).removeClass('wavesurfer-active-button');
				$j(this).children('span').text('Loop');
        wavesurfer[i].on('finish', function() {
          wavesurfer[i].pause();
        });
      } else {
        $j(this).addClass('wavesurfer-active-button');
				$j(this).children('span').text('Unloop');
        wavesurfer[i].on('finish', function() {
          wavesurfer[i].play();
        });
      };
    });

    // Check if playlist
    if ($j(this).hasClass('wavesurfer-playlist')) {
      // The playlist links
      var tracks = $j(this).find('.wavesurfer-list-group li');
      var current = 0;
      tracks.eq(current).addClass('wavesurfer-active-track');

      // When cliking on an item
      tracks.click(function() {
        if ($j(this).hasClass('wavesurfer-active-track') == false) {

          tracks.each(function() {
            $j(this).removeClass('wavesurfer-active-track');
          });
          var url = $j(this).data('url');
          current = $j(this).index();
          wavesurfer[i].load(url);
          progressBar.attr('value', '0');
          // progressBar.show(); -- hidden since 2.2 for BackEnd element

          // Remove active tracks from all tracks
          wavesurfer[i].on('loading', function(percent) {
            progressBar.attr('value', percent);
          });
          wavesurfer[i].on('ready', function() {
            progressBar.hide();
            wavesurfer[i].play();
          });
          $j(this).addClass('wavesurfer-active-track');
          buttonPlay.addClass('wavesurfer-active-button');

          // Add an active class
          buttonPlay.addClass('wavesurfer-active-button');

          // Remove active class from the other buttons
          buttonPlay.parent().children('button.wavesurfer-play').removeClass('wavesurfer-paused-button');
          buttonPlay.parent().children('button.wavesurfer-stop').removeClass('wavesurfer-active-button');

          buttonDownload.parent().parent('.wavesurfer-block').children('.wavesurfer-player').data('url', url);
        } // ENDIF active track
      });

      wavesurfer[i].on('finish', function() {
        // Increment current track number
        current++;

        // Get track URL
        var url = '';
        url = tracks.eq(current).data('url');
        // If there no other tracks after
        if (url != undefined) {
          wavesurfer[i].load(url);
          progressBar.attr('value', '0');
          // progressBar.show(); -- hidden since 2.2 for BackEnd element

          // Remove active tracks from all tracks
          wavesurfer[i].on('loading', function(percent) {
            progressBar.attr('value', percent);
          });

          tracks.eq(current - 1).removeClass('wavesurfer-active-track');
          tracks.eq(current).addClass('wavesurfer-active-track');

          buttonDownload.parent().parent('.wavesurfer-block').children('.wavesurfer-player').data('url', url);
          // Check if continuous PLay is on.
          // TO DO

          // When it is loaded, play.
          wavesurfer[i].on('ready', function() {
            wavesurfer[i].play();
          });

				} // End if url not undefined

			}); // End of wavesurfer.on('finish')

		} // End if playlist

	}); // End loop in each wavesurfer-block

} // End function WaveSurferInit


// Convert seconds into MS
function secondsTimeSpanToMS(s) {
  var m = Math.floor(s / 60); //Get remaining minutes
  s -= m * 60;
  s = Math.floor(s);
  return (m < 10 ? '0' + m : m) + ":" + (s < 10 ? '0' + s : s); //zero padding on minutes and seconds
} // End secondsTimeSpanToMS
