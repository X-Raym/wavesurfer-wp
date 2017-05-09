=== WaveSurfer-WP ===
Contributors: X-Raym
Tags: audio, player, waveform, visualization, media
Donate link: https://www.extremraym.com/en/donation/
Requires at least: 4.0
Tested up to: 4.7.4
Stable tag: trunk
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Customizable HTML5 Audio controller with waveform preview (mixed or split channels), using WordPress native audio and playlist shortcode.

== Description ==
This plugin replaces the default WordPress audio player with a player capable of displaying audio waveforms. It can display a mix of the different audio channels (for podcast, radio replays, e-learning, music), or all channels simultaneously (for sound tutorial, sounds-packs showcases, audio products demo etc...), which is its main purpose.

By working with the default audio/playlist shortcode, you have great advantages:

*   It works with all your previous posts
*   You still have the default player in the Visual Editor (not just shortcode)
*   It supports every audio format supported by WordPress (wav, ogg, mp3, m4a).
*   Safe deactivation: if you deactivate the plugin, your shortcode will fallback to the WordPress default audio player.

Global colors and style settings can be overridden locally by dedicated shortcode attributes.

*   `progress_color="purple"`
*   `wave_color="#FF0000"`
*   `cursor_color="#FF0000"`
*   `height="128"`
*   `bar_width="0"`

Also, there is some attributes accessible at shortcode level:

*   `mute_button="true"`
*   `loop_button="true"`
*   `download_button="true"`
*   `split_channels="true"`
*   `player="default"`

For more advanced customization, with a custom [site-plugin](http://www.wpbeginner.com/beginners-guide/what-why-and-how-tos-of-creating-a-site-specific-wordpress-plugin/), you can:

* add shortcode attributes conditionally with the filter `wavesurfer_wp_shortcode_attributes`.
* use the `wavesurfer_wp_shortcode_data` filter if you want to add custom waveform data attributes.
* use the `wavesurfer-wp-init` JavaScript event handler to render the player on custom events, like after click on a button. See examples on the [WaveSurfer-WP-Init](https://github.com/X-Raym/wavesurfer-wp-init) GitHub repository.

Check this [Gist](https://gist.github.com/X-Raym/5c388e6554b30ca6a56646fb8d96d17f) for demos of how to use the filters.

Extra features :

* Pressing play on a player automatically set all the others on the same page to pause.
* AJAX Page loading compatibility
* MultiSite Friendly

The default style requires some icons of [Font-Awesome 1.0](https://fortawesome.github.io/). These are included in the plugin as a small custom font. Because this icon-font is already used in a lot of themes and plugins, you can deactivate this custom font if needed.

You can deactivate the default WaveSurder-WP theme, and use your own theme style. I strongly encourage you to do that as custom CSS is the only way to make it fit your theme perfectly. There is a lot of dedicated CSS selectors for that. You can take one of the included theme as reference.
This will allow you to have more control on icons, responsivity, mouse hover behavior etc...

[More Infos & Demos](https://www.extremraym.com/en/wavesurfer-wp)

You can contribute by to WaveSurfer-WP development on [GitHub](https://github.com/x-raym/wavesurfer-wp)

Themes and Translations are welcome !

Optimization trick: if you only use this plugin on a couple of pages, I invite you to use a plugin like [Plugin Organizer](https://wordpress.org/plugins/plugin-organizer/) or [Gonzales](http://tomasz-dobrzynski.com/wordpress-gonzales) to globally deactivate the plugin or its style, and make it load resources only on pages which need it.
No need for that on the back-end, only for front-end.

This player doesn't have and will not have Like Button, Sharing Button, Play count and Download count.
If you are looking for a WordPress player with such Social Features and advanced statistics like [SoundCloud](http://www.soundcloud.com) or [Hearthis.at](http://www.hearthis.at), take a look at [ZoomSounds](http://codecanyon.net/item/zoomsounds-neat-html5-audio-player/4525354).
For other advanced WordPress integration of wavesurfer-js, you can check [WavePlayer](http://codecanyon.net/item/waveplayer-a-wordpress-audio-player/14349799) by luigipulcini or [WaveSurfer-Plus](https://codecanyon.net/item/wavesurfer-plus-mp3-player-module-for-gmedia-plugin/19242349) by GalleryCreator.

Contrary to the other WordPress plugin based on wavesurfer-js, wavesurfer-js hasn't been modified in this plugin. This means that you can extend feature of this plugin using the wavesurfer-js [methods](http://wavesurfer-js.org/), and that updates from the wavesurfer-js community will be pushed in WaveSurfer-WP regularly.

= WaveSurfer-WP Premium Add-on =
A premium add-on is available to add extra features to WaveSurfer-WP.

*   Cache Peaks File
This add-on creates and loads peaks from small files, containing peaks values. No need to wait for the full audio to be decoded to display its waveform.

*   Markers System
You can add custom clickable element on your pages to seek WaveSurfer-WP player to a desired position. Can be useful for adding marks, chapters, and it can even be used for [interactive audio transcripts](https://www.extremraym.com/en/wavesurfer-wp-markers)!

*   Plug and Play
These extra features are packed as an add-on. No need to delete and replace the original plugin. You will still be able to benefit from translations made by the community. Also, the core is still open source, to allow contribution.

*   TimeLine Plugin
When activated thanks to a shortcode attribute, a customizable time ruler will appear below your waveform.

You can buy it from the official [product page](https://www.extremraym.com/en/downloads/wavesurfer-wp-premium).
Thanks for considering this way to support WaveSurfer-WP !

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
Shortcode are interpreted but there is problem for loading the file.

I/ The most common error is that the file is hosted on a CDN that isn't set to allow the kind of requests made by WaveSurfer-js.

So, you can choose one of these solutions :

*   Deactivate WaveSurfer for this particular instance using player="default" in the shortcode
*   Go to your CDN settings panel and allow Access-Control-Allow-Origin for XMLHttpRequest
*   Host your file on your main server

II/ This problem may also occurs if your file is not supported by your browser.

Some audio formats / browsers combo are not possible. This cannot be fixed by our side. (For ex, wav 24 bits in FireFox).
Convert the file to a more web-friendly format and try again.

III/ Also, check that your file is accessible via http (aka, check if the link is valid).

Please report other problems on the support forum.

== Screenshots ==
1. Front-End Settings Page
1. Back-End Settings Page
1. Mute, Loop and Download buttons with the Flat Icons Style

== Changelog ==
= 2.7.3 (2017-04-18) =
* Filters: better logic.
* Scripts: dependencies minification. Dev versions could be loaded thanks to WordPress SCRIPT_DEBUG variable.
* Better compatibility with AJAX

= 2.7.2 (2017-04-14) =
* Continuous shortcode attribute for playlist support for the premium add-on

= 2.7.1 (2017-01-27) =
* Better internal hook names

= 2.7 (2017-01-19) =
* Add sound duration, title (fallback to post title), thumbnail and artist in playlist tracks
* Fix 'default' shortcode attribute behavior for playlists
* MultiSite friendly
* Notices fixes
* Small styles enhancements
* Possibility to enqueue/dequeue a custom icons font (default is enqueue)

= 2.6.4 (2016-12-21) =
* Updated styles for WaveSurfer-WP-Premium interactive markers system

= 2.6.3 (2016-11-29) =
* AJAX Page loading compatibility is back
* JavaScript hook for custom WaveSurfer-WP player initialization

= 2.6.2 (2016-11-22) =
* Fixed Playlist behavior.
* Back to wavesurfer-js original fork.
* Fixed playlist with one file only warning error.

= 2.6.1 (2016-10-31) =
* Fixed one attachment playlist shortcode PHP warning error.

= 2.6 (2016-10-24) =
* Added new filters for bulk adding shortcode attributes or waveform data.

= 2.5.3 (2016-10-22) =
* Optimizations for WaveSurfer-WP Premium

= 2.5.2 (2016-10-19) =
* Performance: Don't load audio unless play is clicked (waveforms are still drawn at page ready)

= 2.5.1 (2016-10-17) =
* Fixed playlist with one file only issue.

= 2.5 (2016-10-07) =
* Updated for WaveSurfer-WP Premium add-on
* Better localization domain name
* Update wavesurfer-js with custom version
* Various enhancements

= 2.3.1 (2016-03-23) =
* Player can now be translated

= 2.3 (2016-03-20) =
* Play one player pause the others
* Scripts only loaded when shortcode is present
* Loop button on playlist now works as expected
* "Resume" instead of "Play" for paused players

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
= 2.7.3 (2017-04-18) =
* Filters: better logic.
* Scripts: dependencies minification. Dev versions could be loaded thanks to WordPress SCRIPT_DEBUG variable.
* Better compatibility with AJAX

= 2.7.2 (2017-04-14) =
* Continuous shortcode attribute for playlist support for the premium add-on

= 2.7.1 (2017-01-27) =
* Better internal hook names

= 2.7 (2017-01-19) =
* Add sound duration, title (fallback to post title), thumbnail and artist in playlist tracks
* Fix 'default' shortcode attribute behavior for playlists
* MultiSite friendly
* Notices fixes
* Small styles enhancements
* Possibility to enqueue/dequeue a custom icons font (default is enqueue)

= 2.6.4 (2016-12-21) =
* Updated styles for WaveSurfer-WP-Premium interactive markers system

= 2.6.3 (2016-11-29) =
AJAX Page loading compatibility is back.
JavaScript hook for custom WaveSurfer-WP player initialization

= 2.6.2 (2016-11-22) =
Fixed Playlist behavior and PHP warning error if only one id was set.

= 2.6.1 (2016-10-31) =
Fixed one attachment playlist shortocde PHP warning error.

= 2.6 (2016-10-24) =
Added new filters for bulk adding shortcode attributes or waveform data. Code examples on the plugin WordPress page.

= 2.5.3 =
Optimizations for WaveSurfer-WP Premium

= 2.5.2 =
Performance: Don't load audio unless play is clicked (waveforms are still drawn at page ready)

= 2.5.1 =
Fixed playlist with one file only issue.

= 2.5 =
Updated for WaveSurfer-WP Premium add-on. Possible regression for website which features ajax page transition (need testing).

= 2.3.1 =
Player can now be translated

= 2.3 =
Play one player pause the others, better resources loading, few other enhancements.

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
3. [Rob](http://soundpacks.com)

Do you want to contribute or sponsor one particular feature ? See you on the [donation page](https://www.extremraym.com/en/donation/). Thanks !
