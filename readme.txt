=== Multi Slider ===
Contributors: Columcille
Donate link: http://croberts.me/
Tags: slider, header, image, text
Requires at least: 3.0
Tested up to: 3.6.1
Stable tag: 1.3.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Provides a simple method to add one or more sliders to your website.

== Description ==

Provides a simple method to add one or more sliders to your website. Makes use of the outstanding jQuery Cycle plugin from http://jquery.malsup.com/cycle/ and provides some options to take advantage of the features of jQuery Cycle. Also adds very basic click tracking which tracks the number of clicks on image slides.

When you define a slider with Multi Slider, the slider is set up as a custom content type with a corresponding menu in your dashboard. Create posts of that type and add a featured image to each post and Multi Slider will automatically display the images wherever you place the slider. Slides can be either images (set as featured images) or text (anything in the post content box). 

The sliders can be placed anywhere you wish via the mslider_show_slide() function, the [mslider] shortcode, or theme widgets. The plugin makes it possible to design and place as many sliders as you like, each with their own settings.

== Installation ==

Unzip Multi Slider into your plugins folder (be sure all files are in plugins/multi_slider) and activate through the dashboard. You will see a new Multi Slider menu show up in the dashboard. Add a new slider and enjoy!

== Frequently Asked Questions ==

= What is a slider? =

Sliders define your slide collection. Say you want a rotating slide in your homepage header and another in your sidebar. These would be two different sliders, each with their own slides. 

= Why do I only see Multi-Slider in the dashboard? =

Under the Multi-Slider menu, make sure you add new sliders. Once you have created a slider, you will see it in the dashboard as a custom content type.

= How do I add a new slide? =

Add a slider in the Multi-Slider menu, then in the dashboard you should see the slider as a custom-content type. For instance, if you create the slider "Property Slideshow", this will show up as a menu in your dashboard sidebar. Under the menu for the desired slider, select Add New Slide and go from there.

Add a featured image if you want the slider to transition through pictures. If you want the picture to point to a link, add the link in the custom link area. If you want this to be text, put any markup desired in the post content area. To specify the order of your slides, use the Slide Position box on the right.

= How do I use the slider code in my theme? =

The function is mslider_show_slide($mslider_slug, $mslider_return_output) $mslider_slug should be whatever slug you defined for that slider, and $mslider_return_output is an optional boolean: should the slider return the display code, our output it? By default, the slider outputs the code. Pass true to have it return the display code instead.

== Screenshots ==

1. When you have defined sliders, they are listed under Manage Sliders and can be edited or deleted.
2. There are various options available for defining a slider, including multiple transitions.

== Changelog ==

= 1.3.1 =
* Fixed a bug causing text sliders to display text twice.

= 1.3.0 =
* Slider content gets displayed with the slider, allowing overlay text, descriptive content, etc.

= 1.2.0 =
* Updated to use responsive jQuery Cycle 2
* Note that there are a lot less transition effects available by default with jQuery Cycle 2

= 1.1.5 =
* Fixed an issue when using the slider widget

= 1.1.4 =
* Improved data validation
* Improved cross-browser presenting of flash data

= 1.1.2 =
* Fixed missing file glitch

= 1.1.1 =
* Added new Flash field to allow slides to be flash objects.

= 1.1.0 =
* mslider is now a static object

= 1.0.1 =
* Addresses an issue with file path

= 1.0.0 =
* Release version

= 0.9.0 =
* Development version. Everything should be in place.