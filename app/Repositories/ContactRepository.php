<?php

namespace App\Repositories;

use App\Models\Formateur;
use App\Models\Commercial;
use App\Models\PoleRelationClient;
use App\Repositories\Interfaces\ContactRepositoryInterface;

class ContactRepository implements ContactRepositoryInterface
{
    public function getFormateurContacts($stagiaireId)
    {
        return Formateur::whereHas('formations.stagiaires', function($query) use ($stagiaireId) {
            $query->where('stagiaires.id', $stagiaireId);
        })
        ->with([
            'user',
            'formations' => function ($query) use ($stagiaireId) {
                $query->whereHas('stagiaires', function($q) use ($stagiaireId) {
                    $q->where('stagiaires.id', $stagiaireId);
                });
            }
        ])
        ->get();

    }

    public function getCommercialContacts($stagiaireId)
    {
        return Commercial::whereHas('stagiaires', function($query) use ($stagiaireId) {
            $query->where('stagiaires.id', $stagiaireId);
        })->with('user')->get();
    }

    public function getPoleRelationContacts($stagiaireId)
    {
        return PoleRelationClient::whereHas('stagiaires', function($query) use ($stagiaireId) {
            $query->where('stagiaires.id', $stagiaireId);
        })->with('user')->get();
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
