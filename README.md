# WaveSurfer-WP #
**Contributors:** X-Raym

**Tags:** audio, player, waveform, visualization, media

**Donate link:** http://www.extremraym.com/en/donation/

**Requires at least:** 1.0.0

**Tested up to:** 4.3.1

**Stable tag:** trunk

**License:** GPL2

**License URI:** https://www.gnu.org/licenses/gpl-2.0.html


HTML5 Audio controller with waveform preview (mixed or split channels), using WordPress native audio shortcode.

## Description ##
This plugin replaces the default WordPress audio player with a player capable of displaying audio waveforms. It can display a mix of the different audio channels (for podcast, radio, e-learning, music), or all channels simultaneously (for sound tutorial, sounds-packs showcases, products demo etc...), which is is main purpose.

By working with the default audio shortcode, you have two great advantages:

*   It works with all your previous posts
*   You still have the default player in the Visual Editor (not just shortcode)
*   It supports every audio format supported by WordPress (wav, ogg, mp3, m4a).
*   Safe deactivation: if you deactivate the plugin, your shortcode will fallback to the WordPress default audio player.

Global colors and style settings can be overridden by dedicated shortcode attributes.

*   `progress_color="purple"`
*   `wave_color="#FF0000"`
*   `cursor_color="#FF0000"`
*   `download_button="true"`
*   `mute_button="true"`
*   `loop_button="true"`

The default style requires [Font-Awesome 1.0](https://fortawesome.github.io/). Because this icon-font is already used in a lot of themes and plugins, it is not included in this pack. However, if your themes and plugins doesn't have it, you can use the [Enqueue Font Awesome CDN](https://wordpress.org/plugins/font-awesome-4-menus/) WordPress plugin, or any other plugin that loads on every page.

You can deactivate the default WaveSurder-WP theme, and use your main theme style.
You can also write your own style a lot of dedicated selectors. This will allow you to have more control on icons, responsivity, mouse hover behavior etc...

[More Infos & Demo](http://www.extremraym.com/en/wavesurfer-wp)

It is a port for WordPress of WaveSurfer by katspaugh.
[wavesurfer.js](http://wavesurfer-js.org/)

It also contains [Download-js](http://danml.com/download.html) by dandavis.

You can contribute by to WaveSurfer-WP development on github:
[WaveSurfer-WP on GitHub](https://github.com/x-raym/wavesurfer-wp)
Themes and Translations are welcome !

Optimization trick: if you only use this plugin on a couple of pages, I invite you to use a plugin like [Plugin Organizer](https://wordpress.org/plugins/plugin-organizer/) to globally deactivate the plugin, and make it load resources only on pages which need it.
No need for that on the back-end, only for front-end.

Notes: Some audio formats / browsers combo are not possible. This cannot be fixed by our side. (For ex, wav 24 bits in FireFox).

## Installation ##
For an automatic installation through WordPress:

1. Go to the *Add New* plugins screen in your WordPress admin area
1. Search for *WaveSurfer-WP*
1. Click *Install Now* and activate the plugin

For a manual installation via FTP:

1. Upload the `wavesurfer-wp` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the *Plugins* screen in your WordPress admin area

To upload the plugin through WordPress, instead of FTP:

1. Upload the downloaded zip file on the Add New* plugins screen (see the *Upload* tab) in your WordPress admin area and activate.

## Frequently Asked Questions ##
Be the first to ask!

## Screenshots ##
###1. Front-End Settings Page
###
![Front-End Settings Page
](https://ps.w.org/wavesurfer-wp/assets/screenshot-1.png)

###2. Back-End Settings Page
###
![Back-End Settings Page
](https://ps.w.org/wavesurfer-wp/assets/screenshot-2.png)


## Changelog ##
### 1.1.0 (2015-11-30) ###
+ Cursor Color setting and shortcode
+ Download Button shortcode
+ Loop Button shortcode
+ Mute Button shortcode
+ Flat Icons theme
# Play/Pause buttons have merged

### 1.0.0 (2015-11-24) ###
First release.

## Upgrade Notice ##
### 1.0.0 ###
Initial release.
