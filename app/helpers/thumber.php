<?php

Use Nette\Utils\Image;

class ThumberHelper extends Nette\Object
{
    private $context;
    private $basePath;
    private $baseUrl;
    private $thumbPath;

    public function __construct($context)
    {
        $this->context      = $context;
        $this->baseUrl      = $this->context->httpRequest->url->basePath;
        $this->basePath     = $this->context->parameters['wwwDir'];
        $this->thumbPath    = $this->basePath . '/thumbs';
    }

    public function loader($helper)
    {
        if (method_exists($this, $helper)) {
            return callback($this, $helper);
        }
    }

    public function thumber($path, $width = 100, $height = 100, $method = 'fit')
    {

        $width  = intval($width);
        $height = intval($height);

        // original file
        $originalUrl = $this->baseUrl . $path;
        $originalPath = $this->basePath . '/' .  $path;

        // new file
        $newPath        = $this->getThumbFileName($path, $width, $height);;
        $thumbUrl       = $this->baseUrl . 'thumbs/' .  $newPath;
        $thumbPath      = $this->thumbPath . '/' . $newPath;

        // cereating new file
        if(file_exists($originalPath)) {

        // create image
            $image = Image::fromFile($originalPath);

            if($method == 'crop') {
                $image->crop('50%', '50%', $width, $height);
            } else {

                // resolving method
                switch ($method) {
                    case('fit'):
                        $netteMethod = Image::FIT;
                        break;
                    case('fill'):
                        $netteMethod = Image::FILL;
                        break;
                    case('exact'):
                        $netteMethod = Image::EXACT;
                        break;
                    case('shrink_only'):
                        $netteMethod = Image::SHRINK_ONLY;
                        break;
                    case('stretch'):
                        $netteMethod = Image::STRETCH;
                        break;
                    default:
                        $netteMethod = Image::FIT;
                }

                // resize image
                $image->resize($width, $height, $netteMethod);
            }

            // save image (but make sure the directories are created first
            if(!is_dir(dirname($thumbPath))) {
                mkdir(dirname($thumbPath), 0777, 1);
            }

            // sharpen image
            $image->sharpen();

            // save thumbnail
            $image->save($thumbPath);

        } else {
            // todo osetrit
        }

        $output = '<img src="'. $thumbUrl .'" />';

        return $output;
    }

    private function getThumbFileName($filename, $width, $height)
    {

        // new file
        $lastDot    = strrpos($filename, ".");
        $ext        = substr($filename, $lastDot);
        $dir        = substr($filename, 0, $lastDot);

        $thumbPath = $dir . '_' . $width . 'x' . $height . $ext;

        return $thumbPath;
    }

}