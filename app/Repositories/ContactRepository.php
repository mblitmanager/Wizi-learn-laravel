<?php

namespace App\Repositories;

use App\Models\Contact;
use App\Models\Formateur;
use App\Models\Commercial;
use App\Models\PoleRelation;
use App\Repositories\Interfaces\ContactRepositoryInterface;

class ContactRepository implements ContactRepositoryInterface
{
    public function getFormateurContacts($stagiaireId)
    {
        return Formateur::whereHas('formations.stagiaires', function($query) use ($stagiaireId) {
            $query->where('stagiaires.id', $stagiaireId);
        })->get();
    }

    public function getCommercialContacts($stagiaireId)
    {
        return Commercial::whereHas('stagiaires', function($query) use ($stagiaireId) {
            $query->where('stagiaires.id', $stagiaireId);
        })->get();
    }

    public function getPoleRelationContacts($stagiaireId)
    {
        return PoleRelation::all(); // Tous les contacts du pôle relation sont accessibles
    }

    public function getAllContacts($stagiaireId)
    {
        return [
            'formateurs' => $this->getFormateurContacts($stagiaireId),
            'commerciaux' => $this->getCommercialContacts($stagiaireId),
            'pole_relation' => $this->getPoleRelationContacts($stagiaireId)
        ];
    }
} 