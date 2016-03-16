=== WaveSurfer-WP ===
Contributors: X-Raym
Tags: audio, player, waveform, visualization, media
Donate link: http://www.extremraym.com/en/donation/
Requires at least: 1.0.0
Tested up to: 4.3.1
Stable tag: trunk
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Customizable HTML5 Audio controller with waveform preview (mixed or split channels), using WordPress native audio and playlist shortcode.

== Description ==
This plugin replaces the default WordPress audio player with a player capable of displaying audio waveforms. It can display a mix of the different audio channels (for podcast, radio, e-learning, music), or all channels simultaneously (for sound tutorial, sounds-packs showcases, products demo etc...), which is its main purpose.

By working with the default audio/playlist shortcode, you have two great advantages:

*   It works with all your previous posts
*   You still have the default player in the Visual Editor (not just shortcode)
*   It supports every audio format supported by WordPress (wav, ogg, mp3, m4a).
*   Safe deactivation: if you deactivate the plugin, your shortcode will fallback to the WordPress default audio player.

Global colors and style settings can be overridden by dedicated shortcode attributes.

*   `progress_color="purple"`
*   `wave_color="#FF0000"`
*   `cursor_color="#FF0000"`
*   `height="128"`

Also, there is some attributes accessible at shortcode level:

*   `mute_button="true"`
*   `loop_button="true"`
*   `download_button="true"`

The default style requires [Font-Awesome 1.0](https://fortawesome.github.io/). Because this icon-font is already used in a lot of themes and plugins, it is not included in this pack. However, if your themes and plugins doesn't have it, you can use the [Enqueue Font Awesome CDN](https://wordpress.org/plugins/font-awesome-4-menus/) WordPress plugin, or any other plugin that loads on every page.

You can deactivate the default WaveSurder-WP theme, and use your own theme style. I strongly encourage you to do that as custom CSS is the only way to make it fit your theme perfectly. There is a lot of dedicated CSS selectors for that. You can take one of the included theme as reference.
This will allow you to have more control on icons, responsivity, mouse hover behavior etc...

[More Infos & Demo](http://www.extremraym.com/en/wavesurfer-wp)

You can contribute by to WaveSurfer-WP development on github:
[WaveSurfer-WP on GitHub](https://github.com/x-raym/wavesurfer-wp)
Themes and Translations are welcome !

Optimization trick: if you only use this plugin on a couple of pages, I invite you to use a plugin like [Plugin Organizer](https://wordpress.org/plugins/plugin-organizer/) to globally deactivate the plugin, and make it load resources only on pages which need it.
No need for that on the back-end, only for front-end.

This player doesn't have and will not have Like Button, Sharing Button, Play count and Download count.
If you are looking for a WordPress player with such Social Features and advanced statistics like [SoundCloud](http://www.soundcloud.com) or [Hearthis.at](http://www.hearthis.at), take a look at [ZoomSounds](http://codecanyon.net/item/zoomsounds-neat-html5-audio-player/4525354).

Notes: Some audio formats / browsers combo are not possible. This cannot be fixed by our side. (For ex, wav 24 bits in FireFox).

== Installation ==
For an automatic installation through WordPress:

1. Go to the *Add New* plugins screen in your WordPress admin area
1. Search for *WaveSurfer-WP*
1. Click *Install Now* and activate the plugin

For a manual installation via FTP:

1. Upload the `wavesurfer-wp` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the *Plugins* screen in your WordPress admin area

To upload the plugin through WordPress, instead of FTP:

1. Upload the downloaded zip file on the Add New* plugins screen (see the *Upload* tab) in your WordPress admin area and activate.

== Frequently Asked Questions ==
= The buttons appear but not the Waveform =
There shortcode is interpreted but there is problem for loading the file.

I/ The most common error is that the file is hosted on a CDN that isn't set to allow the kind of requests made by WaveSurfer-js.

So, you have three solutions :

*   Deactivate WaveSurfer for this particular instance using player="default" in the shortcode
*   Go to your CDN settings panel and allow Access-Control-Allow-Origin for XMLHttpRequest
*   Host your file on your main server

II/ This problem may also occurs if your file is not supported by your browser.
Convert the file to a more web-friendly format and try again.

III/ Also, check that your file is accessible via http (aka, check if the link is valid).

Please report other problems on the support forum.

== Screenshots ==
1. Front-End Settings Page
1. Back-End Settings Page
1. Mute, Loop and Download buttons with the Flat Icons Style

== Changelog ==
= 2.2 (2016-03-15) =
* Backend: to back MediaElement instead of WebAudio, which make it possible to play while file is loading. No progress bar while loading needed anymore, and maybe less restrictions with CDN.
* height shortcode and setting
* Play/Pause button is now only Play or Pause according to its state. Same for Mute button and Loop buttons, with Unmute and Unloop, for better accessibility.
* Prevent z-index error in style
* Prevent max-width canvas error
* Script init optimization
* WaveSurfer-js 1.0.58 from 2016-02-28


= 2.1.3 (2016-02-23) =
* Fix double slash in URL (thanks to Glen Rowell)

= 2.1.2 (2016-01-05) =
* AJAX pages loading compatible
* Few HTML optimizations

= 2.1.1 (2016-01-03) =
* Src attribute in audio shortcode is now valid

= 2.1.0 (2016-01-03) =
* Playlist shortcode support
* Responsive waveform
* Deleted Lang Packs (now hosted by WordPress.org)

= 2.0.0 (2015-12-29) =
* New WebAudio rendering for better performance
* wavesurfer.js 1.0.48

= 1.1.0 (2015-11-30) =
* Cursor Color setting and shortcode
* Download Button shortcode
* Loop Button shortcode
* Mute Button shortcode
* Flat Icons theme
* Play/Pause buttons have merged

= 1.0.0 (2015-11-24) =
* First release.

== Upgrade Notice ==
= 2.2 =
Fixes, enhancements, possibility to play while file is loading, and Height attribute

= 2.1.2 =
AJAX pages loading compatible

= 2.1.1 =
Src attribute in audio shortcode is now valid

= 2.1.0 =
Playlist shortcode support

= 2.0.0 =
New WebAudio rendering for better performance

= 1.0.0 =
Initial release.

== Additional Infos ==
It is a port for WordPress of [WaveSurfer-js](http://wavesurfer-js.org/) by katspaugh.

It also contains [Download-js](http://danml.com/download.html) by dandavis.

== Donators ==
Thanks to our generous donators for supporting this plugin development !

1. [SignalToNoize.com](http://signaltonoize.com/)
2. [hawthonn](http://theopod.com)

Do you want to contribute or sponsor one particular feature ? See you on the [donation page](http://www.extremraym.com/en/donation/). Thanks !
