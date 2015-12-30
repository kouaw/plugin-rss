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

	public function RSSComplet(){
		
		log::add('rss','debug','/**************************************************/');
		log::add('rss','debug','/*                                                */');
		log::add('rss','debug','/*    Lancement de la recherche de flux RSS       */');
		log::add('rss','debug','/*                                                */');
		log::add('rss','debug','/**************************************************/');
		
		$lien_dossier = realpath(dirname(__FILE__) . '/../../flux_rss');
		log::add('rss','debug','Lien du dossier RSS :'.$lien_dossier);
		
		$handle=opendir($lien_dossier.'/'); 
			while (false !== ($fichier = readdir($handle))) { 
				if (($fichier != ".") && ($fichier != "..")) { 
					unlink($lien_dossier.'/'.$fichier); 
				} 
			} 
			
		log::add('rss','debug','////////////////////////////////////////////////////');
		foreach (rss::byType('rss') as $rss_plugin) {
			log::add('rss','debug','params :'. $rss_plugin->getId() );
			$parametre = $rss_plugin->getId();
			$nom_json_parametre = array();
			log::add('rss','debug','----------------------------------------------------');
			foreach (cmd::byEqLogicId($parametre) as $cmd_rss_plugin){
				$lien_recuperation = $lien_dossier.'/'.rss::myUrlEncode($cmd_rss_plugin->getName()).'.json';
				log::add('rss','debug','fichier tester :'.$lien_recuperation);
					$derniere_description = null;
					
				if($cmd_rss_plugin->getIsVisible() == 1){
					$array_push = rss::myUrlEncode($cmd_rss_plugin->getName());
					array_push($nom_json_parametre, $array_push);
					$configuration_rss = $cmd_rss_plugin->getConfiguration();
					$lien_rss = $configuration_rss['lien_rss'];
					$nbr = $configuration_rss['nbr_article'];
					$name_rss = $cmd_rss_plugin->getName();
					log::add('rss','debug','Lien :'.$lien_rss.' ,Nombre :'.$nbr.' ,Nom :'.$name_rss);
				
					$array_rss_avant = RSS_Links($lien_rss,$nbr);
					log::add('rss','debug','Retour RSS :'. json_encode($array_rss_avant));
					log::add('rss','debug','Retour premier titre RSS :'. $array_rss_avant[0][1]['title']);
					
						log::add('rss','debug','Nous avons une nouveauté :'. $array_rss_avant[0][1]['title']);
						$array_rss = array('name_rss' => $name_rss,'lien_rss' => $lien_rss, 'contenu' => $array_rss_avant);
						$json_array = json_encode($array_rss);
						$file_rss = fopen($lien_recuperation, 'w');
						fwrite($file_rss, $json_array);
						fclose($file_rss);	
						log::add('rss','debug','Nouveau Fichier enregistré');
				}else{
					log::add('rss','debug','N est pas selectionne');
				}
				log::add('rss','debug','----------------------------------------------------');
				$nom_json_parametre_json = json_encode($nom_json_parametre);
				$file_rss_get = fopen($lien_dossier.'/fluxrss_'.$rss_plugin->getId().'.json', 'w');
						fwrite($file_rss_get, $nom_json_parametre_json);
						fclose($file_rss_get);

			}
			log::add('rss','debug','////////////////////////////////////////////////////');
		}
		
	}
	
	public function RSSJeedom($fluxjeedom){
		if($fluxjeedom == 0){
		
			$Jeedom_Blog = $this->getCmd(null, 'Jeedom_Blog');
			if (is_object($Jeedom_Blog)) {
			$Jeedom_Blog->remove();
			}
			$Jeedom_Market = $this->getCmd(null, 'Jeedom_Market_Plugin');
			if (is_object($Jeedom_Market)) {
			$Jeedom_Market->remove();
			}
			$Jeedom_Market_2 = $this->getCmd(null, 'Jeedom_Market_Widget');
			if (is_object($Jeedom_Market_2)) {
			$Jeedom_Market_2->remove();
			}
			$Jeedom_Market_3 = $this->getCmd(null, 'Jeedom_Market_Script');
			if (is_object($Jeedom_Market_3)) {
			$Jeedom_Market_3->remove();
			}
			
		}elseif($fluxjeedom == 1){
		
		$Jeedom_Blog = $this->getCmd(null, 'Jeedom_Blog');
		if (!is_object($Jeedom_Blog)) {
			$Jeedom_Blog = new rssCmd();
			$Jeedom_Blog->setLogicalId('Jeedom_Blog');
			$Jeedom_Blog->setIsVisible(0);
			$Jeedom_Blog->setOrder(1);
			$Jeedom_Blog->setName(__('Jeedom Blog', __FILE__));
		}
        $Jeedom_Blog->setType('info');
		$Jeedom_Blog->setSubType('string');
		$Jeedom_Blog->setConfiguration("lien_rss","https://blog.jeedom.com/?feed=rss2");
		$Jeedom_Blog->setConfiguration("nbr_article","2");
		$Jeedom_Blog->setEqLogic_id($this->getId());
		$Jeedom_Blog->save();
		
		$Jeedom_Market = $this->getCmd(null, 'Jeedom_Market_Plugin');
		if (!is_object($Jeedom_Market)) {
			$Jeedom_Market = new rssCmd();
			$Jeedom_Market->setLogicalId('Jeedom_Market_Plugin');
			$Jeedom_Market->setIsVisible(0);
			$Jeedom_Market->setOrder(2);
			$Jeedom_Market->setName(__('Jeedom Market (Plugin)', __FILE__));
		}
        $Jeedom_Market->setType('info');
		$Jeedom_Market->setSubType('string');
		$Jeedom_Market->setConfiguration("lien_rss","https://market.jeedom.com/plugin.xml");
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
		$Jeedom_Market_2->setConfiguration("lien_rss","https://market.jeedom.com/widget.xml");
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
		$Jeedom_Market_3->setConfiguration("lien_rss","https://market.jeedom.com/script.xml");
		$Jeedom_Market_3->setConfiguration("nbr_article","2");
		$Jeedom_Market_3->setEqLogic_id($this->getId());
		$Jeedom_Market_3->save();
		
		}
	}

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

	public function postAjax(){
		log::add('rss','debug','Je PostAjax');
		$configuration_flux = $this->getconfiguration();
		$fluxJeedom = $configuration_flux['fluxjeedom'];
		log::add('rss','debug','le flux Jeedom est :'.$fluxJeedom);
		rss::RSSJeedom($fluxJeedom);
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
    
    public function myUrlEncode($string) {
    $entities = array('_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_');
    $replacements = array('!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]","+");
    return str_replace($entities, $replacements, urlencode($string));
}

	public static function cron15() {
			rss::RSSComplet();
    }

	public function toHtml($_version = 'dashboard') 
	{
		$lien_dossier = realpath(dirname(__FILE__) . '/../../flux_rss');
		$li = null;
		//$array_dossier = scandir($lien_dossier);
		
		$lienfor = $lien_dossier.'/fluxrss_'.$this->getId().'.json';
		
		if(file_exists($lienfor)){
			
			$fluxRSS = fopen($lienfor, 'r');
			$fluxRSSread = fread($fluxRSS, filesize($lienfor));
			fclose($fluxRSS);
			$fluxRSS = json_decode($fluxRSSread);
			foreach ($fluxRSS as $key => $value){
				$lien_file_dash_rss = $lien_dossier.'/'.$value.'.json';
				$file_dash_rss = fopen($lien_file_dash_rss, 'r');
				$read_file_dash_rss = fread($file_dash_rss, filesize($lien_file_dash_rss));
				fclose($file_dash_rss);
				$file_dash_rss = null;
				$recuperateur = json_decode($read_file_dash_rss, true);
				$read_file_dash_rss = null;
				
				$configuration = $this->getconfiguration();
				$theme_voulu = $configuration['theme'];
				if($theme_voulu == 1){$theme = 'barre';}else{$theme = 'standard';}
				
				if($theme == 'standard'){
				
				$width = '300px';
				$mini_height = "160px";
				$mini_widht = "280px";
				
				$li .= '<a href="#" class="list-group-item disabled"><img src="http://www.google.com/s2/favicons?domain='.$recuperateur['lien_rss'].'" /> '.$recuperateur['name_rss'].'</a>';
				foreach($recuperateur['contenu'] as $recup){
					if(isset($recup['title'])){
						$li .= '<a onclick="open_rss'.$this->getId().'(\''.$recup['link'].'\')" class="list-group-item" style="background-color:transparent;cursor:pointer;font-size : 0.9em;">'.htmlentities($recup['title']).'</a>';
					}else{
						foreach($recup as $recupencore){
							if(isset($recupencore['title'])){
								$li .= '<a onclick="open_rss'.$this->getId().'(\''.$recupencore['link'].'\')" class="list-group-item" style="background-color:transparent;cursor:pointer;font-size : 0.9em;">'.$recupencore['title'].'</a>';
							}
						}
					}
				}
				}elseif($theme == 'barre'){
				
				$width = '586px';
				$mini_height = "80px";
				$mini_widht = "500px";
				
					$li .= '<div class="btn-group" style="width:100%;">';
					$li .= '<button type="button" style="float:left;width:20%;" class="btn btn-danger" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="http://www.google.com/s2/favicons?domain='.$recuperateur['lien_rss'].'" /> '.$recuperateur['name_rss'].'</button>';
					//$li .= '<div style="float:left;padding: 6px 12px;" > TEST </div>';
					$li .= '<button type="button" style="float:left;width:75%;" class="btn btn btn-info" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><marquee>';
					
					foreach($recuperateur['contenu'] as $recup){
					if(isset($recup['title'])){
						$li .= htmlentities($recup['title']).' // ';
					}else{
						foreach($recup as $recupencore){
							if(isset($recupencore['title'])){
								$li .= $recupencore['title'].' // ';
							}
						}
					}
				}
					$li .= '</marquee></button>';
					$li .= '<button type="button" style="float:right;width:5%;" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
					$li .= '<span class="caret"></span>';
					$li .= '<span class="sr-only">Toggle Dropdown</span>';
					$li .= '</button>';
					$li .= '<ul class="dropdown-menu" style="width:100%;">';
					
					foreach($recuperateur['contenu'] as $recup){
					if(isset($recup['title'])){
						$li .= '<li><a onclick="open_rss'.$this->getId().'(\''.$recup['link'].'\')" class="list-group-item" style="background-color:transparent;cursor:pointer;font-size : 0.9em;">'.htmlentities($recup['title']).'</a></li>';
					}else{
						foreach($recup as $recupencore){
							if(isset($recupencore['title'])){
								$li .= '<li><a onclick="open_rss'.$this->getId().'(\''.$recupencore['link'].'\')" class="list-group-item" style="background-color:transparent;cursor:pointer;font-size : 0.9em;">'.$recupencore['title'].'</a></li>';
							}
						}
					}
				}
					
					$li .= '</ul>';
					$li .= '</div>';
					$li .= '<br />';
					
				}
			}
			
		}
		$_version = jeedom::versionAlias($_version);

		$replace = array(
			'#id#' => $this->getId(),
			'#name#' => ($this->getIsVisible()) ? $this->getName() : '<del>' . $this->getName() . '</del>',
			'#eqLink#' => $this->getLinkToConfiguration(),
			'#li#' => $li,
			'#height#' => $this->getDisplay('height', 'auto'),
            '#width#' => $this->getDisplay('width', $width),
            '#miniheight#' => $mini_height,
            '#miniwidth#' => $mini_widht
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
