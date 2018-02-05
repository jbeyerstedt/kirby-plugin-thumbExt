# Kirby thumbExt Plugin
by Jannik Beyerstedt from Hamburg, Germany  
[jannikbeyerstedt.de](http://jannikbeyerstedt.de) | [Github](https://github.com/jbeyerstedt)  
**license:** GNU GPL v3  
**version:** v1.0.1

## Introduction
**Kirby Plugin for thumbnails with srcset for different device resolutions**

This plugin extends the thumb class of kirby with some nice additions regarding the srcset attribute for devices with higher pixel density.  
It also adds the thumbnails width and height to the img tag, so that the layout is displayed correctly even if the content is not loaded completely. I needed this for my masonry-style image gallery.

Another thing to mention is, that this plugin does only support the pixel density descriptor (@2x, etc.), because defining different images for different sizes will add too much complexity.
With different sources for different devices you can have a very good adaption of your site to the different screen sizes, but there are simply too many different cases and method to add all these in the attributes.  
(But feel free to fork and add some functions for your own usecases or add a suggestion in the issues section of github.)

## User Manual

#### How to Use
store this file in

    site/plugins/

use it in your templates the same way as the "normal" thumb function:

    ThumbExt($pic, array('width' => 200, 'srcset' => '2x, 3x'))

the attributes are the same as the standard thumb function, but I’ve added:

- srcset: string (with the pixel density descriptors, separated by commas)
if you want 1x only, simply type `'1x'`. But 1x is always added, so you don’t have to type `'1x, 2x'`, you can leave 1x out.
- inline-size: boolean (default true)
you can turn off the inline size feature of this plugin
- srcset-only: boolean (default false)
this enables that there is only the srcset-string returned, e.g. for [picturefill](http://scottjehl.github.io/picturefill/).

The standard function of this plugin is, that this returns a full img-tag ready to use in your template or snippet.  
By default 1x and 2x images are generated, which is the most often used case. But you can define any pixel ratio you want to.  


## Contribution
Feel free to fork this repository and make it better.  
Perhaps we can implement the viewport width feature in some way.
