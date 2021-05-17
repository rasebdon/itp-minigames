<?php
/**
 * User storage class for fast access to often used variables
 */
class Picture {
    private $id;
    private $sourcePath;
    private $thumbnailPath;

    function __construct(int $id, string $sourcePath, string $thumbnailPath)
    {
        $this->id = $id;
        $this->sourcePath = $sourcePath;
        $this->thumbnailPath = $thumbnailPath;
    }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of sourcePath
     */ 
    public function getSourcePath()
    {
        return $this->sourcePath;
    }

    /**
     * Set the value of sourcePath
     *
     * @return  self
     */ 
    public function setSourcePath($sourcePath)
    {
        $this->sourcePath = $sourcePath;

        return $this;
    }

    /**
     * Get the value of thumbnailPath
     */ 
    public function getThumbnailPath()
    {
        return $this->thumbnailPath;
    }

    /**
     * Set the value of thumbnailPath
     *
     * @return  self
     */ 
    public function setThumbnailPath($thumbnailPath)
    {
        $this->thumbnailPath = $thumbnailPath;

        return $this;
    }
}