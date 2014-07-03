<?php
namespace Sopinet\Bundle\UserNotificationsBundle\Util;

class TimeAgoHelper {
 
    protected $strings_es = array(
        'y' => array('1 año', '%d años'),
        'm' => array('1 mes', '%d meses', 'y 1 mes', 'y %d meses'),
        'd' => array('1 dia', '%d días', 'y 1 día', 'y %d días'),
        'h' => array('1 hora', '%d horas', 'y 1 hora', 'y %d horas'),
        'i' => array('1 minuto', '%d minutos', 'y 1 minuto', 'y %d minutos'),
        's' => array('ahora', '%d segundos', 'y 1 segundo', 'y %d segundos'),
    );
    
    protected $strings_en = array(
    	'y' => array('1 year', '%d years'),
    	'm' => array('1 month', '%d months', 'and 1 month', 'and %d months'),
        'd' => array('1 day', '%d days', 'and 1 day', 'and %d days'),
        'h' => array('1 hour', '%d hours', 'and 1 hour', 'and %d hours'),
        'i' => array('1 minute', '%d minutes', 'and 1 minute', 'and %d minutes'),
        's' => array('now', '%d seconds', 'and 1 second', 'and %d seconds'),
    );
    
    protected function getStrings() {
    	if ($this->lang == "es") return $this->strings_es;
    	else if ($this->lang == "en") return $this->strings_en;
    }
 
    protected $datetime;
    protected $from;
    protected $container;
    protected $lang;
 
    public function __construct($datetime, $from = null, $container = null)
    {	
    	$this->container = $container;
    	$this->lang = $container->get('request')->getLocale();
        $this->datetime = $datetime;
        if ($this->datetime == null) return ""; // Devolvemos "" si es nulo
        if ($from == null) $this->from = new \DateTime('now');
        else $this->from = $from;               
    }
    
    public static function getMonths($datetime){
    	$difference = $datetime->diff(new \DateTime('now'));
    	return ($difference->y *12) + $difference->m;
    }
 
    /**
     * Returns the difference from the current time in the format X time ago
     * @return string
     */
    public function __toString() {
    	$strings = $this->getStrings();
        $diff = $this->datetime->diff($this->from);
 
        $first = false;  
        $text = "";      
        foreach($strings as $key => $value){
            if($first) {
                return $text." ".$this->getDiffText($key, $diff, true);                
            }
            if( ($text = $this->getDiffText($key, $diff)) ){                
                $first = true;
            }            
 
        }
        if ($text == "") return $strings['s'][0];
        return $text;
    }
 
    /**
     * Try to construct the time diff text with the specified interval key
     * @param string $intervalKey A value of: [y,m,d,h,i,s]
     * @param DateInterval $diff
     * @param $append
     * @return string|null
     */
    protected function getDiffText($intervalKey, $diff, $append = false){
    	$strings = $this->getStrings();
    	
        $pluralKey = (!$append)?1:3;
        $value = $diff->$intervalKey;
        if($value > 0){
            if($value < 2){
                $pluralKey = (!$append)?0:2;
            }
            return sprintf($strings[$intervalKey][$pluralKey], $value);
        }
        return "";
    }
}
?>