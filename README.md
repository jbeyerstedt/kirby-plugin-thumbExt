# kirby2 plugin thumbExt
by jtByt.Pictures, Jannik Beyerstedt from Hamburg, Germany  
[jannik.beyerstedt.de](http://jannik.beyerstedt.de) | [Github](https://github.com/jbeyerstedt)  


## get kirbytext without the sorrounding p-tags

#### note:
This is only tested with kirby 2!

#### how to use
store this file in
	
	site/plugins/

use it in your templates the same way as the "normal" thumb function:

    ThumbExt($pic, array('width' => 200, 'srcset' => '2x, 3x'))
    
the attributes are the same as the standard thumb function, but I´ve added:

- srcset: string (with the pixel density descriptors, seperated by commas)
