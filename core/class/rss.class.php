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
    	
    	$Jeedom_Blog = $this->getCmd(null, 'Jeedom_Blog');
		if (!is_object($Jeedom_Blog)) {
			$Jeedom_Blog = new rssCmd();
			$Jeedom_Blog->setLogicalId('Jeedom_Blog');
			$Jeedom_Blog->setIsVisible(1);
			$Jeedom_Blog->setOrder(1);
			$Jeedom_Blog->setName(__('Jeedom Blog', __FILE__));
		}
        $Jeedom_Blog->setType('info');
		$Jeedom_Blog->setSubType('string');
		$Jeedom_Blog->setConfiguration("lien_rss","http://blog.jeedom.fr/?feed=rss2");
		$Jeedom_Blog->setConfiguration("nbr_article","2");
		$Jeedom_Blog->setEqLogic_id($this->getId());
		$Jeedom_Blog->save();
		
		$Jeedom_Market = $this->getCmd(null, 'Jeedom_Market_Plugin');
		if (!is_object($Jeedom_Market)) {
			$Jeedom_Market = new rssCmd();
			$Jeedom_Market->setLogicalId('Jeedom_Market_Plugin');
			$Jeedom_Market->setIsVisible(1);
			$Jeedom_Market->setOrder(2);
			$Jeedom_Market->setName(__('Jeedom Market (Plugin)', __FILE__));
		}
        $Jeedom_Market->setType('info');
		$Jeedom_Market->setSubType('string');
		$Jeedom_Market->setConfiguration("lien_rss","http://market.jeedom.fr/plugin.xml");
		$Jeedom_Market->setConfiguration("nbr_article","2");
		$Jeedom_Market->setEqLogic_id($this->getId());
		$Jeedom_Market->save();
		
		$Jeedom_Market_2 = $this->getCmd(null, 'Jeedom_Market_Widget');
		if (!is_object($Jeedom_Market_2)) {
			$Jeedom_Market_2 = new rssCmd();
			$Jeedom_Market_2->setLogicalId('Jeedom_Market_Widget');
			$Jeedom_Market_2->setIsVisible(0);
			$Jeedom_Market_2->setOrder(3);
			$Jeedom_Market_2->setName(__('Jeedom Market (Widget)', __FILE__));
		}
        $Jeedom_Market_2->setType('info');
		$Jeedom_Market_2->setSubType('string');
		$Jeedom_Market_2->setConfiguration("lien_rss","http://market.jeedom.fr/widget.xml");
		$Jeedom_Market_2->setConfiguration("nbr_article","2");
		$Jeedom_Market_2->setEqLogic_id($this->getId());
		$Jeedom_Market_2->save();
		
		$Jeedom_Market_3 = $this->getCmd(null, 'Jeedom_Market_Script');
		if (!is_object($Jeedom_Market_3)) {
			$Jeedom_Market_3 = new rssCmd();
			$Jeedom_Market_3->setLogicalId('Jeedom_Market_Script');
			$Jeedom_Market_3->setIsVisible(0);
			$Jeedom_Market_3->setOrder(4);
			$Jeedom_Market_3->setName(__('Jeedom Market (Script)', __FILE__));
		}
        $Jeedom_Market_3->setType('info');
		$Jeedom_Market_3->setSubType('string');
		$Jeedom_Market_3->setConfiguration("lien_rss","http://market.jeedom.fr/script.xml");
		$Jeedom_Market_3->setConfiguration("nbr_article","2");
		$Jeedom_Market_3->setEqLogic_id($this->getId());
		$Jeedom_Market_3->save();
        
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
			if($value['isVisible'] == 1){
			$li .= '<a href="#" class="list-group-item disabled">'.$value['name'].'</a>';
			$nbr = $value['configuration']['nbr_article'];
			$lien_rss = $value['configuration']['lien_rss'];
			$li .= RSS_Links($lien_rss,$nbr,$this->getId());
			}
		}
		log::add('rss','debug','params:'.$li);
		$_version = jeedom::versionAlias($_version);

		$replace = array(
			'#id#' => $this->getId(),
			'#name#' => ($this->getIsVisible()) ? $this->getName() : '<del>' . $this->getName() . '</del>',
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
