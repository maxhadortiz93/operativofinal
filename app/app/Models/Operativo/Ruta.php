<?php

namespace App\Models\operativo;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Ruta extends Model
{
    protected $table = "rutas";
    protected $fillable = [

    	  'id',
        'distancia',
        'duracion',
        'tarifa_vuelo',
        'origen_id',
        'destino_id',
        'estado',
      

    ];

    public function segmentos()
    {
    	return $this->belongsTo('App\Models\Operativo\Segmento');
    }

    public function sucursal()
    {
    	return $this->belongsTo('App\Models\Operativo\Sucursal');
    }

    public function origen()
    {
      return $this->belongsTo('App\Models\Operativo\Sucursal','origen_id','id');
    }
    public function destino()
    {
      return $this->belongsTo('App\Models\Operativo\Sucursal','destino_id','id');
    }

    public function scopeRutas($query, $origen_id, $destino_id,$date)
    {
      return DB::table('vuelos')
                ->select('vuelos.id')
                ->join('segmentos','vuelos.id','=','segmentos.vuelo_id')
                ->join('rutas','segmentos.ruta_id','=','rutas.id')
                ->join('sucursales','rutas.origen_id','=','sucursales.id')
                ->where([['rutas.origen_id','=',$origen_id],['rutas.destino_id','=',$destino_id]])
                ->whereDate('vuelos.fecha_salida', '=', $date->format('Y-m-d'))->get();
       // return DB::table('rutas')->join('sucursales','sucursales.id','=','rutas.origen_id')
       //                         ->join('segmentos','segmentos.ruta_id','=','rutas.id')  
       //                         ->join('vuelos','vuelos.id','=','segmentos.vuelo_id')
       //                         ->where([['rutas.origen_id','=',$origen_id],['rutas.destino_id','=',$destino_id]])
       //                         ->whereDate('vuelos.fecha_salida', '=', $date->format('Y-m-d'));
       //                         ->get();
    }

    public function scopeDetalleOrigen($query, $origen_id, $destino_id,$fecha_salida)
    {
        
         return DB::table('rutas')->join('sucursales','sucursales.id', '=', 'rutas.origen_id')
                                               ->join('segmentos','segmentos.ruta_id','=','rutas.id')
                                               ->join('vuelos','vuelos.id','=','segmentos.vuelo_id')
                                               ->where([['rutas.origen_id','=',$origen_id],['rutas.destino_id','=',$destino_id],['vuelos.fecha_salida','=',$fecha_salida]])
                                               ->select('sucursales.ciudad','sucursales.pais','sucursales.sigla','sucursales.aeropuerto','rutas.tarifa_vuelo','rutas.distancia','rutas.duracion','vuelos.fecha_salida')->get();


    }

    public function scopeDetalleDestino($query, $origen_id, $destino_id,$fecha_salida)
    {
        
         return DB::table('rutas')->join('sucursales','sucursales.id', '=', 'rutas.destino_id')
                                               ->join('segmentos','segmentos.ruta_id','=','rutas.id')
                                               ->join('vuelos','vuelos.id','=','segmentos.vuelo_id')
                                               ->where([['rutas.origen_id','=',$origen_id],['rutas.destino_id','=',$destino_id],['vuelos.fecha_salida','=',$fecha_salida]])
                                               ->select('sucursales.ciudad','sucursales.pais','sucursales.sigla','sucursales.aeropuerto','rutas.tarifa_vuelo','rutas.distancia','rutas.duracion','vuelos.fecha_salida')->get();


    }

    public function scopeBuscador($query, $origen,$destino){
      return $query->where('destino_id',$destino)
                  ->where('origen_id',$origen)
                  ->where('estado',"activo");
  }
  public function scopeRutaInactiva($query, $origen,$destino){
      return $query->where('destino_id',$destino)
                  ->where('origen_id',$origen)
                  ->where('estado',"inactiva");
  }

}
