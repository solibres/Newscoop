<?php

namespace Newscoop\Entity\Proxy;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class NewscoopEntityPublicationProxy extends \Newscoop\Entity\Publication implements \Doctrine\ORM\Proxy\Proxy
{
    private $_entityPersister;
    private $_identifier;
    public $__isInitialized__ = false;
    public function __construct($entityPersister, $identifier)
    {
        $this->_entityPersister = $entityPersister;
        $this->_identifier = $identifier;
    }
    private function _load()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;
            if ($this->_entityPersister->load($this->_identifier, $this) === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            unset($this->_entityPersister, $this->_identifier);
        }
    }

    
    public function getId()
    {
        $this->_load();
        return parent::getId();
    }

    public function getName()
    {
        $this->_load();
        return parent::getName();
    }

    public function getIssues()
    {
        $this->_load();
        return parent::getIssues();
    }

    public function getLanguages()
    {
        $this->_load();
        return parent::getLanguages();
    }

    public function getDefaultLanguage()
    {
        $this->_load();
        return parent::getDefaultLanguage();
    }

    public function getDefaultLanguageName()
    {
        $this->_load();
        return parent::getDefaultLanguageName();
    }

    public function getSections()
    {
        $this->_load();
        return parent::getSections();
    }

    public function setModeratorTo($p_moderator_to)
    {
        $this->_load();
        return parent::setModeratorTo($p_moderator_to);
    }

    public function getModeratorTo()
    {
        $this->_load();
        return parent::getModeratorTo();
    }

    public function setModeratorFrom($p_moderator_from)
    {
        $this->_load();
        return parent::setModeratorFrom($p_moderator_from);
    }

    public function getModeratorFrom()
    {
        $this->_load();
        return parent::getModeratorFrom();
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'id', 'name', 'default_language', 'issues', 'public_enabled', 'moderator_to', 'moderator_from');
    }

    public function __clone()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;
            $class = $this->_entityPersister->getClassMetadata();
            $original = $this->_entityPersister->load($this->_identifier);
            if ($original === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            foreach ($class->reflFields AS $field => $reflProperty) {
                $reflProperty->setValue($this, $reflProperty->getValue($original));
            }
            unset($this->_entityPersister, $this->_identifier);
        }
        
    }
}