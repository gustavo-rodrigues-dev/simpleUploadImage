<?php
class ImageModify
{
    private $extensions = array('jpg', 'jpeg', 'pjpeg', 'png', 'gif');

    protected $source;
    protected $path;
    protected $modeResize = self::RESIZE_MODE_SCALE;
    protected $anchor = "";
    protected $mask;
    protected $font = "arial.ttf";
    protected $textSize = 12;
    protected $string;

    public $name;
    public $pathImages = "data/uploaded/";
    public $extension;

    public $width;
    public $height;
    public $imageQuality = 100;
    public $positionCrop = array();

    const RESIZE_MODE_SCALE = 0;
    const RESIZE_MODE_PROPORTIONAL = 1;
    const RESIZE_MODE_AR_WIDTH = 2;
    const RESIZE_MODE_AR_HEIGHT = 3;
    const RESIZE_MODE_SQUARE = 4;
    const RESIZE_MODE_AUTO = 5;

    public function __construct(){

        $options = func_get_args();

        if(count($options) > 0){

            if(strpos($options[0], 'http://') !== false){
                $this->openByExternal($options[0]);

            }elseif(is_string($options[0])){
                $this->open($options[0]);

            }elseif(is_numeric($options[0]) and is_numeric($options[1])){
                $this->create($options[0], $options[1]);

            }elseif(is_resource($options[0]) and get_resource_type($options[0]) == "gd"){
                $this->setSource($options[0]);

            }

        }

    }

    /**
     * @param string $anchor
     * @return ImageModify
     */
    public function setAnchor($anchor){
        $this->anchor = $anchor;
        return $this;
    }

    /**
     * @return string
     */
    public function getAnchor(){
        return $this->anchor;
    }

    /**
     * @param integer $mode
     * @return ImageModify
     */
    public function setModeResize($mode){
        $this->modeResize = $mode;
        return $this;
    }

    /**
     * @return integer
     */
    public function getModeResize(){
        return $this->modeResize;
    }

    /**
     * @param string $path
     * @return ImageModify
     */
    public function setPath($path){
        $this->path = $path;
        return $this->setExtensionByPath($path);
    }

    /**
     * @return string
     */
    public function getPath(){
        return $this->path;
    }

    /**
     * @param resource $source
     * @return ImageModify
     */
    public function setSource($source){
        $this->source = $source;
        $this->width = imagesx($this->source);
        $this->height = imagesy($this->source);
        return $this;
    }

    /**
     * @return resource
     */
    public function getSource(){
        return $this->source;
    }

    /**
     * @param string $maskPath
     * @return ImageModify
     */
    public function setMaskByPath($maskPath)
    {
        $this->mask = imagecreatefrompng($this->anchor . $maskPath);

        imagealphablending($this->mask, true);
        imagesavealpha($this->mask, true);

        return $this;
    }

    /**
     * @param resource $mask
     * @return ImageModify
     */
    public function setMask($mask)
    {
        $this->mask = $mask;
        return $this;
    }

    /**
     * @return resource
     */
    public function getMask()
    {
        return $this->mask;
    }

    /**
     * @param string $path
     * @return ImageModify
     */
    public function setExtensionByPath($path){
        $path_parts = pathinfo($path);

        $extension = $path_parts['extension'];
        if(in_array($extension, $this->extensions)){
            $this->extension = $extension;
        }else{
            $this->extension = "png";
        }
        return $this;
    }

    /**
     * @param string $imagePath
     * @param bool $anchor
     * @return ImageModify
     */
    public function open($imagePath, $anchor = false) {

        $this->path = $imagePath;

        if($anchor) {
            $imagePath = $this->anchor($imagePath);

        }else{
            $imagePath = $this->anchor . $imagePath;
        }

        $this->setExtensionByPath($imagePath);

        switch ($this->extension) {
            case 'jpg':
            case 'jpeg':
            case 'pjpeg':
                $this->source = imagecreatefromjpeg($imagePath);
                break;
            case 'gif':
                $this->source = imagecreatefromgif($imagePath);
                break;
            case 'png':
            default:
                $this->source = imagecreatefrompng($imagePath);
        }
        $this->width = imagesx($this->source);
        $this->height = imagesy($this->source);
        return $this->applyAlpha();
    }

    /**
     * @param array $file
     * @return ImageModify
     */

    public function openByFile($file) {
        $imagePath = $file['tmp_name'];
        $this->extension = substr(strrchr($file['type'], "/"), 1);
        switch ($this->extension) {
            case 'jpg':
            case 'jpeg':
            case 'pjpeg':
                $this->source = imagecreatefromjpeg($imagePath);
                break;
            case 'gif':
                $this->source = imagecreatefromgif($imagePath);
                break;
            case 'png':
            default:
                $this->source = imagecreatefrompng($imagePath);
        }
        $this->width = imagesx($this->source);
        $this->height = imagesy($this->source);
        return $this->applyAlpha();
    }

    /** Retorna data image da imagem
     * @param array $file
     * @return string Image
     */
    public function dataImage($file){
        $contents   = file_get_contents($file['tmp_name']);
        $base64     = base64_encode($contents);
        return ('data:' . $file['type'] . ';base64,' . $base64);
    }

    /**
     * @param string $url
     * @return ImageModify
     */
    public function openByExternal($url){
        $ch = curl_init();

        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt ($ch, CURLOPT_HEADER, false);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 0);

        $rawData = curl_exec($ch);

        $image = imagecreatefromstring($rawData);

        curl_close($ch);

        $this->setSource($image);
        return $this;
    }


    /**
     * @param string $savePath
     * @return ImageModify
     */
    public function saveImage($savePath = null) {

        $savePath or $savePath = $this->path;

        //Captura a extensão da miagem
        $this->extension or $this->extension = strrchr($savePath, '.');
        $this->extension = strtolower($this->extension);

        switch ($this->extension) {
            case 'jpg':
            case 'jpeg':
            case 'pjpeg':
                if (imagetypes() & IMG_JPG) {
                    imagejpeg($this->source, $savePath, $this->imageQuality);
                }
                break;

            case 'gif':
                if (imagetypes() & IMG_GIF) {
                    imagegif($this->source, $savePath);
                }
                break;

            case 'png':
                // *** Scale quality from 0-100 to 0-9
                $scaleQuality = round(($this->imageQuality / 100) * 9);

                // *** Invert quality setting as 0 is best, not 9
                $invertScaleQuality = 9 - $scaleQuality;

                if (imagetypes() & IMG_PNG) {
                    imagepng($this->source, $savePath, $invertScaleQuality);
                }
                break;
            default:
                break;
        }
        $this->path = $savePath;
        return $this;
    }

    /**
     * @param integer $width
     * @param integer $height
     * @param string $backgroundColor
     * @return ImageModify
     */
    public function create($width, $height, $backgroundColor = null){
        $this->source = imagecreatetruecolor($width, $height);

        return $this->applyAlpha($backgroundColor);
    }

    public function applyAlpha($backgroundColor = null){
        imagealphablending($this->source, true);
        imagesavealpha($this->source, true);

        if($backgroundColor != null) {
            imagefill($this->source,0,0, $this->hex2Color($backgroundColor, true));
        }
        return $this;
    }

    /**
     * @param integer $width
     * @param integer $height
     * @param string $backgroundColor
     * @return ImageModify
     */
    public function resize($width, $height, $backgroundColor = null , $x= 0, $y = 0) {

        $resize = new self;
        $resize->create($width, $height, $backgroundColor);

        $orig_W = imagesx($this->source);
        $orig_H = imagesy($this->source);

        switch ($this->modeResize) {
            case self::RESIZE_MODE_PROPORTIONAL:

                if($orig_W > $orig_H) {

                    $thumb_W = $width;
                    $thumb_H = ($width * $orig_H) / $orig_W;
                    $thumb_X = ($width / 2) - ($thumb_W) / 2;
                    $thumb_Y = ($height / 2) - ($thumb_H) / 2;

                } else if($orig_W < $orig_H) {

                    $thumb_W = ($orig_W * $height) / $orig_H;
                    $thumb_H = $height;
                    $thumb_X = ($width / 2) - ($thumb_W) / 2;
                    $thumb_Y = ($height / 2) - ($thumb_H) / 2;

                } else {

                    $thumb_W = $width;
                    $thumb_H = $height;
                    $thumb_X = 0;
                    $thumb_Y = 0;

                }

                break;

            case self::RESIZE_MODE_AR_WIDTH:

                $thumb_W = $width;
                $thumb_H = ($width * $orig_H) / $orig_W;
                $thumb_X = ($width / 2) - ($thumb_W) / 2;
                $thumb_Y = ($height / 2) - ($thumb_H) / 2;

                break;

            case self::RESIZE_MODE_AR_HEIGHT:

                $thumb_W = ($orig_W * $height) / $orig_H;
                $thumb_H = $height;
                $thumb_X = ($width / 2) - ($thumb_W) / 2;
                $thumb_Y = ($height / 2) - ($thumb_H) / 2;

                break;

            case self::RESIZE_MODE_SQUARE:

                if($orig_W > $orig_H) {

                    $thumb_W = $width;
                    $thumb_H = ($width * $orig_H) / $orig_W;
                    $thumb_X = 0;
                    $thumb_Y = ($height / 2) - ($thumb_H) / 2;

                } else if($orig_W < $orig_H) {

                    $thumb_W = ($orig_W * $height) / $orig_H;
                    $thumb_H = $height;
                    $thumb_X = ($width / 2) - ($thumb_W) / 2;
                    $thumb_Y = 0;

                } else {

                    $thumb_W = $width;
                    $thumb_H = $height;
                    $thumb_X = 0;
                    $thumb_Y = 0;

                }

                break;
            case self::RESIZE_MODE_AUTO:

                if($orig_W > $orig_H) {

                    $thumb_W = $width;
                    $thumb_H = ($width * $orig_H) / $orig_W;
                    $thumb_X = 0;
                    $thumb_Y = 0;

                } else {
                    $thumb_W = ($orig_W * $height) / $orig_H;
                    $thumb_H = $height;
                    $thumb_X = 0;
                    $thumb_Y = 0;
                }

                $resize->create($thumb_W, $thumb_H, $backgroundColor);
                break;

            case self::RESIZE_MODE_SCALE:
            default:

                $thumb_W = $width;
                $thumb_H = $height;
                $thumb_X = 0;
                $thumb_Y = 0;

                break;
        }

            

        imagecopyresampled($resize->source, $this->source, $thumb_X, $thumb_Y, $x, $y, $thumb_W, $thumb_H, $orig_W, $orig_H);
        $this->source = $resize->source;
        $this->width = $width;
        $this->height = $height;
        return $this;
    }


    /**
     * @return ImageModify
     */
    public function blackOnWhite(){
        imagefilter($this->source, IMG_FILTER_GRAYSCALE);
        return $this;
    }

    /**
     * @param integer $imageFilter
     * @return ImageModify
     */
    public function applyFilter($imageFilter){
        imagefilter($this->source, $imageFilter);
        return $this;
    }

    /**
     * @param resource $mask
     * @return ImageModify
     */
    public function overlap($mask = null){
        if(isset($mask)){
            $this->mask = $mask;
        }

        imagecopy($this->source, $this->mask, 0, 0, 0, 0, $this->width, $this->height);
        return $this;
    }

    public function merge($x, $y){
        $markWidth  = imagesx($this->mask);
        $markHeight = imagesy($this->mask);

        $canvas = new self;
        $canvas->create($this->width, $this->height);

        imagecopyresampled($canvas->source, $this->source, 0, 0, 0, 0, $this->width, $this->height, $this->width, $this->height);
        $this->source = $canvas->source;
        imagecopy($this->source, $this->mask, $x, $y, 0, 0, $markWidth, $markHeight);
        return $this;
    }

    /**
     * @param resource $mask
     * @return ImageModify
     */
    public function applyAlphaMask($mask = null) {

        if(isset($mask)){
            $this->mask = $mask;
        }

        $canvas = new self();
        $canvas->create($this->width, $this->height, "#000000");

        for($x = 0; $x < $this->width; $x++) {
            for($y = 0; $y < $this->height; $y++) {

                $alphaAtMask = imagecolorsforindex($this->mask, imagecolorat( $this->mask, $x, $y ));

                //$clist = "{$alphaAtMask['alpha']}";
                //echo '<span style="display:block;float:left;background:' . $clr . ';width:70px;height:10px; color: white; font-size:10px;font-family:arial;>' . $clist . '</span>';

                $colorAt = imagecolorsforindex($this->source, imagecolorat($this->source, $x, $y));
                imagesetpixel($canvas->source, $x, $y, imagecolorallocatealpha($canvas->source, $colorAt['red'], $colorAt['green'], $colorAt['blue'], $alphaAtMask['alpha']));

            }
        }
        $this->path = str_replace(".jpg", ".png", $this->path);
        $this->extension = "png";
        $this->source = $canvas->source;

        return $this;

    }


    /**
     * Calcula as coordenadas do eixo X para posicionar uma string de texto no centro da imagem
     * @param int $width A largura da imagem
     * @return float A posição no eixo X
     */
    public function calculateTextCenter($width) {
        $bb = imagettfbbox($this->textSize, 0, $this->font, $this->string);
        $text_width = $bb[2] - $bb[0];
        return ($width/2) - ($text_width/2);
    }

    /**
     * Calcula as coordenadas do eixo Y para posicionar uma string de texto no centro da imagem
     * @param int $height A altura da imagem
     * @return float A posição no eixo Y
     */
    public function calculateTextMiddle($height) {
        $bb = imagettfbbox($this->textSize, 0, $this->font, $this->string);
        $text_height = $bb[1] - $bb[3];
        return ($height/2) - ($text_height/2);
    }

    /**
     * Renderiza uma linha de texto na imagem
     * @param string $string A linha de texto
     * @param integer $x A posição no eixo X
     * @param integer $y A posição no eixo Y
     * @param integer $size O tamanho da fonte
     * @param string $color A cor em hexadecimal completo (Ex.: #FFFFFF)
     * @return ImageModify
     */
    public function addText($string, $x, $y, $size = 12, $color = "#FFFFFF") {
        $this->string = $string;
        $this->textSize = $size;
        $color = self::hex2Color($this->source, $color);

        imagettftext($this->source, $size, 0, $x, $y, $color, $this->font, $this->string);

        return $this;
    }

    /**
     * @param string $hexStr
     * @param boolean $alpha
     * @return integer
     */
    public function hex2Color($hexStr, $alpha = null) {

        $colorVal = hexdec($hexStr);
        $color_R = 0xFF & ($colorVal >> 0x10);
        $color_G = 0xFF & ($colorVal >> 0x8);
        $color_B = 0xFF & $colorVal;

        if($alpha){
            return imagecolorallocatealpha($this->source, $color_R, $color_G, $color_B, 127);
        }

        return imagecolorallocate($this->source, $color_R, $color_G, $color_B);

    }

    /**
     * Extrai o caminho correto absoluto
     *
     * @static
     * @param string $path O caminho original
     * @return string O caminho absoluto
     */
    public static function getAbsolutePath($path) {
        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
        $absolutes = array();
        foreach ($parts as $part) {
            if ('.' == $part)
                continue;
            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        return implode(DIRECTORY_SEPARATOR, $absolutes);
    }

    /**
     * @param string $location
     * @return string
     */
    public function anchor($location) {
        return ((!defined('ENV_WINDOWS'))?"/":"").self::getAbsolutePath(dirname(__FILE__) . "/../../{$location}");
    }

    public function render(){
        header("Content-type: image/png");
        imagepng($this->source);
        imagedestroy($this->source);
        exit;
    }

}