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
    
	public function RSSComplet(){
		log::add('rss','debug','/**************************************************/');
		log::add('rss','debug','/*                                                */');
		log::add('rss','debug','/*    Lancement de la recherche de flux RSS       */');
		log::add('rss','debug','/*                                                */');
		log::add('rss','debug','/**************************************************/');
		//$lien_dossier = dirname(__FILE__) . '/../../flux_rss';
		$lien_dossier = realpath(dirname(__FILE__) . '/../../flux_rss');
		log::add('rss','debug','Lien du dossier RSS :'.$lien_dossier);
		log::add('rss','debug','////////////////////////////////////////////////////');
		foreach (rss::byType('rss') as $rss_plugin) {
			log::add('rss','debug','params :'. $rss_plugin->getId() );
			$parametre = $rss_plugin->getId();
			log::add('rss','debug','----------------------------------------------------');
			foreach (cmd::byEqLogicId($parametre) as $cmd_rss_plugin){
				$lien_recuperation = $lien_dossier.'/'.$cmd_rss_plugin->getLogicalId().'.json';
				log::add('rss','debug','On verifi que le fichier existe :'.$lien_recuperation);
				
				if(file_exists($lien_recuperation)){
					log::add('rss','debug','Il existe je récupére le dernier titre');
					$file_rss = fopen($lien_recuperation, "r");
					$file_rss_read = fread($file_rss, filesize($lien_recuperation));
					fclose($file_rss);
					$file_rss = null;
					$recuperateur = json_decode($file_rss_read, true);
					log::add('rss','debug','fichier > '.$file_rss_read);
					$derniere_description = $recuperateur['contenu'][1]['title'];
					log::add('rss','debug','Dernier titre du fichier > '.$derniere_description);
					$file_existe = 1;
				}else{
					log::add('rss','debug','pas de fichier');
					$derniere_description = null;
					$file_existe = 0;
				}
				
				if($cmd_rss_plugin->getIsVisible() == 1){
					$configuration_rss = $cmd_rss_plugin->getConfiguration();
					$lien_rss = $configuration_rss['lien_rss'];
					$nbr = $configuration_rss['nbr_article'];
					$name_rss = $cmd_rss_plugin->getName();
					log::add('rss','debug','Lien :'.$lien_rss.' ,Nombre :'.$nbr.' ,Nom :'.$name_rss);
				
					$array_rss_avant = RSS_Links($lien_rss,$nbr);
					log::add('rss','debug','Retour RSS :'. json_encode($array_rss_avant));
					log::add('rss','debug','Retour premier titre RSS :'. $array_rss_avant[0][1]['title']);
					
					if($array_rss_avant[0][1]['title'] != $derniere_description){
						log::add('rss','debug','Nous avons une nouveauté :'. $array_rss_avant[0][1]['title']);
						$array_rss = array('name_rss' => $name_rss, 'contenu' => $array_rss_avant);
						$json_array = json_encode($array_rss);
						//file_put_contents($lien_recuperation,'test');
						if($file_existe == 1){
							unlink($lien_recuperation);
						}
						$file_rss = fopen($lien_recuperation, w);
						fwrite($file_rss, $json_array);
						fclose($file_rss);	
						log::add('rss','debug','Nouveau Fichier enregistré');
					}
				}else{
					if($file_existe == 1){
						unlink($lien_recuperation);
					}
					log::add('rss','debug','N est pas selectionne');
				}
				log::add('rss','debug','----------------------------------------------------');
			}
			log::add('rss','debug','////////////////////////////////////////////////////');
		}
	}


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
        
        rss::RSSComplet();
    }

    public function preUpdate() {
        
    }

    public function postUpdate() {
    }

    public function preRemove() {
        
    }

    public function postRemove() {
    }

	public static function cron15() {
		log::add('rss','debug','/**************************************************/');
		log::add('rss','debug','/*                                                */');
		log::add('rss','debug','/*    Lancement de la recherche de flux RSS       */');
		log::add('rss','debug','/*                                                */');
		log::add('rss','debug','/**************************************************/');
		//$lien_dossier = dirname(__FILE__) . '/../../flux_rss';
		$lien_dossier = realpath(dirname(__FILE__) . '/../../flux_rss');
		log::add('rss','debug','Lien du dossier RSS :'.$lien_dossier);
		log::add('rss','debug','////////////////////////////////////////////////////');
		foreach (rss::byType('rss') as $rss_plugin) {
			log::add('rss','debug','params :'. $rss_plugin->getId() );
			$parametre = $rss_plugin->getId();
			log::add('rss','debug','----------------------------------------------------');
			foreach (cmd::byEqLogicId($parametre) as $cmd_rss_plugin){
				$lien_recuperation = $lien_dossier.'/'.$cmd_rss_plugin->getLogicalId().'.json';
				log::add('rss','debug','On verifi que le fichier existe :'.$lien_recuperation);
				
				if(file_exists($lien_recuperation)){
					log::add('rss','debug','Il existe je récupére le dernier titre');
					$file_rss = fopen($lien_recuperation, "r");
					$file_rss_read = fread($file_rss, filesize($lien_recuperation));
					fclose($file_rss);
					$file_rss = null;
					$recuperateur = json_decode($file_rss_read, true);
					log::add('rss','debug','fichier > '.$file_rss_read);
					$derniere_description = $recuperateur['contenu'][1]['title'];
					log::add('rss','debug','Dernier titre du fichier > '.$derniere_description);
					$file_existe = 1;
				}else{
					log::add('rss','debug','pas de fichier');
					$derniere_description = null;
					$file_existe = 0;
				}
				
				if($cmd_rss_plugin->getIsVisible() == 1){
					$configuration_rss = $cmd_rss_plugin->getConfiguration();
					$lien_rss = $configuration_rss['lien_rss'];
					$nbr = $configuration_rss['nbr_article'];
					$name_rss = $cmd_rss_plugin->getName();
					log::add('rss','debug','Lien :'.$lien_rss.' ,Nombre :'.$nbr.' ,Nom :'.$name_rss);
				
					$array_rss_avant = RSS_Links($lien_rss,$nbr);
					log::add('rss','debug','Retour RSS :'. json_encode($array_rss_avant));
					log::add('rss','debug','Retour premier titre RSS :'. $array_rss_avant[0][1]['title']);
					
					if($array_rss_avant[0][1]['title'] != $derniere_description){
						log::add('rss','debug','Nous avons une nouveauté :'. $array_rss_avant[0][1]['title']);
						$array_rss = array('name_rss' => $name_rss, 'contenu' => $array_rss_avant);
						$json_array = json_encode($array_rss);
						//file_put_contents($lien_recuperation,'test');
						if($file_existe == 1){
							unlink($lien_recuperation);
						}
						$file_rss = fopen($lien_recuperation, w);
						fwrite($file_rss, $json_array);
						fclose($file_rss);	
						log::add('rss','debug','Nouveau Fichier enregistré');
					}
				}else{
					if($file_existe == 1){
						unlink($lien_recuperation);
					}
					log::add('rss','debug','N est pas selectionne');
				}
				log::add('rss','debug','----------------------------------------------------');
			}
			log::add('rss','debug','////////////////////////////////////////////////////');
		}


    }

	public function toHtml($_version = 'dashboard') 
	{
		$lien_dossier = realpath(dirname(__FILE__) . '/../../flux_rss');
		$li = null;
		$array_dossier = scandir($lien_dossier);
		foreach ($array_dossier as $key => $value){
			if($value == '.' || $value == '..'){
				
			}else{
				$lien_file_dash_rss = $lien_dossier.'/'.$value;
				$file_dash_rss = fopen($lien_file_dash_rss, 'r');
				$read_file_dash_rss = fread($file_dash_rss, filesize($lien_file_dash_rss));
				fclose($file_dash_rss);
				$file_dash_rss = null;
				$recuperateur = json_decode($read_file_dash_rss, true);
				$read_file_dash_rss = null;
				
				$li .= '<a href="#" class="list-group-item disabled">'.$recuperateur['name_rss'].'</a>';
				foreach($recuperateur['contenu'] as $recup){
					if($recup['title'] !== null){
						$li .= '<a onclick="open_rss'.$this->getId().'(\''.$recup['link'].'\')" class="list-group-item" style="background-color:transparent;cursor:pointer;">'.htmlentities($recup['title']).'</a>';
					}else{
						foreach($recup as $recupencore){
							if($recupencore['title'] !== null){
								$li .= '<a onclick="open_rss'.$this->getId().'(\''.$recupencore['link'].'\')" class="list-group-item" style="background-color:transparent;cursor:pointer;">'.$recupencore['title'].'</a>';
							}
						}
					}
				}
			}
		}
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
