#### Installation
store this file in

    site/plugins/

#### Usage
use it in your templates the same way as the "normal" thumb function:

    ThumbExt($pic, array('width' => 200, 'srcset' => '2x, 3x'))

the attributes are the same as the standard thumb function, but I´ve added:

- srcset: string (with the pixel density descriptors, seperated by commas)
if you want 1x only, simply type `'1x'`. But 1x is always added, so you don´t have to type `'1x, 2x'`, you can leave 1x out.
- inline-size: boolean (default true)
you can turn off the inline size feature of this plugin
- srcset-only: boolean (default false)
this enables that there is only the srcset-string returned, e.g. for [picturefill](http://scottjehl.github.io/picturefill/).

The standard function of this plugin is, that this returns a full img-tag ready to use in your template or snippet.
By default 1x and 2x images are generated, which is the most often used case. But you can define any pixel ratio you want to.

## contribution
Feel free to fork this repository an make it better.
Perhaps we can implement the viewport width feature in some way.
