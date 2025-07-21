<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InscriptionRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'stagiaire_id',
        'catalogue_formation_id',
        'status',
    ];

    public function stagiaire() { return $this->belongsTo(Stagiaire::class); }
    public function catalogueFormation() { return $this->belongsTo(CatalogueFormation::class); }
} 