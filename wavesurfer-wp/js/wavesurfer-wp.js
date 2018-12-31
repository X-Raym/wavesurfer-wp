/**
 * WaveSurfer-WP Front-End Script
 * Author: X-Raym
 * Author URl: https://www.extremraym.com
 * Date: 2017-05-09
 * Version: 2.7.3
 */


// No conflict for WordPress
var $j = jQuery.noConflict();

// Init table for storing wavesurfer objects
var wavesurfer = [];

// On Document Ready and Ajax Complete
$j(document).on('ready ajaxComplete wavesurfer', function(event, request, settings) {
	if (typeof settings !== 'undefined') {
		if (settings.success.name === 'wavesurfer_wp_ajax') return;
	} else {
		WaveSurferInit();
	}
});

/* FUNCTIONS */

// WaveSurfer Init
function WaveSurferInit() {

	// Loop in each wavesurfer block
	$j('.wavesurfer-block').each(function(i) {

		// If there is already a player instance for this id
		if( typeof wavesurfer[i] !== 'undefined' ) return;

		// Get WaveSurfer block for datas attribute
		var container = $j(this).children('.wavesurfer-player');
		var split = container.data('split-channels');

		// Wavesurfer block object
		var object = this;

		init(i, container, object, split);

	}); // End loop in each wavesurfer-block

} // End function WaveSurferInit

function init(i, container, object, split) {

	// Text selector for the player
	var selector = '#wavesurfer-player-' + i;

	// Add unique ID to WaveSurfer Block
	container.attr('id', 'wavesurfer-player-' + i);

	// Get data attribute
	var wave_color = container.data('wave-color');
	var progress_color = container.data('progress-color');
	var cursor_color = container.data('cursor-color');
	var file_url = container.data('url');
	var height = container.data('height');
	var bar_width = container.data('bar-width');

	// Init and Control
	var options = {
		container: selector,
		splitChannels: split,
		waveColor: wave_color,
		progressColor: progress_color,
		cursorColor: cursor_color,
		backend: 'MediaElement',
		height: height,
		barWidth: bar_width,
		responsive: true
	};

	// Others parameters
	var peaks = null;
	var preload = 'metadata';

	// Create WaveSurfer object
	wavesurfer[i] = WaveSurfer.create(options);

	// Prevent error if the player can't be initialized
	if ( typeof wavesurfer[i] === 'undefined' ) return;

	// File
	wavesurfer[i].load(file_url, peaks, preload);

	// Buttons

	// Timecode blocks
	var timeblock = $j(object).find('.wavesurfer-time');
	var duration = $j(object).find('.wavesurfer-duration');

	// Controls Definition
	var buttonPlay = $j(object).find('button.wavesurfer-play');
	var buttonStop = $j(object).find('button.wavesurfer-stop');
	var buttonMute = $j(object).find('button.wavesurfer-mute');
	var buttonDownload = $j(object).find('button.wavesurfer-download');
	var buttonLoop = $j(object).find('button.wavesurfer-loop');
	var debugBlock = $j(object).find('.debug');
	var progressBar = $j(object).find('progress');

	var playlist = false;
	if ( $j(object).hasClass('wavesurfer-playlist') ) playlist = true;

	wavesurfer[i].on('error', function() {
		progressBar.hide();
	});

	// Timecode during Play
	wavesurfer[i].on('audioprocess', function() {
		var current_time = wavesurfer[i].getCurrentTime();
		timeblock.html(secondsTimeSpanToMS(current_time));
	});

	// Timecode and duration at Ready
	wavesurfer[i].on('ready', function() {
		progressBar.hide();
		var audio_duration = wavesurfer[i].getDuration();
		duration.html(secondsTimeSpanToMS(audio_duration));
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

		// IF PLAYING -> TO PAUSE
		if ($j(this).hasClass('wavesurfer-active-button')) {

			SetPauseButton(this);

		} else {
			// IS NOT PLAYING -> TO PLAY

			PauseOtherPlayers(wavesurfer, i);

			$j(this).children('span').text(wavesurfer_localize.pause);

			// Add an active class
			$j(this).addClass('wavesurfer-active-button');

			// Remove active class from the other buttons
			$j(this).parent().children('button.wavesurfer-play').removeClass('wavesurfer-paused-button');
			$j(this).parent().children('button.wavesurfer-stop').removeClass('wavesurfer-active-button');
		}

	});
	buttonStop.click(function() {
		wavesurfer[i].stop();

		if (!$j(this).hasClass('wavesurfer-active-button')) {

			$j(this).addClass('wavesurfer-active-button');
			$j(this).parent().children('button.wavesurfer-play').removeClass('wavesurfer-active-button');
			$j(this).parent().children('button.wavesurfer-play').removeClass('wavesurfer-paused-button');
			$j(this).parent().children('button.wavesurfer-play').children('span').text(wavesurfer_localize.play);
			var current_time = wavesurfer[i].getCurrentTime();
			timeblock.html(secondsTimeSpanToMS(current_time));
		}
	});

	// Button Mute
	buttonMute.click(function() {
		wavesurfer[i].toggleMute();

		// IF ACTIVE
		if ($j(this).hasClass('wavesurfer-active-button')) {
			$j(this).removeClass('wavesurfer-active-button');
			$j(this).children('span').text(wavesurfer_localize.mute);
		} else {
			$j(this).addClass('wavesurfer-active-button');
			$j(this).children('span').text(wavesurfer_localize.unmute);
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
		if ( playlist === false ) {
			if (buttonLoop.hasClass('wavesurfer-active-button') === false) {
				buttonPlay.removeClass('wavesurfer-active-button');
				buttonPlay.children('span').text(wavesurfer_localize.play);
				buttonStop.addClass('wavesurfer-active-button');
			}
		}
	});

	// Button Loop
	buttonLoop.click(function() { // NOTE: seamless loop need WebAudio backend
		// IF LOOP
		if ($j(this).hasClass('wavesurfer-active-button')) {
			$j(this).removeClass('wavesurfer-active-button');
			$j(this).children('span').text(wavesurfer_localize.loop);
			wavesurfer[i].on('finish', function() {
				wavesurfer[i].pause();
			});
		} else {
			$j(this).addClass('wavesurfer-active-button');
			$j(this).children('span').text(wavesurfer_localize.unloop);
			wavesurfer[i].on('finish', function() {
				wavesurfer[i].play();
			});
		}
	});

	// Check if playlist
	if ( playlist === true) {

		// The playlist list
		var tracks = $j(object).find('.wavesurfer-list-group li');

		// Set the first track as active at init
		var current = 0;
		tracks.eq(current).addClass('wavesurfer-active-track');

		// When cliking on an item
		tracks.click(function() {

			if ($j(this).hasClass('wavesurfer-active-track') === false) {

				// Remove active track class to all tracks
				tracks.each(function() {
					$j(this).removeClass('wavesurfer-active-track');
				});

				// Add active track class
				$j(this).addClass('wavesurfer-active-track');

				file_url = $j(this).data('url');
				current = $j(this).index();

				// Load sound and waveform
				wavesurfer[i].load(file_url, peaks, preload);

				wavesurfer[i].on('ready', function() {
					if (buttonPlay.hasClass('wavesurfer-active-button')) {
						wavesurfer[i].play();
					}
				});

			}

		}); // END click track

		wavesurfer[i].on('finish', function() {

			if (buttonLoop.hasClass('wavesurfer-active-button')) {
				wavesurfer[i].play();
			} else {
				// Increment current track number
				current++;

				// Get track URL
				var url = '';
				url = tracks.eq(current).data('url');
				// If there no other tracks after
				if (url !== undefined) {
					wavesurfer[i].load(url, peaks, preload);
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
					if (buttonPlay.hasClass('wavesurfer-active-button')) {
						wavesurfer[i].on('ready', function() {
							if (buttonPlay.hasClass('wavesurfer-active-button')) {
								wavesurfer[i].play();
							}
						});
					}

				} else {
					if (buttonLoop.hasClass('wavesurfer-active-button') === false) {
						buttonPlay.removeClass('wavesurfer-active-button');
						buttonPlay.children('span').text(wavesurfer_localize.play);
						buttonStop.addClass('wavesurfer-active-button');
					}
				}// End if url not undefined
			} // End if Loop is on
		}); // End of wavesurfer.on('finish')

	} // End if playlist
}

// Convert seconds into MS
function secondsTimeSpanToMS(s) {
	var m = Math.floor(s / 60); //Get remaining minutes
	s -= m * 60;
	s = Math.floor(s);
	return (m < 10 ? '0' + m : m) + ":" + (s < 10 ? '0' + s : s); //zero padding on minutes and seconds
} // End secondsTimeSpanToMS

// Pause the other players if Play is pressed on a player
function PauseOtherPlayers(wavesurfer, i) {
	$j.each(wavesurfer, function(j) {
		if (wavesurfer[j].isPlaying() && j != i) {
			wavesurfer[j].playPause();
		}
	});

	// Loop in each wavesurfer block
	$j('.wavesurfer-block button.wavesurfer-play').each(function(i) {
		// IF IS NOT PLAYING
		if ($j(this).hasClass('wavesurfer-active-button')) {
			SetPauseButton(this);
		}
	});
}

// Set Button to Pause
function SetPauseButton(object) {
	$j(object).removeClass('wavesurfer-active-button');

	$j(object).addClass('wavesurfer-paused-button');

	$j(object).children('span').text(wavesurfer_localize.resume);

	$j(object).parent().children('button.wavesurfer-play').removeClass('wavesurfer-active-button');
	$j(object).parent().children('button.wavesurfer-stop').removeClass('wavesurfer-active-button');
}
