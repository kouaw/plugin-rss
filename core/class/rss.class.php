<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
include_file('3rdparty','rss/rsslib','php','rss');
$RSS_Content = array();
class rss extends eqLogic {
    /*     * *************************Attributs****************************** */
	


    /*     * ***********************Methode static*************************** */

    /*
     * Fonction exécutée automatiquement toutes les minutes par Jeedom
      public static function cron() {

      }
     */


    /*
     * Fonction exécutée automatiquement toutes les heures par Jeedom
      public static function cronHourly() {

      }
     */

    /*
     * Fonction exécutée automatiquement tous les jours par Jeedom
      public static function cronDayly() {

      }
     */



    /*     * *********************Méthodes d'instance************************* */

    public function preInsert() {
        
    }

    public function postInsert() {
        
    }

    public function preSave() {
        
    }

    public function postSave() {
        
    }

    public function preUpdate() {
        
    }

    public function postUpdate() {
        
    }

    public function preRemove() {
        
    }

    public function postRemove() {
        
    }

	public function toHtml($_version = 'dashboard') 
	{
		$li = null;
		$parametre = utils::o2a(cmd::byEqLogicId($this->getId()));
		
		foreach ($parametre as $key => $value){
			$li .= '<a href="#" class="list-group-item disabled">'.$value['name'].'</a>';
			$nbr = $value['configuration']['nbr_article'];
			$lien_rss = $value['configuration']['lien_rss'];
			$li .= RSS_Links($lien_rss,$nbr,$this->getId());
		}
		log::add('rss','debug','params:'.$li);
		$_version = jeedom::versionAlias($_version);

		$replace = array(
			'#id#' => $this->getId(),
			'#name#' => ($this->getIsEnable()) ? $this->getName() : '<del>' . $this->getName() . '</del>',
			'#eqLink#' => $this->getLinkToConfiguration(),
			'#li#' => $li
			);
			
		return template_replace($replace, getTemplate('core', $_version, 'eqLogic','rss'));
    }

    /*     * **********************Getteur Setteur*************************** */
}

class rssCmd extends cmd {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    /*
     * Non obligatoire permet de demander de ne pas supprimer les commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
      public function dontRemoveCmd() {
      return true;
      }
     */

    public function execute($_options = array()) {
    }
    /*     * **********************Getteur Setteur*************************** */
}


?>
