<?php
/**
 * Class following Singleton pattern o create or update configuration items from
 * JSON files in resources folder
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 31 May 2017
 * @license AGPL-3.0
 */
class CRM_Basis_ConfigItems_ConfigItems {

  private static $_singleton;

  protected $_resourcesPath;
  protected $_customDataDir;

  /**
   * CRM_Basis_ConfigItems_ConfigItems constructor.
   */
  function __construct() {
    // Get the directory of the extension based on the name.
    $container = CRM_Extension_System::singleton()->getFullContainer();
    $resourcesPath = $container->getPath('be.mediwe.basis').'/CRM/Basis/ConfigItems/resources/';
    if (!is_dir($resourcesPath) || !file_exists($resourcesPath)) {
      throw new Exception(ts('Could not find the folder '.$resourcesPath
        .' which is required for extension be.mediwe.basis in '.__METHOD__
        .'.It does not exist or is not a folder, contact your system administrator'));
    }
    $this->_resourcesPath = $resourcesPath;

    $this->setContactTypes();
    $this->setRelationshipTypes();
    $this->setMembershipTypes();
    $this->setOptionGroups();
    
    $this->setActivityTypes();

    // cases after load activities
    $this->setCaseTypes();
      
    // customData as last one because it might need one of the previous ones (option group, relationship types, activity types)
    $this->setCustomData();
  }

  /**
   * Singleton method
   *
   * @return CRM_Basis_ConfigItems_ConfigItems
   * @access public
   * @static
   */
  public static function singleton() {
    if (!self::$_singleton) {
      self::$_singleton = new CRM_Basis_ConfigItems_ConfigItems();
    }
    return self::$_singleton;
  }

  /**
   * Method to create option groups
   *
   * @throws Exception when resource file not found
   * @access protected
   */
  protected function setOptionGroups() {
    $jsonFile = $this->_resourcesPath.'option_groups.json';
    if (!file_exists($jsonFile)) {
      throw new Exception(ts('Could not load option_groups configuration file for extension,
      contact your system administrator!'));
    }
    $optionGroupsJson = file_get_contents($jsonFile);
    $optionGroups = json_decode($optionGroupsJson, true);
    foreach ($optionGroups as $name => $optionGroupParams) {
      $optionGroup = new CRM_Basis_ConfigItems_OptionGroup();
      $optionGroup->create($optionGroupParams);
    }
  }

  /**
   * Method to create contact types
   *
   * @throws Exception when resource file not found
   * @access protected
   */
  protected function setContactTypes() {
    $jsonFile = $this->_resourcesPath.'contact_types.json';
    if (!file_exists($jsonFile)) {
      throw new Exception(ts('Could not load contact_types configuration file for extension,
      contact your system administrator!'));
    }
    $contactTypesJson = file_get_contents($jsonFile);
    $contactTypes = json_decode($contactTypesJson, true);
    foreach ($contactTypes as $name => $contactTypeParams) {
      $contactType = new CRM_Basis_ConfigItems_ContactType();
      $contactType->create($contactTypeParams);
    }
  }

    /**
     * Method to create relationship types
     *
     * @throws Exception when resource file not found
     * @access protected
     */
    protected function setRelationshipTypes() {
        $jsonFile = $this->_resourcesPath.'relationship_types.json';
        if (!file_exists($jsonFile)) {
            throw new Exception(ts('Could not load contact_types configuration file for extension,
      contact your system administrator!'));
        }
        $relationshipTypesJson = file_get_contents($jsonFile);
        $relationshipTypes = json_decode($relationshipTypesJson, true);
        $relationshipType = new CRM_Basis_ConfigItems_RelationshipType();

        foreach ($relationshipTypes as $name => $relationshipTypeParams) {
            $relationshipType->create($relationshipTypeParams);
        }

        $relationshipType->disableRelationshipType("Child of");
        $relationshipType->disableRelationshipType("Spouse of");
        $relationshipType->disableRelationshipType("Sibling of");
        $relationshipType->disableRelationshipType("Volunteer for");
        $relationshipType->disableRelationshipType("Head of Household for");
        $relationshipType->disableRelationshipType("Household Member of");
        $relationshipType->disableRelationshipType("Homeless Services Coordinator is");
        $relationshipType->disableRelationshipType("Health Services Coordinator is");
        $relationshipType->disableRelationshipType("Senior Services Coordinator is");
        $relationshipType->disableRelationshipType("Benefits Specialist is");
    }

    /**
     * Method to create activity types
     *
     * @throws Exception when resource file not found
     * @access protected
     */
    protected function setActivityTypes() {
        $jsonFile = $this->_resourcesPath.'activity_types.json';
        if (!file_exists($jsonFile)) {
            throw new Exception(ts('Could not load activity_types configuration file for extension,
      activity your system administrator!'));
        }
        $activityTypesJson = file_get_contents($jsonFile);
        $activityTypes = json_decode($activityTypesJson, true);
        foreach ($activityTypes as $name => $activityTypeParams) {
            $activityType = new CRM_Basis_ConfigItems_ActivityType();
            $activityType->create($activityTypeParams);
        }
    }

    /**
     * Method to create case types
     *
     * @throws Exception when resource file not found
     * @access protected
     */
    protected function setCaseTypes() {
        $jsonFile = $this->_resourcesPath.'case_types.json';
        if (!file_exists($jsonFile)) {
            throw new Exception(ts('Could not load case_types configuration file for extension,
      case your system administrator!'));
        }
        $caseTypesJson = file_get_contents($jsonFile);
        $caseTypes = json_decode($caseTypesJson, true);
        foreach ($caseTypes as $name => $caseTypeParams) {
            $caseType = new CRM_Basis_ConfigItems_CaseType();
            $caseType->create($caseTypeParams);
        }

        // workaround
        CRM_Core_DAO::executeQuery("INSERT INTO civicrm_case_type SELECT * FROM mediwe_case_type.civicrm_case_type;");
    }


    /**
     * Method to create membership types
     *
     * @throws Exception when resource file not found
     * @access protected
     */
    protected function setMembershipTypes() {
        $jsonFile = $this->_resourcesPath.'membership_types.json';
        if (!file_exists($jsonFile)) {
            throw new Exception(ts('Could not load contact_types configuration file for extension,
      contact your system administrator!'));
        }
        $membershipTypesJson = file_get_contents($jsonFile);
        $membershipTypes = json_decode($membershipTypesJson, true);
        foreach ($membershipTypes as $name => $membershipTypeParams) {
            $membershipType = new CRM_Basis_ConfigItems_MembershipType();
            $membershipType->create($membershipTypeParams);
        }
    }
    
  /**
   * Method to set the custom data groups and fields
   *
   * @throws Exception when config json could not be loaded
   * @access protected
   */
  protected function setCustomData() {
    // read all json files from custom_groups dir
    $customDataPath = $this->_resourcesPath.'custom_groups';
    if (file_exists($customDataPath) && is_dir($customDataPath)) {
      // get all json files from dir
      $jsonFiles = glob($customDataPath.DIRECTORY_SEPARATOR. "*.json");
      foreach ($jsonFiles as $customDataFile) {
        $customDataJson = file_get_contents($customDataFile);
        $customData = json_decode($customDataJson, true);
        foreach ($customData as $customGroupName => $customGroupData) {
          $customGroup = new CRM_Basis_ConfigItems_CustomGroup();
          $created = $customGroup->create($customGroupData);
          foreach ($customGroupData['fields'] as $customFieldName => $customFieldData) {
            $customFieldData['custom_group_id'] = $created['id'];
            $customField = new CRM_Basis_ConfigItems_CustomField();
            $customField->create($customFieldData);
          }
          // remove custom fields that are still on install but no longer in config
          CRM_Basis_ConfigItems_CustomField::removeUnwantedCustomFields($created['id'], $customGroupData);
        }
      }
    }
  }

  /**
   * Method to disable configuration items
   */
  public static function disable() {
    self::disableCustomData();
    self::disableOptionGroups();
    self::disableContactTypes();

  }

  /**
   * Method to enable configuration items
   */
  public static function enable() {
    self::enableCustomData();
    self::enableOptionGroups();
    self::enableContactTypes();

  }

  /**
   * Method to uninstall configuration items
   */
  public static function uninstall() {
    self::uninstallCustomData();
    self::uninstallOptionGroups();
    self::uninstallContactTypes();
  }

  /**
   * Method to uninstall custom data
   */
  private static function uninstallCustomData() {
    // read all json files from custom_groups dir
    $container = CRM_Extension_System::singleton()->getFullContainer();
    $customDataPath = $container->getPath('be.mediwe.basis').'/CRM/Basis/ConfigItems/resources/custom_groups';
    if (file_exists($customDataPath) && is_dir($customDataPath)) {
      // get all json files from dir
      $jsonFiles = glob($customDataPath.DIRECTORY_SEPARATOR. "*.json");
      foreach ($jsonFiles as $customDataFile) {
        $customDataJson = file_get_contents($customDataFile);
        $customData = json_decode($customDataJson, true);
        foreach ($customData as $customGroupName => $customGroupData) {
          $customGroup = new CRM_Basis_ConfigItems_CustomGroup();
          $customGroup->uninstall($customGroupName);
        }
      }
    }
  }

  /**
   * Method to enable custom data
   */
  private static function enableCustomData() {
    // read all json files from custom_groups dir
    $container = CRM_Extension_System::singleton()->getFullContainer();
    $customDataPath = $container->getPath('be.mediwe.basis').'/CRM/Basis/ConfigItems/resources/custom_groups';
    if (file_exists($customDataPath) && is_dir($customDataPath)) {
      // get all json files from dir
      $jsonFiles = glob($customDataPath.DIRECTORY_SEPARATOR. "*.json");
      foreach ($jsonFiles as $customDataFile) {
        $customDataJson = file_get_contents($customDataFile);
        $customData = json_decode($customDataJson, true);
        foreach ($customData as $customGroupName => $customGroupData) {
          $customGroup = new CRM_Basis_ConfigItems_OptionGroup();
          $customGroup->enable($customGroupName);
        }
      }
    }
  }

  /**
   * Method to disable custom data
   */
  private static function disableCustomData() {
    // read all json files from custom_groups dir
    $container = CRM_Extension_System::singleton()->getFullContainer();
    $customDataPath = $container->getPath('be.mediwe.basis').'/CRM/Basis/ConfigItems/resources/custom_groups';
    if (file_exists($customDataPath) && is_dir($customDataPath)) {
      // get all json files from dir
      $jsonFiles = glob($customDataPath.DIRECTORY_SEPARATOR. "*.json");
      foreach ($jsonFiles as $customDataFile) {
        $customDataJson = file_get_contents($customDataFile);
        $customData = json_decode($customDataJson, true);
        foreach ($customData as $customGroupName => $customGroupData) {
          $customGroup = new CRM_Basis_ConfigItems_CustomGroup();
          $customGroup->disable($customGroupName);
        }
      }
    }
  }

  /**
   * Method to disable option groups
   */
  private static function disableOptionGroups() {
    // read all json files from dir
    $container = CRM_Extension_System::singleton()->getFullContainer();
    $resourcePath = $container->getPath('be.mediwe.basis').'/CRM/Basis/ConfigItems/resources/';
    $jsonFile = $resourcePath.'option_groups.json';
    if (file_exists($jsonFile)) {
      $optionGroupsJson = file_get_contents($jsonFile);
      $optionGroups = json_decode($optionGroupsJson, true);
      foreach ($optionGroups as $name => $optionGroupParams) {
        $optionGroup = new CRM_Basis_ConfigItems_OptionGroup();
        $optionGroup->disable($name);
      }
    }
  }

  /**
   * Method to disable contact types
   */
  private static function disableContactTypes() {
    // read all json files from dir
    $container = CRM_Extension_System::singleton()->getFullContainer();
    $resourcePath = $container->getPath('be.mediwe.basis').'/CRM/Basis/ConfigItems/resources/';
    $jsonFile = $resourcePath.'contact_types.json';
    if (file_exists($jsonFile)) {
      $contactTypesJson = file_get_contents($jsonFile);
      $contactTypes = json_decode($contactTypesJson, true);
      foreach ($contactTypes as $name => $contactTypeParams) {
        $contactType = new CRM_Basis_ConfigItems_ContactType();
        $contactType->disableContactType($name);
      }
    }
  }

  /**
   * Method to enable contact types
   */
  private static function enableContactTypes() {
    // read all json files from dir
    $container = CRM_Extension_System::singleton()->getFullContainer();
    $resourcePath = $container->getPath('be.mediwe.basis').'/CRM/Basis/ConfigItems/resources/';
    $jsonFile = $resourcePath.'contact_types.json';
    if (file_exists($jsonFile)) {
      $contactTypesJson = file_get_contents($jsonFile);
      $contactTypes = json_decode($contactTypesJson, true);
      foreach ($contactTypes as $name => $contactTypeParams) {
        $contactType = new CRM_Basis_ConfigItems_ContactType();
        $contactType->enableContactType($name);
      }
    }
  }

  /**
   * Method to uninstall contact types
   */
  private static function uninstallContactTypes() {
    // read all json files from dir
    $container = CRM_Extension_System::singleton()->getFullContainer();
    $resourcePath = $container->getPath('be.mediwe.basis').'/CRM/Basis/ConfigItems/resources/';
    $jsonFile = $resourcePath.'contact_types.json';
    if (file_exists($jsonFile)) {
      $contactTypesJson = file_get_contents($jsonFile);
      $contactTypes = json_decode($contactTypesJson, true);
      foreach ($contactTypes as $name => $contactTypeParams) {
        $contactType = new CRM_Basis_ConfigItems_ContactType();
        $contactType->uninstallContactType($name);
      }
    }
  }

  /**
   * Method to enable option groups
   */
  private static function enableOptionGroups() {
    // read all json files from dir
    $container = CRM_Extension_System::singleton()->getFullContainer();
    $resourcePath = $container->getPath('be.mediwe.basis').'/CRM/Basis/ConfigItems/resources/';
    $jsonFile = $resourcePath.'option_groups.json';
    if (file_exists($jsonFile)) {
      $optionGroupsJson = file_get_contents($jsonFile);
      $optionGroups = json_decode($optionGroupsJson, true);
      foreach ($optionGroups as $name => $optionGroupParams) {
        $optionGroup = new CRM_Basis_ConfigItems_OptionGroup();
        $optionGroup->enable($name);
      }
    }
  }

  /**
   * Method to uninstall option groups
   */
  private static function uninstallOptionGroups() {
    // read all json files from dir
    $container = CRM_Extension_System::singleton()->getFullContainer();
    $resourcePath = $container->getPath('be.mediwe.basis').'/CRM/Basis/ConfigItems/resources/';
    $jsonFile = $resourcePath.'option_groups.json';
    if (file_exists($jsonFile)) {
      $optionGroupsJson = file_get_contents($jsonFile);
      $optionGroups = json_decode($optionGroupsJson, true);
      foreach ($optionGroups as $name => $optionGroupParams) {
        $optionGroup = new CRM_Basis_ConfigItems_OptionGroup();
        $optionGroup->uninstall($name);
      }
    }
  }
}