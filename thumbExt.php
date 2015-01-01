<?php

/**
 * ThumbExt
 * Extended thumbnail function with auto generated thumbnails for html5 srcset.
 * only the pixel density descriptor is supported!
 * width descriptor should be another plugin, because those images mustn´t be auto generated 
 *
 * @package   Kirby Plugins
 * @author    Jannik Beyerstedt <jtByt.Pictures@gmail.com>
 * @link      http://jannikbeyerstedt.com
 * @copyright Jannik Beyerstedt
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
function ThumbExt($obj, $options=array()) {
  $img_tag = new img_srcset($obj, $options);
  return $img_tag;
}


class img_srcset {
  
  static public $defaults = array(
    'filename'    => '{safeName}-{hash}.{extension}',
    'filenameSrc' => '{safeName}@{pxDensity}-{hash}.{extension}',
    'srcset'      => '1x, 2x'
  );

  public $sourcePath   = null;
  public $results      = array();
  public $thumbs       = array();
  public $options      = array();
  
  public $debug       = "Hello World";
  
  /**
   * Constructor
   *
   * @param mixed $source
   * @param array $params
   */
  public function __construct($source, $params = array()) {
    
//    $this->source  = $this->result = is_a($source, 'Media') ? $source : new Media($source);
    $this->sourcePath  = $source;
    $this->options = array_merge(static::$defaults, $this->params($params));
    
    // decompose srcset descriptors
    $descriptors = array_map('trim',explode(',', $this->options['srcset']));
    
    // handle default pixel density descriptor
    $this->thumbs['1x'] = new Thumb($this->sourcePath, $this->options);
    
    foreach ($descriptors as $desc) {
      $factor = floatval($desc);
      
      // only handle non 1x pixel density descriptors, 1x is handeled above as default.
      if ($factor != 1) {        
        // include the factor in the params for the new object
        $this->options['factor'] = $factor;
        $this->options['pxDensity'] = $desc;
        
        $this->thumbs[$desc] = new ThumbSrc($this->sourcePath, $this->options);
      }
    }
  
  }
  
  /**
   * Returns the source media object
   *
   * @return Media
   */
  public function source() {
    return $this->source;
  }
  
  /**
   * Makes it possible to pass a string of params
   * which is shorter and more convenient than
   * passing a full array of keys and values:
   * width:300|height:200|crop:true
   *
   * @param array $params
   * @return array
   */
  public function params($params) {
    if(is_array($params)) return $params;
    $result = array();
    foreach(explode('|', $params) as $param) {
      $pos = strpos($param, ':');
      $result[trim(substr($param, 0, $pos))] = trim(substr($param, $pos+1));
    }
    return $result;
  }
  
  /**
   * Generates and returns the full html tag for the thumbnail
   *
   * @param array $attr An optional array of attributes, which should be added to the image tag
   * @return string
   */
  public function tag($attr = array()) {
    
    return html::img($this->thumbs['1x']->result->url(), array_merge(array(
      'alt'    => isset($this->options['alt'])   ? $this->options['alt']   : $this->sourcePath->name(),
      'width'  => $this->thumbs['1x']->result->width(),
      'height' => $this->thumbs['1x']->result->height(),
      'class'  => isset($this->options['class']) ? $this->options['class'] : null,
      'srcset' => $this->srcset_string(),
    ), $attr));
  }
  
  /**
   * Makes it possible to echo the entire object
   */
  public function __toString() {
    return $this->tag();
  }
  
  /**
   * assembles the srcset string
   */
  private function srcset_string() {    
    foreach ($this->thumbs as $tag=>$thumb) {
      if ($tag == "1x") { // first is different
        $result = $thumb->result->url() . " " . $tag;
      }else {
        $result .= ", " . $thumb->result->url() . " " . $tag;
      }
    }
    
    return $result;
  }
  
};


/**
 * ThumbSrc
 * Extends the Thumb class with another filename pattern and factorised width and height (for srcset)
 *
 * @package   Kirby Plugins
 * @author    Jannik Beyerstedt <jtByt.Pictures@gmail.com>
 * @link      http://jannikbeyerstedt.com
 * @copyright Jannik Beyerstedt
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class ThumbSrc extends Thumb {
  
  public function __construct($source, $params = array()) {

    $this->source  = $this->result = is_a($source, 'Media') ? $source : new Media($source);
    $this->options = array_merge(static::$defaults, $this->params($params));
    
    // extend original Thumb class by modified height and width
    $this->options['width'] = $this->options['width']*$this->options['factor'];
    $this->options['height'] = $this->options['height']*$this->options['factor'];
    
    $this->destination = new Obj();
    $this->destination->filename = str::template($this->options['filenameSrc'], array(
      'extension'    => $this->source->extension(),
      'name'         => $this->source->name(),
      'pxDensity'    => $this->options['pxDensity'], // extended for another name pattern
      'filename'     => $this->source->filename(),
      'safeName'     => f::safeName($this->source->name()),
      'safeFilename' => f::safeName($this->source->name()) . '.' . $this->extension(),
      'width'        => $this->options['width'],
      'height'       => $this->options['height'],
      'hash'         => md5($this->source->root() . $this->settingsIdentifier()),
    ));

    $this->destination->url  = $this->options['url'] . '/' . $this->destination->filename;
    $this->destination->root = $this->options['root'] . DS . $this->destination->filename;

    // don't create the thumbnail if it's not necessary
    if($this->isObsolete()) return;

    // don't create the thumbnail if it exists
    if(!$this->isThere()) {

      // check for a valid image
      if(!$this->source->exists() or $this->source->type() != 'image') {
        throw new Error('The given image is invalid', static::ERROR_INVALID_IMAGE);
      }

      // check for a valid driver
      if(!array_key_exists($this->options['driver'], static::$drivers)) {
        throw new Error('Invalid thumbnail driver', static::ERROR_INVALID_DRIVER);
      }

      // create the thumbnail
      $this->create();

      // check if creating the thumbnail failed
      if(!file_exists($this->destination->root)) return;

    }

    // create the result object
    $this->result = new Media($this->destination->root, $this->destination->url);

  }
  
};