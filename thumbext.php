<?php

/**
 * ThumbExt
 * Extended thumbnail function with auto generated thumbnails for html5 srcset.
 * only the pixel density descriptor is supported!
 * width descriptor should be another plugin, because those images mustn´t be auto generated
 *
 * @package   Kirby Plugins
 * @author    Jannik Beyerstedt <code@jannikbeyerstedt.de>
 * @link      http://jannikbeyerstedt.com
 * @copyright Jannik Beyerstedt
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt GPLv3 License
 */
function ThumbExt($obj, $options=array()) {
  $thumb_tag = new thumb_srcset($obj, $options);
  return $thumb_tag;
}


class thumb_srcset {

  static public $defaults = array(
    'srcset'      => '1x, 2x',
    'inline-size' => true,
    'srcset-only' => false
  );

  public $sourcePath   = null;
  public $thumbs       = array(); // stores all thumb objects
  public $options      = array();

  /**
   * Constructor
   *
   * @param mixed $source
   * @param array $params
   */
  public function __construct($source, $params = array()) {
    $this->sourcePath  = $source;
    $this->options = array_merge(static::$defaults, $this->params($params));

    // decompose srcset descriptors
    $descriptors = array_map('trim',explode(',', $this->options['srcset']));

    // handle default pixel density descriptor
    $this->thumbs['1x'] = Thumb($this->sourcePath, $this->options);

    foreach ($descriptors as $desc) {
      $factor = floatval($desc);

      // only handle non 1x pixel density descriptors, 1x is handeled above as default.
      if ($factor != 1) {
        // copy all options and adjust the width and height with the custom factor
        $scaledOptions = $this->options;
        if (isset($this->options['width'])) { // width must be set every time! no height alone!
          $scaledOptions['width'] = $this->options['width']*$factor;
          if (isset($this->options['height'])) {
            $scaledOptions['height'] = $this->options['height']*$factor;
          }
        }else {
          throw new Error('No width or height value set');
        }

        $this->thumbs[$desc] = Thumb($this->sourcePath, $scaledOptions);
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
    if ($this->options['inline-size'] && !$this->options['srcset-only']) { //if srcset-only, then no inline-size possible
      return html::img($this->thumbs['1x']->url(), array_merge(array(
        'alt'    => isset($this->options['alt'])   ? $this->options['alt']   : $this->sourcePath->name(),
        'width'  => $this->thumbs['1x']->width(),
        'height' => $this->thumbs['1x']->height(),
        'class'  => isset($this->options['class']) ? $this->options['class'] : null,
        'srcset' => $this->srcset_string(),
      ), $attr));

    }else if($this->options['srcset-only']){
      return $this->srcset_string();

    }else{
      return html::img($this->thumbs['1x']->url(), array_merge(array(
        'alt'    => isset($this->options['alt'])   ? $this->options['alt']   : $this->sourcePath->name(),
        'class'  => isset($this->options['class']) ? $this->options['class'] : null,
        'srcset' => $this->srcset_string(),
      ), $attr));
    }


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
        $result = $thumb->url() . " " . $tag;
      }else {
        $result .= ", " . $thumb->url() . " " . $tag;
      }
    }

    return $result;
  }

};
