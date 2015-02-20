# kirby2 plugin thumbExt
by jtByt.Pictures, Jannik Beyerstedt from Hamburg, Germany  
[jannik.beyerstedt.de](http://jannik.beyerstedt.de) | [Github](https://github.com/jbeyerstedt)  


## get a thumbnail with srcset and inline image size

This plugin extends the thumb class of kirby 2 with some nice additions regarding the srcset attribute for devices with higher pixel density. It also adds the thumbnails width and height to the img tag, so that the layout is displayed correctly even if the content is not loaded completely. I needed this for my masonry-style image gallery.

#### note:
This is only tested with kirby 2!

#### how to use
store this file in
	
	site/plugins/

use it in your templates the same way as the "normal" thumb function:

    ThumbExt($pic, array('width' => 200, 'srcset' => '2x, 3x'))
    
the attributes are the same as the standard thumb function, but I´ve added:

- srcset: string (with the pixel density descriptors, seperated by commas)
- inline-size: boolean (default true)  
you can turn off the inline size feature of this plugin

## contribution
Feel free to fork this repository an make it better.  
Perhaps we can implement the viewport width feature in some way.
