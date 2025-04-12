<?php

namespace App\Services;

use App\Repositories\Interfaces\ContactRepositoryInterface;
use App\Models\Contact;

class ContactService
{
    protected $contactRepository;

    public function __construct(ContactRepositoryInterface $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }

    public function getContactsByStagiaire($stagiaireId)
    {
        return $this->contactRepository->getAllContacts($stagiaireId);
    }

    public function getFormateurContacts($stagiaireId)
    {
        return $this->contactRepository->getFormateurContacts($stagiaireId);
    }

    public function getCommercialContacts($stagiaireId)
    {
        return $this->contactRepository->getCommercialContacts($stagiaireId);
    }

    public function getPoleRelationContacts($stagiaireId)
    {
        return $this->contactRepository->getPoleRelationContacts($stagiaireId);
    }

    public function addContact($stagiaireId, array $data)
    {
        $data['stagiaire_id'] = $stagiaireId;
        return $this->contactRepository->create($data);
    }
} 