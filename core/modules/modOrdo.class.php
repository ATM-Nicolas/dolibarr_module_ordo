<?php
/* Scrumboard for project's tasks
 * Copyright (C) 2013 ATM Consulting <support@atm-consulting.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
/**
 * 	\defgroup	scrumboard	scrumboard module
 * 	\brief		scrumboard module descriptor.
 * 	\file		core/modules/modscrumboard.class.php
 * 	\ingroup	scrumboard
 * 	\brief		Description and activation file for module scrumboard
 */
include_once DOL_DOCUMENT_ROOT . "/core/modules/DolibarrModules.class.php";
/**
 * Description and activation class for module scrumboard
 */
class modOrdo extends DolibarrModules
{
    /**
     * 	Constructor. Define names, constants, directories, boxes, permissions
     *
     * 	@param	DoliDB		$db	Database handler
     */
    public function __construct($db)
    {
        global $langs, $conf;
        $this->db = $db;
        // Id for module (must be unique).
        // Use a free id here
        // (See in Home -> System information -> Dolibarr for list of used modules id).
        $this->numero = 104211; // 104000 to 104999 for ATM CONSULTING
        // Key text used to identify module (for permissions, menus, etc...)
        $this->rights_class = 'ordo';
        // Family can be 'crm','financial','hr','projects','products','ecm','technic','other'
        // It is used to group modules in module setup page
        $this->family = "GPAO";
        // Module label (no space allowed)
        // used if translation string 'ModuleXXXName' not found
        // (where XXX is value of numeric property 'numero' of module)
        $this->name = preg_replace('/^mod/i', '', get_class($this));
        // Module description
        // used if translation string 'ModuleXXXDesc' not found
        // (where XXX is value of numeric property 'numero' of module)
        $this->description = "Description of module ordo";
        // Possible values for version are: 'development', 'experimental' or version
        $this->version = '2.2';
        // Key used in llx_const table to save module status enabled/disabled
        // (where MYMODULE is value of property name of module in uppercase)
        $this->const_name = 'MAIN_MODULE_' . strtoupper($this->name);
        // Where to store the module in setup page
        // (0=common,1=interface,2=others,3=very specific)
        $this->special = 0;
        // Name of image file used for this module.
        // If file is in theme/yourtheme/img directory under name object_pictovalue.png
        // use this->picto='pictovalue'
        // If file is in module/img directory under name object_pictovalue.png
        // use this->picto='pictovalue@module'
        $this->picto = 'ordo@ordo'; // mypicto@ordo
        // Defined all module parts (triggers, login, substitutions, menus, css, etc...)
        // for default path (eg: /ordo/core/xxxxx) (0=disable, 1=enable)
        // for specific path of parts (eg: /ordo/core/modules/barcode)
        // for specific css file (eg: /ordo/css/scrumboard.css.php)
        $this->module_parts = array(
            // Set this to 1 if module has its own trigger directory
            'triggers' => 1,
            // Set this to 1 if module has its own login method directory
            //'login' => 0,
            // Set this to 1 if module has its own substitution function file
            //'substitutions' => 0,
            // Set this to 1 if module has its own menus handler directory
            //'menus' => 0,
            // Set this to 1 if module has its own barcode directory
            //'barcode' => 0,
            // Set this to 1 if module has its own models directory
            //'models' => 0,
            // Set this to relative path of css if module has its own css file
            //'css' => '/ordo/css/mycss.css.php',
            // Set here all hooks context managed by module
            'hooks' => array('ordercard','propalcard','projectcard','actioncard')
            // Set here all workflow context managed by module
            //'workflow' => array('order' => array('WORKFLOW_ORDER_AUTOCREATE_INVOICE'))
        );
        // Data directories to create when module is enabled.
        // Example: this->dirs = array("/ordo/temp");
        $this->dirs = array();
        // Config pages. Put here list of php pages
        // stored into scrumboard/admin directory, used to setup module.
        $this->config_page_url = 'ordo_setup.php@ordo';
        // Dependencies
        // List of modules id that must be enabled if this module is enabled
        $this->depends = array();
        // List of modules id to disable if this one is disabled
        $this->requiredby = array();
        // Minimum version of PHP required by module
        $this->phpmin = array(5, 3);
        // Minimum version of Dolibarr required by module
        $this->need_dolibarr_version = array(3, 2);
        $this->langfiles = array("ordo@ordo"); // langfiles@ordo
        // Constants
        // List of particular constants to add when module is enabled
        // (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
        // Example:
        $this->const = array(
       		array('SCRUM_DEFAULT_VELOCITY','chaine', 7,'Vélocité par défaut d\'un projet',0)
            ,array('SCRUM_GROUP_TASK_BY_PRODUCT_TOLERANCE','chaine', 4,'tolérance de groupage des tâches par produit',0)
     	);
        // Array to add new pages in new tabs
        // Example:
        $this->tabs = array(
            
        );
        // where objecttype can be
        // 'thirdparty'			to add a tab in third party view
        // 'intervention'		to add a tab in intervention view
        // 'order_supplier'		to add a tab in supplier order view
        // 'invoice_supplier'	to add a tab in supplier invoice view
        // 'invoice'			to add a tab in customer invoice view
        // 'order'				to add a tab in customer order view
        // 'product'			to add a tab in product view
        // 'stock'				to add a tab in stock view
        // 'propal'				to add a tab in propal view
        // 'member'				to add a tab in fundation member view
        // 'contract'			to add a tab in contract view
        // 'user'				to add a tab in user view
        // 'group'				to add a tab in group view
        // 'contact'			to add a tab in contact view
        // 'categories_x'		to add a tab in category view
        // (replace 'x' by type of category (0=product, 1=supplier, 2=customer, 3=member)
        // Dictionnaries
        if (! isset($conf->ordo->enabled)) {
            $conf->ordo=new stdClass();
            $conf->ordo->enabled = 0;
        }
        $this->dictionnaries = array();
        /* Example:
          // This is to avoid warnings
          if (! isset($conf->scrumboard->enabled)) $conf->scrumboard->enabled=0;
          $this->dictionnaries=array(
          'langs'=>'ordo@ordo',
          // List of tables we want to see into dictonnary editor
          'tabname'=>array(
          MAIN_DB_PREFIX."table1",
          MAIN_DB_PREFIX."table2",
          MAIN_DB_PREFIX."table3"
          ),
          // Label of tables
          'tablib'=>array("Table1","Table2","Table3"),
          // Request to select fields
          'tabsql'=>array(
          'SELECT f.rowid as rowid, f.code, f.la			$this->date_lancement = strtotime('+'.$delai.' day midnight');
		 * bel, f.active'
          . ' FROM ' . MAIN_DB_PREFIX . 'table1 as f',
          'SELECT f.rowid as rowid, f.code, f.label, f.active'
          . ' FROM ' . MAIN_DB_PREFIX . 'table2 as f',
          'SELECT f.rowid as rowid, f.code, f.label, f.active'
          . ' FROM ' . MAIN_DB_PREFIX . 'table3 as f'
          ),
          // Sort order
          'tabsqlsort'=>array("label ASC","label ASC","label ASC"),
          // List of fields (result of select to show dictionnary)
          'tabfield'=>array("code,label","code,label","code,label"),
          // List of fields (list of fields to edit a record)
          'tabfieldvalue'=>array("code,label","code,label","code,label"),
          // List of fields (list of fields for insert)
          'tabfieldinsert'=>array("code,label","code,label","code,label"),
          // Name of columns with primary key (try to always name it 'rowid')
          'tabrowid'=>array("rowid","rowid","rowid"),
          // Condition to show each dictionnary
          'tabcond'=>array(
          $conf->scrumboard->enabled,
          $conf->scrumboard->enabled,
          $conf->scrumboard->enabled
          )
          );
         */
        // Boxes
        // Add here list of php file(s) stored in core/boxes that contains class to show a box.
        $this->boxes = array(); // Boxes list
        /*
          $this->boxes[$r][1] = "myboxb.php";
          $r++;
         */
        // Permissions
        $this->rights = array(); // Permission array used by this module
        
        $r = $this->numero;
        $this->rights[]=array(
            0=>$this->numero . $r
            ,1=>'UseOrdonnancement'
            ,3=>0
            ,4=>'ordo'
        );
        
        $r++;
        // Add here list of permission defined by
        // an id, a label, a boolean and two constant strings.
        // Example:
        //// Permission id (must not be already used)
        //$this->rights[$r][0] = 2000;
        //// Permission label
        //$this->rights[$r][1] = 'Permision label';
        //// Permission by default for new user (0/1)
        //$this->rights[$r][3] = 1;
        //// In php code, permission will be checked by test
        //// if ($user->rights->permkey->level1->level2)
        //$this->rights[$r][4] = 'level1';			$this->date_lancement = strtotime('+'.$delai.' day midnight');
        
        //// In php code, permission will be checked by test
        //// if ($user->rights->permkey->level1->level2)
        //$this->rights[$r][5] = 'level2';
        //$r++;
        // Main menu entries
        $this->menus = array(); // List of menus to add
        $r = 0;
       
      $this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=project',			                // Put 0 if this is a top menu
								'type'=>'left',			                // This is a Top menu entry
								'titre'=>'Ordonnancement',
								'mainmenu'=>'project',
								'leftmenu'=>'grid',
								'url'=>'/ordo/grid.php',
								'langs'=>'ordo@ordo',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
								'position'=>100,
								'perms'=>'$user->rights->ordo->ordo',			                // Use 'perms'=>'$user->rights->report->level1->level2' if you want your menu with a permission rules
								'target'=>'',
								'user'=>2);				                // 0=Menu for internal users, 1=external users, 2=both
		$r++;
	   
       $this->menu[$r]=array(   'fk_menu'=>'fk_mainmenu=of',     // Use r=value where r is index key used for the parent menu entry (higher parent must be a top menu entry)
            'type'=>'left',         // This is a Left menu entry
            'titre'=>'Ordonnancement',
            'mainmenu'=>'of',
            'leftmenu'=>'ordoGPAO',
            'url'=>'/ordo/grid.php',
            'position'=>300,
            'perms'=>'$user->rights->ordo->ordo',
            'lang'=>'ordo@ordo',
            'target'=>'',
            'user'=>2);
        $r++;
       
      $this->menu[$r]=array(   'fk_menu'=>'fk_mainmenu=of,fk_leftmenu=ordoGPAO',     // Use r=value where r is index key used for the parent menu entry (higher parent must be a top menu entry)
            'type'=>'left',         // This is a Left menu entry
            'titre'=>'OrdonnancementStat',
            'mainmenu'=>'ordoGPAO',
            'leftmenu'=>'ordoStat',
            'url'=>'/ordo/grid-stat.php',
            'position'=>300,
            'perms'=>'$user->rights->ordo->ordo',
            'lang'=>'ordo@ordo',
            'target'=>'',
            'user'=>2);
        $r++;
		
	$this->menu[$r]=array(	'fk_menu'=>'fk_mainmenu=project,fk_leftmenu=Scrumboard',			                // Put 0 if this is a top menu
							'type'=>'left',			                // This is a Top menu entry
							'titre'=>'Projets par Chef de projet',
							'mainmenu'=>'Scrumboard',
							'leftmenu'=>'grid',
							'url'=>'/ordo/nb_proj_cdp.php',
							'langs'=>'ordo@ordo',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
								'position'=>110,
								'perms'=>'$user->rights->ordo->ordo',			                // Use 'perms'=>'$user->rights->report->level1->level2' if you want your menu with a permission rules
								'target'=>'',
								'user'=>2);				                // 0=Menu for internal users, 1=external users, 2=both
		$r++;
       
	   
        // Exports
        $r = 1;
        // Example:
        //$this->export_code[$r]=$this->rights_class.'_'.$r;
        //// Translation key (used only if key ExportDataset_xxx_z not found)
        //$this->export_label[$r]='CustomersInvoicesAndInvoiceLines';
        //// Condition to show export in list (ie: '$user->id==3').
        //// Set to 1 to always show when module is enabled.
        //$this->export_enabled[$r]='1';
        //$this->export_permission[$r]=array(array("facture","facture","export"));
        //$this->export_fields_array[$r]=array(
        //	's.rowid'=>"IdCompany",
        //	's.nom'=>'CompanyName',
        //	's.address'=>'Address',
        //	's.cp'=>'Zip',
        //	's.ville'=>'Town',
        //	's.fk_pays'=>'Country',
        //	's.tel'=>'Phone',
        //	's.siren'=>'ProfId1',
        //	's.siret'=>'ProfId2',
        //	's.ape'=>'ProfId3',
        //	's.idprof4'=>'ProfId4',
        //	's.code_compta'=>'CustomerAccountancyCode',
        //	's.code_compta_fournisseur'=>'SupplierAccountancyCode',
        //	'f.rowid'=>"InvoiceId",
        //	'f.facnumber'=>"InvoiceRef",
        //	'f.datec'=>"InvoiceDateCreation",
        //	'f.datef'=>"DateInvoice",
        //	'f.total'=>"TotalHT",
        //	'f.total_ttc'=>"TotalTTC",
        //	'f.tva'=>"TotalVAT",
        //	'f.paye'=>"InvoicePaid",
        //	'f.fk_statut'=>'InvoiceStatus',
        //	'f.note'=>"InvoiceNote",
        //	'fd.rowid'=>'LineId',
        //	'fd.description'=>"LineDescription",
        //	'fd.price'=>"LineUnitPrice",
        //	'fd.tva_tx'=>"LineVATRate",
        //	'fd.qty'=>"LineQty",
        //	'fd.total_ht'=>"LineTotalHT",
        //	'fd.total_tva'=>"LineTotalTVA",
        //	'fd.total_ttc'=>"LineTotalTTC",
        //	'fd.date_start'=>"DateStart",
        //	'fd.date_end'=>"DateEnd",
        //	'fd.fk_product'=>'ProductId',
        //	'p.ref'=>'ProductRef'
        //);
        //$this->export_entities_array[$r]=array('s.rowid'=>"company",
        //	's.nom'=>'company',
        //	's.address'=>'company',
        //	's.cp'=>'company',
        //	's.ville'=>'company',
        //	's.fk_pays'=>'company',
        //	's.tel'=>'company',
        //	's.siren'=>'company',
        //	's.siret'=>'company',
        //	's.ape'=>'company',
        //	's.idprof4'=>'company',
        //	's.code_compta'=>'company',
        //	's.code_compta_fournisseur'=>'company',
        //	'f.rowid'=>"invoice",
        //	'f.facnumber'=>"invoice",
        //	'f.datec'=>"invoice",
        //	'f.datef'=>"invoice",
        //	'f.total'=>"invoice",
        //	'f.total_ttc'=>"invoice",
        //	'f.tva'=>"invoice",
        //	'f.paye'=>"invoice",
        //	'f.fk_statut'=>'invoice',
        //	'f.note'=>"invoice",
        //	'fd.rowid'=>'invoice_line',
        //	'fd.description'=>"invoice_line",
        //	'fd.price'=>"invoice_line",
        //	'fd.total_ht'=>"invoice_line",
        //	'fd.total_tva'=>"invoice_line",
        //	'fd.total_ttc'=>"invoice_line",
        //	'fd.tva_tx'=>"invoice_line",
        //	'fd.qty'=>"invoice_line",
        //	'fd.date_start'=>"invoice_line",
        //	'fd.date_end'=>"invoice_line",
        //	'fd.fk_product'=>'product',
        //	'p.ref'=>'product'
        //);
        //$this->export_sql_start[$r] = 'SELECT DISTINCT ';
        //$this->export_sql_end[$r] = ' FROM (' . MAIN_DB_PREFIX . 'facture as f, '
        //	. MAIN_DB_PREFIX . 'facturedet as fd, ' . MAIN_DB_PREFIX . 'societe as s)';
        //$this->export_sql_end[$r] .= ' LEFT JOIN ' . MAIN_DB_PREFIX
        //	. 'product as p on (fd.fk_product = p.rowid)';
        //$this->export_sql_end[$r] .= ' WHERE f.fk_soc = s.rowid '
        //	. 'AND f.rowid = fd.fk_facture';
        //$r++;
    }
    /**
     * Function called when module is enabled.
     * The init function add constants, boxes, permissions and menus
     * (defined in constructor) into Dolibarr database.
     * It also creates data directories
     *
     * 	@param		string	$options	Options when enabling module ('', 'noboxes')
     * 	@return		int					1 if OK, 0 if KO
     */
    public function init($options = '')
    {
    	global $db;
	
	    $sql = array();
        $result = $this->loadTables();
		
		$db->query("ALTER TABLE `".MAIN_DB_PREFIX."projet_task` 
					ADD `grid_col` FLOAT NOT NULL DEFAULT '0',
					ADD `grid_row` FLOAT NOT NULL DEFAULT '999999'");		
	
		$db->query("ALTER TABLE `".MAIN_DB_PREFIX."projet_task`
					ADD grid_height FLOAT NOT NULL DEFAULT 0");		
		
		$db->query('ALTER TABLE `'.MAIN_DB_PREFIX.'projet_task`
				ADD INDEX `grid_row_grid_col` (`grid_row`, `grid_col`)');		
	
		$db->query('ALTER TABLE '.MAIN_DB_PREFIX.'projet_task
				ADD INDEX progress (progress)');
				
		$db->query('ALTER TABLE '.MAIN_DB_PREFIX.'projet_task
				ADD INDEX planned_workload (planned_workload)');		
	
			$db->query("ALTER TABLE ".MAIN_DB_PREFIX."projet_task
				ADD date_estimated_start DATETIME NOT NULL 
			  , ADD date_estimated_end DATETIME NOT NULL 
			  , ADD INDEX (date_estimated_start, date_estimated_end)");
	
		dol_include_once('/core/class/extrafields.class.php');
        $extrafields=new ExtraFields($this->db);
        $res = $extrafields->addExtraField('color', 'Couleur du projet', 'varchar', 1, 8, 'projet', false, false, '');
		
        $extrafields=new ExtraFields($this->db);
		$res = $extrafields->addExtraField('grid_use', 'Afficher sur la grille de planning', 'boolean', 0, '', 'projet_task');
        $extrafields=new ExtraFields($this->db);
		$res = $extrafields->addExtraField('needed_ressource', 'nb ressources nécessaires', 'int', 0, '', 'projet_task');
		$extrafields=new ExtraFields($this->db);
		$res = $extrafields->addExtraField('fk_workstation', 'Poste de charge', 'sellist', 0, '', 'projet_task',0,0,'',serialize(array('options'=>array('workstation:name:rowid'=>null))));

     	$extrafields=new ExtraFields($this->db);
        $res = $extrafields->addExtraField('fk_of', 'Ordre de Fabrication', 'sellist', 0, '', 'projet_task',0,0,'',serialize(array('options'=>array('assetOf:numero:rowid'=>null))));
        $extrafields=new ExtraFields($this->db);
        $res = $extrafields->addExtraField('fk_product', 'Produit à fabriquer', 'sellist', 0, '', 'projet_task',0,0,'',serialize(array('options'=>array('product:label:rowid'=>null))));

		$extrafields=new ExtraFields($this->db);
        $res = $extrafields->addExtraField('fk_workstation', 'Poste de charge immobilisé', 'sellist', 0, '', 'actioncomm',0,0,'',serialize(array('options'=>array('workstation:name:rowid'=>null))));
     	$extrafields=new ExtraFields($this->db);
        $res = $extrafields->addExtraField('needed_ressource', 'nb ressources immobilisées', 'int', 0, '', 'actioncomm');
		
		return $this->_init($sql, $options);
    }
    /**
     * Function called when module is disabled.
     * Remove from database constants, boxes and permissions from Dolibarr database.
     * Data directories are not deleted
     *
     * 	@param		string	$options	Options when enabling module ('', 'noboxes')
     * 	@return		int					1 if OK, 0 if KO
     */
    public function remove($options = '')
    {
        $sql = array();
        return $this->_remove($sql, $options);
    }
    /**
     * Create tables, keys and data required by module
     * Files llx_table1.sql, llx_table1.key.sql llx_data.sql with create table, create keys
     * and create data commands must be stored in directory /ordo/sql/
     * This function is called by this->init
     *
     * 	@return		int		<=0 if KO, >0 if OK
     */
    private function loadTables()
    {
        return $this->_load_tables('/ordo/sql/');
    }
}