<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParrainageRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'parrain_id',
        'filleul_id',
        'catalogue_formation_id',
        'status',
    ];

    public function parrain() { return $this->belongsTo(Stagiaire::class, 'parrain_id'); }
    public function filleul() { return $this->belongsTo(Stagiaire::class, 'filleul_id'); }
    public function catalogueFormation() { return $this->belongsTo(CatalogueFormation::class); }
} 