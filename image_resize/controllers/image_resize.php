<?php

class ImageResizeController extends Controller {
    public function resize_image() {
        $originalPath = Settings::getValue("uploads", "files").$this->getMatch("path");

        $width  = intval($this->getMatch("width"));
        $height = $this->getMatch("height") !== null ? intval($this->getMatch("height")) : null;

        if ($width  === 0 ||
            $height === 0 ||
            $width  > Settings::getValue("images", "max_width") ||
            $height > Settings::getValue("images", "max_height")) {

            $this->setResponseCode(404);
            $this->response->addHeader("Content-Type", "text/plain");
            $this->response->setBody("Invalid dimensions");
            return;
        }
            
        if (!file_exists($originalPath)) {
            Log::debug("Input file [".$originalPath."] does not exist");
            $this->setResponseCode(404);
            $this->response->addHeader("Content-Type", "text/plain");
            $this->response->setBody("Image not found");
            return;
        }

        $targetPath = Settings::getValue("uploads", "scaled").$width;
        if ($height !== null) {
            $targetPath .= "x".$height;
        }

        if (!is_dir($targetPath)) {
            Log::debug("Directory [".$targetPath."] does not exist, attempting to create");
            mkdir($targetPath);
        }
        $targetPath .= "/".$this->getMatch("path");

        if ($height !== null) {
            $result = JaossImage::resizeCrop(
                $originalPath,
                $targetPath,
                $width,
                $height
            );
        } else {
            $result = JaossImage::resizeWidth(
                $originalPath,
                $targetPath,
                $width
            );
        }

        if ($result === false) {
            $this->setResponseCode(500);
            $this->response->addHeader("Content-Type", "text/plain");
            $this->response->setBody("Resize failed");
            return;
        }

        $info = getimagesize($targetPath);
        $this->response->addHeader("Content-Type", $info["mime"]);
        $this->response->setBody(
            file_get_contents($targetPath)
        );
    }
}
