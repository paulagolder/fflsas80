<?php

// src/AppBundle/EventListener/ImageUploadListener.php
namespace AppBundle\EventListener;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

// Include Product class and our file uploader
use AppBundle\Entity\Image;
use AppBundle\Service\FileUploader;

class ImageUploadListener
{
    private $uploader;
    private $fileName;

    public function __construct(FileUploader $uploader)
    {
        $this->uploader = $uploader;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $this->uploadFile($entity);
    }
    
    public function preUpdate(PreUpdateEventArgs $args)
    {
        // Retrieve Form as Entity
        $entity = $args->getEntity();
        
        // This logic only works for Image entities
        if (!$entity instanceof Image) {
            return;
        }
        // Check which fields were changes
        $changes = $args->getEntityChangeSet();
        
        // Declare a variable that will contain the name of the previous file, if exists.
        $previousFilename = null;
        
        // Verify if the Imagefile field was changed
        if(array_key_exists("imagefile", $changes)){
            // Update previous file name
            $previousFilename = $changes["imagefile"][0];
        }
        
        // If no new Imagefile file was uploaded
        if(is_null($entity->getImagefile())){
            // Let original filename in the entity
            $entity->setImagefile($previousFilename);

        // If a new Imagefile was uploaded in the form
        }else{
            // If some previous file exist
            if(!is_null($previousFilename)){
                $pathPreviousFile = $this->uploader->getTargetDir(). "/". $previousFilename;

                // Remove it
                if(file_exists($pathPreviousFile)){
                    unlink($pathPreviousFile);
                }
            }
            
            // Upload new file
            $this->uploadFile($entity);
        }
    }

    private function uploadFile($entity)
    {
        // upload only works for Product entities
        if (!$entity instanceof Image) {
            return;
        }

        $file = $entity->getImagefile();
        // only upload new files
        if ($file instanceof UploadedFile) {
            $fileName = $this->uploader->upload($file);
            $entity->setImagefile($fileName);
        }
    }
}
