<?php 
namespace Trazeo\FrontBundle\Translation;
 
use Symfony\Bundle\FrameworkBundle\Translation\Translator as BaseTranslator;
 
class PhraseTranslator extends BaseTranslator
{
    public function trans($id, array $parameters = array(), $domain = 'messages', $locale = null)
    {
    	// modificación de los prefijos
        $prefix = "[[__";
        $suffix = "__]]";
        
        // activar modo depuración
        $debugMode = true;
        
        // Token de la app. Recomendado incluirlo aquí
        $auth_token = '5de9fb0c22272598e06b18ff8d5528a60';
        
        // Desactivación de PhraseApp
        $enabled = false;
 
        if (!isset($locale)) {
            $locale = $this->getLocale();
        }
 
        if (!isset($this->catalogues[$locale])) {
            $this->loadCatalogue($locale);
        }
 
        if ($domain == 'routes') {
            // Return translated values for 'routes' domain
            return strtr($this->catalogues[$locale]->get((string) $id, $domain), $parameters);
        } else {
            // Return PhraseApp translation keys for all other domains
            return $prefix.$id.$suffix;
        }
    }
}