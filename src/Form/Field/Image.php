<?php

namespace Dcat\Admin\Form\Field;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @method $this backup(string $name = 'default') Backups current image state as fallback for reset method under an optional name. Overwrites older state on every call, unless a different name is passed.
 * @method $this blur(int $amount = 1) Apply a gaussian blur filter with a optional amount on the current image. Use values between 0 and 100.
 * @method $this brightness(int $level) Changes the brightness of the current image by the given level. Use values between -100 for min. brightness. 0 for no change and +100 for max. brightness.
 * @method $this cache(\Closure $callback, int $lifetime = null, boolean $returnObj = false) Method to create a new cached image instance from a Closure callback. Pass a lifetime in minutes for the callback and decide whether you want to get an Intervention Image instance as return value or just receive the image stream.
 * @method $this canvas(int $width, int $height, mixed $bgcolor = null) Factory method to create a new empty image instance with given width and height. You can define a background-color optionally. By default the canvas background is transparent.
 * @method $this circle(int $radius, int $x, int $y, \Closure $callback = null) Draw a circle at given x, y, coordinates with given radius. You can define the appearance of the circle by an optional closure callback.
 * @method $this colorize(int $red, int $green, int $blue) Change the RGB color values of the current image on the given channels red, green and blue. The input values are normalized so you have to include parameters from 100 for maximum color value. 0 for no change and -100 to take out all the certain color on the image.
 * @method $this contrast(int $level) Changes the contrast of the current image by the given level. Use values between -100 for min. contrast 0 for no change and +100 for max. contrast.
 * @method $this crop(int $width, int $height, int $x = null, int $y = null) Cut out a rectangular part of the current image with given width and height. Define optional x,y coordinates to move the top-left corner of the cutout to a certain position.
 * @method $this ellipse(int $width, int $height, int $x, int $y, \Closure $callback = null) Draw a colored ellipse at given x, y, coordinates. You can define width and height and set the appearance of the circle by an optional closure callback.
 * @method $this exif(string $key = null) Read Exif meta data from current image.
 * @method $this iptc(string $key = null) Read Iptc meta data from current image.
 * @method $this flip(string $mode = 'h') Mirror the current image horizontally or vertically by specifying the mode.
 * @method $this fit(int $width, int $height = null, \Closure $callback = null, string $position = 'center') Combine cropping and resizing to format image in a smart way. The method will find the best fitting aspect ratio of your given width and height on the current image automatically, cut it out and resize it to the given dimension. You may pass an optional Closure callback as third parameter, to prevent possible upsizing and a custom position of the cutout as fourth parameter.
 * @method $this gamma(float $correction) Performs a gamma correction operation on the current image.
 * @method $this greyscale() Turns image into a greyscale version.
 * @method $this heighten(int $height, \Closure $callback = null) Resizes the current image to new height, constraining aspect ratio. Pass an optional Closure callback as third parameter, to apply additional constraints like preventing possible upsizing.
 * @method $this insert(mixed $source, string $position = 'top-left', int $x = 0, int $y = 0) Paste a given image source over the current image with an optional position and a offset coordinate. This method can be used to apply another image as watermark because the transparency values are maintained.
 * @method $this interlace(boolean $interlace = true) Determine whether an image should be encoded in interlaced or standard mode by toggling interlace mode with a boolean parameter. If an JPEG image is set interlaced the image will be processed as a progressive JPEG.
 * @method $this invert() Reverses all colors of the current image.
 * @method $this limitColors(int $count, mixed $matte = null) Method converts the existing colors of the current image into a color table with a given maximum count of colors. The function preserves as much alpha channel information as possible and blends transarent pixels against a optional matte color.
 * @method $this line(int $x1, int $y1, int $x2, int $y2, \Closure $callback = null) Draw a line from x,y point 1 to x,y point 2 on current image. Define color and/or width of line in an optional Closure callback.
 * @method $this make(mixed $source) Universal factory method to create a new image instance from source, which can be a filepath, a GD image resource, an Imagick object or a binary image data.
 * @method $this mask(mixed $source, boolean $mask_with_alpha) Apply a given image source as alpha mask to the current image to change current opacity. Mask will be resized to the current image size. By default a greyscale version of the mask is converted to alpha values, but you can set mask_with_alpha to apply the actual alpha channel. Any transparency values of the current image will be maintained.
 * @method $this opacity(int $transparency) Set the opacity in percent of the current image ranging from 100% for opaque and 0% for full transparency.
 * @method $this orientate() This method reads the EXIF image profile setting 'Orientation' and performs a rotation on the image to display the image correctly.
 * @method $this pickColor(int $x, int $y, string $format = 'array') Pick a color at point x, y out of current image and return in optional given format.
 * @method $this pixel(mixed $color, int $x, int $y) Draw a single pixel in given color on x, y position.
 * @method $this pixelate(int $size) Applies a pixelation effect to the current image with a given size of pixels.
 * @method $this polygon(array $points, \Closure $callback = null) Draw a colored polygon with given points. You can define the appearance of the polygon by an optional closure callback.
 * @method $this rectangle(int $x1, int $y1, int $x2, int $y2, \Closure $callback = null) Draw a colored rectangle on current image with top-left corner on x,y point 1 and bottom-right corner at x,y point 2. Define the overall appearance of the shape by passing a Closure callback as an optional parameter.
 * @method $this reset(string $name = 'default') Resets all of the modifications to a state saved previously by backup under an optional name.
 * @method $this resize(int $width, int $height = null, \Closure $callback = null) Resizes current image based on given width and/or height. To contraint the resize command, pass an optional Closure callback as third parameter.
 * @method $this resizeCanvas(int $width, int $height, string $anchor = 'center', boolean $relative = false, mixed $bgcolor = null) Resize the boundaries of the current image to given width and height. An anchor can be defined to determine from what point of the image the resizing is going to happen. Set the mode to relative to add or subtract the given width or height to the actual image dimensions. You can also pass a background color for the emerging area of the image.
 * @method $this rotate(float $angle, mixed $bgcolor = null) Rotate the current image counter-clockwise by a given angle. Optionally define a background color for the uncovered zone after the rotation.
 * @method $this sharpen(int $amount = 10) Sharpen current image with an optional amount. Use values between 0 and 100.
 * @method $this text(string $text, int $x = 0, int $y = 0, \Closure $callback = null) Write a text string to the current image at an optional x,y basepoint position. You can define more details like font-size, font-file and alignment via a callback as the fourth parameter.
 * @method $this trim(string $base = 'top-left', array $away = array('top', 'bottom', 'left', 'right'), int $tolerance = 0, int $feather = 0) Trim away image space in given color. Define an optional base to pick a color at a certain position and borders that should be trimmed away. You can also set an optional tolerance level, to trim similar colors and add a feathering border around the trimed image.
 * @method $this widen(int $width, \Closure $callback = null) Resizes the current image to new width, constraining aspect ratio. Pass an optional Closure callback as third parameter, to apply additional constraints like preventing possible upsizing.
 */
class Image extends File
{
    use ImageField;

    protected $rules = ['nullable', 'image'];

    protected $view = 'admin::form.file';

    public function __construct($column, $arguments = [])
    {
        parent::__construct($column, $arguments);

        $this->setupImage();
    }

    protected function setupImage()
    {
        if (! isset($this->options['accept'])) {
            $this->options['accept'] = [];
        }

        $this->options['accept']['mimeTypes'] = 'image/*';
        $this->options['isImage'] = true;
    }

    /**
     * @param  array  $options  support:
     *                          [
     *                          'width' => 100,
     *                          'height' => 100,
     *                          'min_width' => 100,
     *                          'min_height' => 100,
     *                          'max_width' => 100,
     *                          'max_height' => 100,
     *                          'ratio' => 3/2, // (width / height)
     *                          ]
     * @return $this
     */
    public function dimensions(array $options)
    {
        if (! $options) {
            return $this;
        }

        $this->mergeOptions(['dimensions' => $options]);

        foreach ($options as $k => &$v) {
            $v = "$k=$v";
        }

        return $this->rules('dimensions:'.implode(',', $options));
    }

    /**
     * Set ratio constraint.
     *
     * @param  float  $ratio  width/height
     * @return $this
     */
    public function ratio($ratio)
    {
        if ($ratio <= 0) {
            return $this;
        }

        return $this->dimensions(['ratio' => $ratio]);
    }

    /**
     * @param  UploadedFile  $file
     */
    protected function prepareFile(UploadedFile $file)
    {
        $this->callInterventionMethods($file->getRealPath(), $file->getMimeType());

        $this->uploadAndDeleteOriginalThumbnail($file);
    }
}
