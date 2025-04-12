<?php

namespace App\Repositories\Interfaces;

use App\Models\Contact;

interface ContactRepositoryInterface
{
    public function getFormateurContacts($stagiaireId);
    public function getCommercialContacts($stagiaireId);
    public function getPoleRelationContacts($stagiaireId);
    public function getAllContacts($stagiaireId);
} 