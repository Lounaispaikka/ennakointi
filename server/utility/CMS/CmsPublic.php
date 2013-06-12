<?php
namespace Lougis\utility;

require_once(PATH_SERVER.'abstracts/Utility.php');
require_once(PATH_SERVER.'utility/CMS/CmsYmparisto.php');
require_once(PATH_SERVER.'utility/CMS/CmsComment.php');
require_once(PATH_SERVER.'utility/CMS/CmsEnnakointi.php');
require_once(PATH_SERVER.'utility/UsersAndGroups/Permission.php');

/**
 * CmsPublic is used by public side scripts to output CMS content.
 */
class CmsPublic extends \Lougis\abstracts\Utility {

	private $Site;
	private $Lang;
	private $NavTree = null;

	public function __construct( $Site, $Lang ) {
		$this->Site = &$Site;
		$this->Lang = &$Lang;
	}
	
    //Funktiota muutettu
	//Tarkastaa käyttäjän oikeuden päästä sivulle
	//vg 3.4.2013
	public function showRequestPage() {
        
        global $Page;
        
        $Page = false;
		
		$requested_page = pg_escape_string($_REQUEST['p']);
		
		if ( $requested_page ) {
			//Hae onko sivu kielletty
			$RestrictedPage = new \Lougis_cms_page();
		/*	$RestrictedPage->query('SELECT id
									FROM lougis.cms_page
									WHERE restricted_access = true
									AND id = '.$_REQUEST['p'].';'
			);*/
			$RestrictedPage->restricted_access = true;
			$RestrictedPage->id = $requested_page;
			$RestrictedPage->find();
			/*while($RestrictedPage->fetch()) {
				$array[] = clone($RestrictedPage);
				devlog($array['id']);
			}*/
			
			//jos pääsy on rajoitettu
			if($RestrictedPage->fetch() ) {
		
				
				//Tarkista saako käyttäjä mennä sivulle
				$UGroups = new \Lougis_group_user();
				$UGroups->query('SELECT gu.group_id, gp.page_id
								FROM lougis.group_user AS gu
								JOIN lougis.group_permission AS gp
								ON gp.group_id = gu.group_id
								WHERE gu.user_id = '.$_SESSION['user_id'].'
								AND gp.page_id = '.$requested_page.';'
				);
				
				//jos oikeus sivuun
				if($UGroups->fetch() ) {
	
					$Page = $this->Site->getPageByName($_REQUEST['p'], $this->Lang->id);
				//jos ei oikeutta, anna error
				} else {
					require_once(PATH_404_FILE);
				}
			}
			
			//jos pääsy on vapaa
			else {
				$Page = $this->Site->getPageByName($_REQUEST['p'], $this->Lang->id);
			}
		}	
		else {
			$Page = $this->Site->getFirstPage($this->Lang->id);
        }
        
        switch(true) {
        	
        	case $Page !== false :
        		$this->outputPageHtml($Page);
        	break;
        	default:
        		require_once(PATH_404_FILE);
        	break;
        
        }
        
        
	}
	
	public function getPage() {
		
		global $Page;
		return $Page;
		
	}
	
	public function hasRightColumn() {
	
		global $Page;
		
		if ( $Page->hasNews() || $Page->hasColumnContent() ) return true;
		return false;
	
	}
	
	public function currentPageHasParent() {
	
		global $Page;
		
		if ( !empty($Page->parent_id) ) return $Page->parent_id;
		return false;
	
	}
	
	public function currentPageHasChildren( $Published = null ) {
	
		global $Page;
		
		$Search = new \Lougis_cms_page();
		$Search->parent_id = $Page->id;
    	if ( !empty($Publised) ) $Search->published = $Publised;
    	if ( $Search->count() > 0 ) return true;
    	return false;
    	
	
	}
	
	public function outputBreadcrumb() {
	
		global $Page;
		
		$ParentStack = $this->getCurrentPageParentStack();
		for($i=1; $i < count($ParentStack); $i++) {
			$Pg = $ParentStack[$i];
			echo '<a href="'.$Pg->getPageUrl().'">'.trim($Pg->title).'</a>';
			if ( $i < (count($ParentStack)-1) ) echo " | ";
		}
	
	}
	
	public function getCurrentPageParentStack( ) {
	
		global $Page;
		
		$Parents = array();
		$Parent = clone($Page);
		$Parents[] = $Parent;
		
		while( isset($Parent->parent_id) && !empty($Parent->parent_id) ) {
			$Parent = new \Lougis_cms_page($Parent->parent_id);
			$Parents[] = clone($Parent);
		}
		
		$Parents = array_reverse($Parents);
		return $Parents;
	
	}
	
	public function findCurrentPageTopParent( ) {
	
		global $Page;
		
		$navBranch = null;
		$navTree = $this->_navTreeData();
		foreach($navTree as $topLeaf) {
			if ( isset($topLeaf->children) && is_array($topLeaf->children) 
					&& count($topLeaf->children) > 0  && $this->_pageInBranch($topLeaf->children) ) {
					//echo "Pg: {$Page->id} / Leaf: {$topLeaf->id}<br/>";
					return $topLeaf;
			}
		}
		return $Page;
	
	}
	
	private function _pageInBranch( $Branch ) {
	
		global $Page;
		
		foreach($Branch as $Leaf) {
			if ( $Leaf->id == $Page->id ) return true;
			if ( isset($Leaf->children) && is_array($Leaf->children) && count($Leaf->children) > 0 ) {
				$InSubBranch = $this->_pageInBranch( $Leaf->children );
				if ( $InSubBranch ) return true;
			}
		}
		return false;
		
	}
	
	public function outputPageHtml( $Page ) {
	
		global $Site, $Page;
		
		if ( !empty($Page->template) ) {
			$Template = file_get_contents(PATH_TEMPLATE.$this->Site->id.'/page/'.$Page->template);
			$Template = str_replace('{PAGE_CONTENT}', $Page->getContentHtml(), $Template);
		} else {
			$Template = file_get_contents(PATH_TEMPLATE.$this->Site->id.'/'.$this->Site->default_template);
			$Template = str_replace('{PAGE_CONTENT}', $Page->getContentHtml(), $Template);
		}
		
		if ( $Site->id == 'ymparisto' ) {
			$CmsY = new \Lougis\utility\CmsYmparisto();
			$Template = $CmsY->processYmparistoTemplate( $Template );
		}
		
		echo eval('?>'.$Template.'<?');
	}
	
	public function ouputTopNavigation( ) {
		
		global $Page;
		
		$navTree = $this->_navTreeData();
		?>
		<ul>
		<? foreach($navTree as $topLeaf) { 
			if ( $topLeaf->visible == 't' ) {
		?>
			<li><a href="<?=$topLeaf->getPageUrl()?>"<?=( ( $topLeaf->id == $Page->id || $Page->hasParentPage( $topLeaf->id )) ? ' class="active"' : '' )?>><?=$topLeaf->nav_name?></a></li>
		<? 
			}
		} ?>
		</ul>
		<?
		
	}
	
	//Funktioon pitää muuttaa sivun näkyvyyden tarkistus
	//vg 3.4.2013
	public function outputLeftNavigation($Parent) {
		
		global $Page;
		
		$navBranch = null;
		$navTree = $this->_navTreeData();
		foreach($navTree as $topLeaf) {
			if ( $topLeaf->id == $Parent->id ) $navBranch = $topLeaf;
		}
		if ( isset($navBranch->children) && is_array($navBranch->children) && count($navBranch->children) > 0 ) {
		?>
		<ul>
			<? $this->_recurseOutputSubNavTree($navBranch->children) ?>
		</ul>
		<?
		}
		
	}
        
    public function outputChartNavigation() {
		
		global $Page;
		
		$navTree = $this->_chartTreeData();
		?>
		<ul>
		<? foreach($navTree as $topLeaf) { 
			
		?>
			<li><a href="/fi/37/?id=<?=$topLeaf->id?>"><?=$topLeaf->title?></a></li>
                       <? /* <li><a href="<?=$topLeaf->getPageUrl()?>"<?=( ( $topLeaf->id == $Page->id || $Page->hasParentPage( $topLeaf->id ) ) ? ' class="active"' : '' )?>><?=$topLeaf->title?></a></li> */?>
		<? 
			
		} ?>
		</ul>
	<?
	}
	
	// Phorum-kommenttien hakemiseen tarvittava thread_id
	public function getPhorumThreadId($Page_id) {
		
		$Thread_id = new \Public_lougis_phorum_page();
		$Thread_id->get('page_id', $Page_id);
		return $Thread_id->thread_id;
		
	}
	
	private function _recurseOutputSubNavTree($navBranch) {
		
		global $Page;
		foreach($navBranch as $leaf) {
			if ( $leaf->visible == 't' ) {
			?>
			<li><a href="<?=$leaf->getPageUrl()?>" <?=( ( $leaf->id == $Page->id ) ? ' class="active"' : '' )?> <? if($leaf->restricted_access == 't') {?>style="color:#a5a79b"<? }?>><?=$leaf->nav_name?></a>    
			<? 
			if ( isset($leaf->children) && is_array($leaf->children) && count($leaf->children) > 0 
					&& ( $Page->id == $leaf->id || $Page->hasParentPage($leaf->id) ) ) { 
			?>
			<ul><? $this->_recurseOutputSubNavTree($leaf->children) ?></ul>
			<? } ?>
			</li>
			<?
			}
		}
		
	}
	
	private function _recurseExtPageTree( $PageTree, $PageVisible = true ) {
		
		$Branch = array();
		foreach($PageTree as $Pg) {
			$Leaf = array(
				"text" => $Pg->nav_name,
				"page_id" => $Pg->id
			);
			if ( isset($Pg->children) ) {
				$Leaf["expanded"] = true;
				$Leaf["children"] = $this->_recurseExtPageTree( $Pg->children );
			} else {
				$Leaf["leaf"] = true;
			}
			array_push($Branch, $Leaf);
		}
		return $Branch;
	
	}
	
	private function _navTreeData() {
	
		if ( $this->navTree !== null ) return $this->navTree;
		
		$Pages = array();
		/*$Pg = new \Lougis_cms_page();
		$Pg->site_id = $this->Site->id;
		$Pg->restricted_access = false;
		$Pg->orderBy('seqnum ASC');
		$Pg->find();
		while( $Pg->fetch() ) {
			$Pages[$Pg->id] = clone($Pg);
		}*/
		$Pg = new \Lougis_cms_page();
		if(isset($_SESSION['user_id'])) {
		$Pg->query('
			select *
			from lougis.cms_page
			left join lougis.group_permission as gp2
			on gp2.page_id = lougis.cms_page."id"
			where restricted_access = FALSE
			union select *
			from lougis.cms_page as pg
			join lougis.group_permission as gp
			on pg."id" = gp.page_id
			where restricted_access = true
			and gp.group_id IN (select group_id from lougis.group_user where user_id = '.$_SESSION['user_id'].')
			order by seqnum asc
		;');
		}
		else {
		$Pg->query('
			select *
			from lougis.cms_page
			left join lougis.group_permission as gp2
			on gp2.page_id = lougis.cms_page."id"
			where restricted_access = FALSE
			order by seqnum asc
		;');
		}
		while( $Pg->fetch() ) {
			$Pages[$Pg->id] = clone($Pg);
		}
		//devlog($Pages);
		$this->navTree = $this->_recurseNavTreeData( $Pages );
		return $this->navTree;
		
	}
        
        private function _chartTreeData() {
	
		//if ( $this->navTree !== null ) return $this->navTree;
		
		$Pages = array();
		$Pg = new \Lougis_chart();
		$Pg->published = true;
                $Pg->orderBy('title ASC');
		$Pg->find();
		while( $Pg->fetch() ) {
			$Pages[$Pg->id] = clone($Pg);
		}
                $this->navTree = $Pages;
		return $this->navTree;
		
	}
	
	private function _recurseNavTreeData( $Pgs, $Parent = null ) {
		
		$Branch = array();
		foreach( $Pgs as $Pg ) {
			if ( empty($Parent) && empty($Pg->parent_id) ) {
				unset($Pgs[$Pg->id]);
				$Kids = $this->_recurseNavTreeData( $Pgs, $Pg->id );
				if ( $Kids ) $Pg->children = $Kids;
				array_push($Branch, $Pg);
			} elseif ( $Parent == $Pg->parent_id ) {
				unset($Pgs[$Pg->id]);
				$Kids = $this->_recurseNavTreeData( $Pgs, $Pg->id );
				if ( $Kids ) $Pg->children = $Kids;
				array_push($Branch, $Pg);
			} 
		}
		if ( count($Branch) < 1 ) return false;
		return $Branch;
		
	}
	
}
?>