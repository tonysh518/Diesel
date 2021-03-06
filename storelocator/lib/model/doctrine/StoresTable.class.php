<?php

class StoresTable extends Doctrine_Table
{

  const STATUS_OPENED = 'Opened';
  

  /**
   * Ritorna lo store in base allo store_code
   *
   * @param $storeCode lo store code
   * @return array string
   */
  public static function retrieveById($id) {
    return Doctrine_Query::create()
    ->from('Stores s')
    ->where('s.id=?')
    ->andWhere('s.status = ?', self::STATUS_OPENED)
    ->fetchOne($id);
  }
  
  public static function retrieveByUserId($user_id) {
    return Doctrine_Query::create()
    ->from('Stores s')
    ->where('s.user_id=?')
    ->andWhere('s.status = ?', self::STATUS_OPENED)
    ->fetchOne($user_id);
  }
  
  /**
   * Ritorna la lista degli stores
   *
   * @return array string
   */
  public static function retrieveOpened() {
    return Doctrine_Query::create()
    ->from('Stores s')
    ->andWhere('s.status = ?', self::STATUS_OPENED)
    ->execute();
  }
  
  
  
  /**
   * Ritorna lo store piu vicino in base alle coordinate fornite.
   * Thanks to Roberto Butti
   * 
   * Tram: esteso il metodo per trovare gli store nell'arco di n Km
   *
   * @param $latitude latitudine
   * @param $longitude longitudine
   * @return Store
   */
  public static function findClosestStore($latitude, $longitude, $maxDistance=10000000, $multiple = false, $type=NULL) {
    $k = 6372.795477598; //raggio quadratico medio
   // $maxDistance =10000000;

    // formule per il calcolo in radianti
    $lat_alfa = "PI()*  $latitude /180";
    $lat_beta = "PI()*  latitude /180";
    $lng_alfa = "PI()* $longitude /180";
    $lng_beta = "PI()* longitude /180";

    // calcolo angolo compreso
    $fi = " $lng_beta - $lng_alfa ";

    $sin ="SIN($lat_alfa)*SIN($lat_beta)";
    $cos = "COS($lat_alfa)*COS($lat_beta)";
    $dist = "($k * ACOS($sin + ($cos * COS($fi))))";

    $typestr = "";
    if ($type != NULL) {
      if (is_array($type)) {
        $typestr = "AND type IN ('" . implode("','", $type) ."') ";
      }//if
      else {
        $typestr = "AND type = '$type'";
      }
    }
    
    //eseguo una query diretta senza passare per doctrine dato che è particolare
    $con = Doctrine_Manager::getInstance()->connection();
    $sqlString = "SELECT *, ($dist) AS distance FROM stores WHERE $dist <= $maxDistance AND status = '" . self::STATUS_OPENED . "' ". $typestr ." ORDER BY $dist";
    
    $connection = Doctrine_Manager::getInstance()->connection();
    $stmt = $connection->execute($sqlString);
    $result = $stmt->fetchAll();
    
    $store = $result;

    if (!$multiple) {
      //costruisco un oggetto Store da restituire con i dati ritornati nell'oggetto PDO
      if( $result && sizeof( $result ) > 0 ) {
        $store = new Stores();
        $store->hydrate($result[0]);
      }
      else {
        $store = Doctrine_Query::create()
        ->from('Stores s')
        ->where('s.status = ?', self::STATUS_OPENED)
        ->limit(1)
        ->fetchOne();
      }
    }//if

    return $store;
  }//findClosestStore

  
  /**
   * Ritorna le città associate ad un country.
   *
   * @param $country country di cui tornare tutte le city
   * @return array string
   */
  public static function getCountries($addempty) {
    $q = Doctrine_Query::create()
    ->select("DISTINCT (s.country) as country")
    ->from('Stores s')
    ->where('s.status = ?', self::STATUS_OPENED)
    ->orderBy('country ASC');
    
    $stores = $q->execute(array(), Doctrine::FETCH_ASSOC);
        
    $countries = array();
    if( $addempty === true ){
    	$countries[""] = "All Country";
    }
    foreach($stores as $store){
      $country = $store->getCountry();
      //alcune citta non sono valorizzate, prevengo valori null
      if( $country ){
        $countries[$country] = $country;
      }
    }
    
    return $countries;
  }

  
  /**
   * Ritorna le citt� associate ad un country.
   *
   * @param $country country di cui tornare tutte le city
   * @return array string
   */
  public static function getCities($country) {
    $q = Doctrine_Query::create()
    ->select("DISTINCT (city) as city")
    ->from('Stores s')
    ->where('s.country=?')
    ->andWhere('s.status = ?', self::STATUS_OPENED)
    ->orderBy('city ASC');
    
    $stores = $q->execute($country, Doctrine::FETCH_ASSOC);
    
    $cities = array();
    foreach($stores as $store){
      $city = $store->getCity();
      //alcune citta non sono valorizzate, prevengo valori null
      if( $city ){
        $cities[$city] = $city;
      }
    }
    
    return $cities;
  }

  
  /**
   * Ritorna gli stores in base a countryId e city.
   * I due parametri sono facoltativi, se ci sono i risultati vengono filtrati
   * per parametri, altrimenti vengono ritornati tutti gli stores
   *
   * @param $countryId country 
   * @param $city city
   * @return array string
   */
  public static function getStores($country=null, $city=null, $type = array()) {
    $typesMap = Doctrine::getTable('Stores')->getTypesMap();
    $excludedTypes = array_keys($typesMap, 'XXX');
    
    $q = Doctrine_Query::create()->from('Stores s');
    $q->andWhere('s.status = ?', self::STATUS_OPENED);
    if( $country ){
      $q->andWhere('s.country=?', $country);
    }
    if( $city ){
      $q->andWhere('LOWER(s.city)=?', strtolower($city));
    }
    if( !empty($type)){
      $filteredTypes = array_keys($typesMap, $type);
      $q->andWhereIn('s.type', $filteredTypes);
    }
    else 
    {
      $q->andWhereNotIn('s.type', $excludedTypes);
    }
    $q->orderBy('s.city ASC, order ASC, name ASC');

    
    $stores = $q->execute(array(), Doctrine::FETCH_ASSOC);
    $storesAsArray = array();
    
    foreach($stores as $store){
      $storesAsArray[] = array(
        'id'        => $store->getId(),
        'slug'      => $store->getSlug(),
        'brand'     => strtoupper(self::cleanNullValues($store->getBrand())),
        'name'      => self::cleanNullValues($store->getName()),
        'address'   => self::cleanNullValues($store->getAddress()),
        'city'      => self::cleanNullValues($store->getCity()),
        'country'   => self::cleanNullValues($store->getCountry()),
        'zip'       => self::cleanNullValues($store->getZip()),
        'telf'      => self::cleanNullValues($store->getTelf()),
        'latitude'  => self::cleanNullValues($store->getLatitude()),
        'longitude' => self::cleanNullValues($store->getLongitude()),
        'public_type' =>  self::cleanNullValues($store->getPublicType()),
        'info' =>  self::cleanNullValues(nl2br($store->getStoreExtraData()->getInfo())),
        'hours' => $store->getStoreExtraData()->getInfo()?$store->getStoreExtraData()->getInfo():"",
        'additional' => $store->getStoreExtraData()->getTimesNotes()?$store->getStoreExtraData()->getTimesNotes():"",
      );
    }
    
    return $storesAsArray;
  }
  
  /**
   * Ritorna un array di mappa per la colonna type.
   * 
   * La chiave dell'array identifica il valore presente su DB, il valore
   * invece il filtro disponibile all'utente. 
   */
  public function getTypesMap()
  {
    return array(
      'Planet'              =>  'Flagship',
      'Flagship'            =>  'Flagship',
      'Diesel'          =>  'Diesel',
      'Accessories'     =>  'Diesel',
      'Diesel Premium'  =>  'Diesel',
      'Denim Gallery'   =>  'Diesel',
      'Denimoteque'     =>  'Diesel',
      'Concept Store'   =>  'Diesel',
      'Diesel Travel'   =>  'Diesel',
      'Temporary'       =>  'Diesel',
      'Kid'                 =>  'Kid',
      'Kid Travel'          =>  'Kid',
      'Diesel Outlet'   =>  'Factory Outlet',
      'Kid Outlet'      =>  'Factory Outlet',
      'Accessories Outlet'  =>  'Factory Outlet',
      '55DSL'               =>  'XXX',
      'Diesel Black Gold'   =>  'XXX',
    );
  }
  
  /**
   * Genera l'array utilizzato per visualizzare i filtri sugli Store.
   *
   * @internal l' array viene utilizzato dal filtro StoresFinderForm per
   * generare widget e validatori per il form di StoresFinder.
   */
  public function getStoreFilters()
  {
    $map = $this->getTypesMap();
    unset($map['55DSL']);
    unset($map['Diesel Black Gold']);
    $filter = array_values($map);
    $filter = array_unique($filter);
    $filter = array_combine($filter, $filter);
    
    return $filter;
  }
  
  /**
   * In caso di valore null viene ritornata stringa vuota.
   * 
   * @param unknown_type $val
   * @return Ambigous <string, unknown>
   */
  protected static function cleanNullValues($val){
    return !empty($val) ? $val : '';
  }
  
  public static  function getStoresByCountry($country) {
    $q = Doctrine_Query::create()->from('Stores s')
      ->andWhere('s.status = ?', self::STATUS_OPENED)
      ->andWhere('s.country=?', $country)
      ->andWhere('s.user_id is null');
    return $q->execute();
  }//getStoresByCountry
  
}