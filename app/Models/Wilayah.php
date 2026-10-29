<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Official Kemendagri/BPS kode wilayah entry (levels 1-2 only):
 * provinsi and kabupaten/kota. Kecamatan level is not stored here —
 * the in-map area dropdown uses OSM place nodes (CafeArea).
 */
class Wilayah extends Model
{
    // 'wilayah' is already plural in Indonesian; Eloquent would pluralize
    // the class name again into 'wilayahs'.
    protected $table = 'wilayah';

    public $incrementing = false;

    protected $primaryKey = 'kode';

    protected $keyType = 'string';

    protected $fillable = [
        'kode',
        'nama',
        'level',
        'parent_kode',
        'osm_area_id',
    ];

    public const LEVEL_PROVINSI = 'provinsi';

    public const LEVEL_KABUPATEN_KOTA = 'kabupaten_kota';

    public function scopeProvinsi($query)
    {
        return $query->where('level', self::LEVEL_PROVINSI);
    }

    public function scopeKabupatenKota($query)
    {
        return $query->where('level', self::LEVEL_KABUPATEN_KOTA);
    }
}
